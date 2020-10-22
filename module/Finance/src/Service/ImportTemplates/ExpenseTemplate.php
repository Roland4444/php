<?php

namespace Finance\Service\ImportTemplates;

use Finance\Entity\Order;
use Finance\Service\TemplateService;
use Reference\Service\CategoryService;

class ExpenseTemplate implements Template
{
    private $order;
    private $category;
    private $defaultCategory;
    private $templateArray;

    public function __construct(CategoryService $categoryService, TemplateService $templateService)
    {
        $this->defaultCategory = $categoryService->findDefaultByOption();
        $templates = $templateService->findAll();
        $this->templateArray = [];
        foreach ($templates as $template) {
            $this->templateArray[$template->getInn()][] = $template;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function equals(Order $order)
    {
        $this->order = $order;
        $type = $order->getType();
        $source = $order->getSource();
        $dest = $order->getDest();

        if (in_array($type, [1, 4]) && ! empty($source) && empty($dest)) {
            return true;
        } elseif ($type == 17 && $source) {
            if (strpos(strtolower($order->getComment()), 'Списание средств') === false) {
                return true;
            }
        }
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getMessage()
    {
        $msg = 'Расход';
        if ($this->category) {
            $msg .= ' ' . $this->category->getName();
        }
        return $msg . ' ' . $this->order->getMoney();
    }

    public function getParams(): array
    {
        $templates = $this->templateArray[$this->order->getDestInn()] ?? [];

        $template = null;
        foreach ($templates as $item) {
            if (stripos($this->order->getComment(), $item->getText()) !== false) {
                $template = $item;
            }
        }
        $category = $template ? $template->getCategory() : $this->defaultCategory;
        $this->category = $category;
        return ['category' => $category];
    }
}
