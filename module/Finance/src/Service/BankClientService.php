<?php

namespace Finance\Service;

use Application\Exception\ServiceException;
use Exception;
use Finance\Service\ImportTemplates\CashTemplate;
use Finance\Service\ImportTemplates\ExpenseTemplate;
use Finance\Service\ImportTemplates\MetalExpenseTemplate;
use Finance\Service\ImportTemplates\OverdraftExpenseTemplate;
use Finance\Service\ImportTemplates\ReceiptFactoringTemplate;
use Finance\Service\ImportTemplates\ReceiptOtherTemplate;
use Finance\Service\ImportTemplates\ReceiptTraderTemplate;
use Finance\Service\ImportTemplates\TransferTemplate;
use Finance\Entity\Order;
use Zend\Filter\Decompress;

class BankClientService
{
    private $services;

    public function __construct(array $services)
    {
        $this->services = $services;
    }

    /**
     * Читать загруженный файл
     * @param $filesArray
     * @return array
     * @throws ServiceException
     */
    public function readFromRequest($filesArray)
    {
        ini_set('memory_limit', '32M');
        $maxsize = "100000000";
        $extensions = ["zip", "txt"];
        $size = filesize($filesArray['file']['tmp_name']);
        $filename = $filesArray['file']['name'];
        $type = strtolower(substr($filename, 1 + strrpos($filename, ".")));
        $new_name = "bk.zip";
        if ($size > $maxsize) {
            throw new ServiceException("Файл больше 100 мб. Уменьшите размер вашего файла или загрузите другой.");
        }
        if (! in_array($type, $extensions)) {
            throw new ServiceException('Файл имеет недопустимое расширение.');
        }

        $file = $filesArray['file']['tmp_name'];
        if ($type == 'zip') {
            if (copy($file, __DIR__ . "/" . $new_name)) {
                echo "Файл загружен!";
            } else {
                echo "Файл НЕ был загружен.";
            }

            $filter = new Decompress([
                'adapter' => 'Zip',
                'options' => [
                    'archive' => $file,
                    'target' => 'bk',
                ]
            ]);
            $filter->filter($file);

            $data = $this->fromFile('bk/KL_TO_1C.txt');
        } else {
            $data = $this->fromFile($file);
        }
        return $data;
    }

    /**
     *  Save payments from bank to database.
     *
     * @param array $data
     * @param bool $fullAccess
     * @return array
     * @throws Exception
     */
    public function savePayments(array $data, $fullAccess = false): array
    {
        //todo надо переделать все new на DI
        $templates = [
            [
                'template' => new ReceiptTraderTemplate($this->services['traderService']),
                'service' => $this->services['traderReceiptService']
            ],
            [
                'template' => new MetalExpenseTemplate($this->services['customerService']),
                'service' => $this->services['metalExpenseService']
            ],
            [
                'template' => new ExpenseTemplate($this->services['categoryService'], $this->services['templateService']),
                'service' => $this->services['otherExpenseService']
            ],
            [
                'template' => new CashTemplate($this->services['bankAccountService']),
                'service' => $this->services['cashTransferService']
            ],
            [
                'template' => new TransferTemplate(),
                'service' => $this->services['cashTransferService']
            ],
            [
                'template' => new ReceiptFactoringTemplate($this->services['traderService']),
                'service' => $this->services['factoring']
            ],
            [
                'template' => new ReceiptOtherTemplate(),
                'service' => $this->services['otherReceiptService']
            ],
            [
                'template' => new OverdraftExpenseTemplate($this->services['categoryService']),
                'service' => $this->services['otherExpenseService']
            ],
        ];

        $messages = [];

        foreach ($data as $order) {
            if (! $fullAccess && ! $this->services['dateService']->checkDateInRangeMonth(new \DateTime($order->getDate()))) {
                break;
            }

            foreach ($templates as $template) {
                if ($template['template']->equals($order)) {
                    $params = $template['template']->getParams();
                    if ($params) {
                        $template['service']->saveFromOrder($order, $params);
                    } else {
                        $template['service']->saveFromOrder($order);
                    }
                    $messages[] = $template['template']->getMessage();
                    break;
                }
            }
        }
        return $messages;
    }

    private function fromFile($file)
    {
        $data = $this->fileToArray($file);
        $result = [];

        $cashBankAccount = $this->services['bankAccountService']->findCash();
        $diamondBankAccount = $this->services['bankAccountService']->findDiamond();

        $bankAccounts = $this->services['bankAccountService']->findAll();
        $bankAccountArray = [];
        foreach ($bankAccounts as $bankAccount) {
            $bankAccountArray[$bankAccount->getName()] = $bankAccount;
        }

        foreach ($data as $item) {
            $order = new Order();
            $order->setComment($item['НазначениеПлатежа']);
            $date = (isset($item['ДатаСписано']) && ! empty(trim($item['ДатаСписано']))) ? $item['ДатаСписано'] : $item['ДатаПоступило'];
            $order->setDate($date);
            $destAccount = trim($item['ПолучательСчет']);
            if (trim($destAccount) == '40817810005000000000') {
                $dest = $cashBankAccount;
            } elseif (trim($destAccount) == '30233810600002100082'/*алмаз*/ || trim($destAccount) == '30233810600002100257'/*ек*/) {
                //todo Надо вынести куда то в настройки или лучше прописывать номер счета в названии счета
                $dest = $diamondBankAccount;
            } else {
                $dest = $bankAccountArray[$destAccount] ?? null;
            }

            $order->setDest($dest);
            $account = isset($item['ПлательщикРасчСчет']) ? $item['ПлательщикРасчСчет'] : $item['ПлательщикСчет'];
            $source = $bankAccountArray[trim($account)] ?? null;
            $order->setSource($source);
            $order->setDestInn($item['ПолучательИНН']);
            $order->setSourceInn($item['ПлательщикИНН']);
            $order->setMoney(trim($item['Сумма']));
            $order->setNumber($item['Номер']);
            $recipient = ! empty($item['Получатель1']) ? $item['Получатель1'] : $item['Получатель'];
            $order->setRecipient($recipient);
            $order->setSender($item['Плательщик'] ?? $item['Плательщик1']);
            $order->setType(trim($item['ВидОплаты']));

            $result[] = $order;
        }
        return $result;
    }


    private function fileToArray($file)
    {
        $data = file($file);

        $result = [];
        $sectionOpened = false;

        foreach ($data as $line) {
            $str = mb_convert_encoding($line, "UTF-8", "Windows-1251");
            if (substr($str, 0, strlen('СекцияДокумент')) == 'СекцияДокумент') {
                $sectionOpened = true;
                $section = [];
            }
            if (substr($str, 0, strlen('КонецДокумента')) == 'КонецДокумента') {
                $result[] = $section;
                $sectionOpened = false;
            }
            if ($sectionOpened) {
                list($key, $value) = explode("=", $str);

                $section[$key] = $value;
            }
        }

        return $result;
    }
}
