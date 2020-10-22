<?php

namespace Spare\Controller;

use Application\Form\Filter\FilterableController;
use Application\Form\Filter\NameElement;
use Application\Form\Filter\SubmitElement;
use Zend\View\Model\ViewModel;

class TotalController extends SpecifiedSpareController
{
    use FilterableController;

    /**
     * Index action
     * @return ViewModel
     * @throws \Exception
     */
    public function indexAction()
    {
        $filterForm = $this->filterForm($this->getRequest(), 'spareTotal');
        $params = $filterForm->getFilterParams('spareTotal');

        $data = $this->services['spareTotalService']->getTotals($this->getWarehouseId());
        usort($data, function ($a, $b) {
            return strnatcmp($a['text'], $b['text']);
        });
        if (! empty($params['name'])) {
            $data = array_filter($data, function ($item) use ($params) {
                return mb_strpos(mb_strtolower($item['text']), mb_strtolower($params['name'])) !== false;
            });
        }

        return new ViewModel([
            'totals' => $data,
            'form' => $filterForm->getForm($params),
            'warehouseId' => $this->getWarehouseId()
        ]);
    }

    /**
     * Возвращает фильтр для indexAction
     *
     * @param array
     * @return SubmitElement
     */
    protected function getFilterForm()
    {
        return new SubmitElement(new NameElement($this->entityManager));
    }
}
