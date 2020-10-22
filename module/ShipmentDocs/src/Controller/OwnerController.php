<?php

namespace ShipmentDocs\Controller;

use Zend\Http\Response;

class OwnerController extends AbstractCrudController
{
    protected string $indexRoute = 'shipmentDocs/owner';
    protected string $url = 'owner';

    public function getAction(): Response
    {
        return $this->handleRestResponse(function () {
            $id = $this->params()->fromRoute('id');
            return $this->service->apiAction('GET', $this->url . '/' . $id);
        });
    }

    public function listAction(): Response
    {
        return $this->handleRestResponse(function () {
            return json_decode($this->service->apiAction('GET', $this->url), true);
        });
    }

    public function deleteAction(): Response
    {
        return $this->handleRestResponse(function () {
            $id = $this->params()->fromRoute('id');
            return $this->service->apiAction('DELETE', $this->url . '/' . $id);
        });
    }
}
