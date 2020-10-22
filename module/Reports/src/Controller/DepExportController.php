<?php

namespace Reports\Controller;

use Core\Traits\RestMethods;
use Zend\Http\Response;
use Reference\Service\DepartmentService;
use Reports\Form\DepExportFilter;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\I18n\View\Helper\NumberFormat;

class DepExportController extends AbstractActionController
{
    use RestMethods;

    protected $serviceLocator;
    protected $remoteSkladService;
    protected $departmentService;
    protected $config;

    public function __construct($container)
    {
        $this->serviceLocator = $container;
        $this->remoteSkladService = $container->get('RemoteSkladService');
        $this->departmentService = $container->get(DepartmentService::class);
        $this->config = $this->serviceLocator->get('ApplicationConfig');
    }

    /**
     * Возвращает форму фильтра
     *
     * @return DepExportFilter
     */
    protected function getFilterForm()
    {
        return new DepExportFilter($this->serviceLocator, $this->getRequest()->getPost());
    }

    public function indexAction()
    {
        $departments = $this->departmentService->getListForJson();

        return new ViewModel([
            'departments' => json_encode($departments),
            'accessDelete' => $this->hasAccess(static::class, 'delete') ? 'true' : 'false'
        ]);
    }

    public function listAction()
    {
        $requestData = $this->getRequest();

        $data = [
            'startdate' => $requestData->getQuery('startdate'),
            'enddate' => $requestData->getQuery('enddate'),
            'comment' => $requestData->getQuery('comment'),
            'type' => $requestData->getQuery('type'),
            'department' => $requestData->getQuery('department'),
        ];

        $filterForm = $this->getFilterForm();
        $filterForm->setData($data);

        if (! $filterForm->isValid()) {
            return $this->responseError("Не корректно заполнены поля фильтра", Response::STATUS_CODE_400);
        }

        $this->remoteSkladService->setParams($data);
        $rows = $this->remoteSkladService->findAll();
        $totalWeight = $this->remoteSkladService->getTotalWeight();
        $avgSor = $this->remoteSkladService->getAvgSor();
        $numberFormat = new NumberFormat();
        $res = [];
        foreach ($rows as $row) {
            //Рефакторинг все что до этой даты лежит по другому пути
            $dayX = new \DateTime('2018-05-23');
            $reportDay = new \DateTime($row->getDate()->format('Y-m-d'));
            if ($reportDay < $dayX) {
                $path = join('/', [
                    $this->config['images_url'],
                    $row->getPath(),
                    $row->getDate()->format('Y-m-d') . '_' . $this->translate($row->getSklad()) . $row->getReportId()
                ]);
            } else {
                $path = join('/', [
                    $this->config['images_url'],
                    'photo',
                    $row->getDate()->format('Y-m-d'),
                    $this->translate($row->getSklad()) . $row->getReportId()
                ]);
            }
            $res[] = [
                'date' => $row->getDate()->format('d.m.Y'),
                'time' => $row->getTime(),
                'department' => $row->getSklad(),
                'reportid' => $row->getReportId(),
                'id' => $row->getId(),
                'sor' => $row->getSor(),
                'massa' => $numberFormat($row->getMassa(), \NumberFormatter::DECIMAL, \NumberFormatter::TYPE_DEFAULT, "ru_RU"),
                'brute' => $numberFormat($row->getBrute(), \NumberFormatter::DECIMAL, \NumberFormatter::TYPE_DEFAULT, "ru_RU"),
                'tare' => $numberFormat($row->getTare(), \NumberFormatter::DECIMAL, \NumberFormatter::TYPE_DEFAULT, "ru_RU"),
                'netto' => $numberFormat($row->getNetto(), \NumberFormatter::DECIMAL, \NumberFormatter::TYPE_DEFAULT, "ru_RU"),
                'transnumb' => $row->getTransnumb(),
                'trash' => $row->getPrimesi(),
                'waybill' => $row->getNaklnumb(),
                'path' => $path
            ];
        }
        return $this->responseSuccess([
            'rows' => $res,
            'totalSor' => empty($avgSor) ? '0' : $numberFormat($avgSor, \NumberFormatter::DECIMAL, \NumberFormatter::TYPE_DEFAULT, "ru_RU"),
            'totalWeight' => empty($totalWeight) ? '0' : $numberFormat($totalWeight, \NumberFormatter::DECIMAL, \NumberFormatter::TYPE_DEFAULT, "ru_RU"),
        ]);
    }

    private function translate($text)
    {
        $converter = [
            'а' => 'a', 'б' => 'b', 'в' => 'v',
            'г' => 'g', 'д' => 'd', 'е' => 'e',
            'ё' => 'e', 'ж' => 'zh', 'з' => 'z',
            'и' => 'i', 'й' => 'y', 'к' => 'k',
            'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r',
            'с' => 's', 'т' => 't', 'у' => 'u',
            'ф' => 'f', 'х' => 'h', 'ц' => 'c',
            'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch',
            'ь' => '', 'ы' => 'y', 'ъ' => '',
            'э' => 'e', 'ю' => 'yu', 'я' => 'ya',

            'А' => 'A', 'Б' => 'B', 'В' => 'V',
            'Г' => 'G', 'Д' => 'D', 'Е' => 'E',
            'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z',
            'И' => 'I', 'Й' => 'Y', 'К' => 'K',
            'Л' => 'L', 'М' => 'M', 'Н' => 'N',
            'О' => 'O', 'П' => 'P', 'Р' => 'R',
            'С' => 'S', 'Т' => 'T', 'У' => 'U',
            'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
            'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sch',
            'Ь' => '', 'Ы' => 'Y', 'Ъ' => '',
            'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',
        ];
        return strtr($text, $converter);
    }

    public function getNumbersAction()
    {
        $params = $this->params()->fromQuery();
        $response = $this->getResponse();
        $arr = $this->remoteSkladService->findNumbersByTemplate($params['q']);
        $response->setContent(Json::encode($arr));
        return $response;
    }

    public function deleteAction()
    {
        $id = $this->getEvent()->getRouteMatch()->getParam('id', null);
        $this->remoteSkladService->remove($id);
        $this->redirect()->toRoute('reportDepExport');
    }
}
