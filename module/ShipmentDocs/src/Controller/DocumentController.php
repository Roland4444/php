<?php

namespace ShipmentDocs\Controller;

use Application\Form\Filter\DateElement;
use Application\Form\Filter\FilterableController;
use Application\Form\Filter\SubmitElement;
use Core\Traits\RestMethods;
use Dompdf\Dompdf;
use ShipmentDocs\Exception\ServiceException;
use Zend\Http\Response;
use Zend\View\Model\ViewModel;

class DocumentController extends AbstractCrudController
{
    use RestMethods;
    use FilterableController;

    protected string $indexRoute = 'shipmentDocs/document';
    protected $url = 'document';

    public function indexAction(): ViewModel
    {
        $form = $this->filterForm($this->getRequest(), 'shipmentDocs_document');
        $params = $form->getFilterParams('shipmentDocs_document');
        try {
            $shipmentDocs = $this->service->apiAction('GET', $this->url, [
                'query' => [
                    'startdate' => $params['startdate'],
                    'enddate' => $params['enddate']
                ]
            ]);
            return new ViewModel([
                'permissions' => $this->getPermissions(),
                'shipmentDocs' => json_decode($shipmentDocs, true),
                'form' => $form->getForm($params),
            ]);
        } catch (ServiceException $e) {
            return new ViewModel([
                'form' => $form->getForm($params),
                'error' => 'Произошла ошибка: ' . $e->getMessage()
            ]);
        }
    }

    public function addAction(): ViewModel
    {
        $drivers = $this->service->apiAction('GET', 'driver');
        $owners = $this->service->apiAction('GET', 'owner');
        $receivers = $this->service->apiAction('GET', 'receiver');

        return new ViewModel([
            'drivers' => $drivers,
            'owners' => $owners,
            'receivers' => $receivers,
        ]);
    }

    public function editAction(): ViewModel
    {
        $docId = $this->params()->fromRoute('id');

        $drivers = $this->service->apiAction('GET', 'driver');
        $owners = $this->service->apiAction('GET', 'owner');
        $receivers = $this->service->apiAction('GET', 'receiver');
        $shipmentDoc = $this->service->apiAction('GET', $this->url . '/' . $docId);

        return new ViewModel([
            'drivers' => $drivers,
            'owners' => $owners,
            'receivers' => $receivers,
            'shipmentDoc' => $shipmentDoc
        ]);
    }

    public function saveAction(): Response
    {
        return $this->handleRestResponse(function () {
            $content = $this->getRequest()->getContent();
            $data = json_decode($content, true);

            return $this->service->apiAction('POST', $this->url, [
                'json' => [
                    'driver' => $data['driverId'],
                    'owner' => $data['ownerId'],
                    'vehicle' => json_decode($data['vehicle'], true),
                    'receiver' => $data['receiverId'],
                    'payer' => $data['payerId'],
                    'container' => json_decode($data['container'], true),
                    'letterOfAuthorityNumber' => $data['letterOfAuthorityNumber'],
                    'packingListNumber' => $data['packingListNumber'],
                    'explosiveDocNumber' => $data['explosiveDocNumber'],
                    'radiationDocNumber' => $data['radiationDocNumber'],
                    'transportWaybillNumber' => $data['transportWaybillNumber'],
                    'id' => $data['id'],
                    'date' => $data['date']
                ]
            ]);
        });
    }

    public function pdfPackingListAction()
    {
        try {
            $docId = $this->params()->fromRoute('id');
            $response = $this->service->apiAction('GET', $this->url . '/packing-list/' . $docId);
            $this->renderPdf($response);
        } catch (ServiceException $e) {
            return $this->responseError($e->getMessage());
        }
    }

    public function pdfTransportWaybillAction()
    {
        try {
            $docId = $this->params()->fromRoute('id');
            $response = $this->service->apiAction('GET', $this->url . '/waybill/' . $docId);
            $this->renderPdf($response, 'portrait');
        } catch (ServiceException $e) {
            return $this->responseError('Произошла ошибка: ' . $e->getMessage());
        }
    }

    public function pdfPackingTransportAction()
    {
        try {
            $docId = $this->params()->fromRoute('id');
            $response = $this->service->apiAction('GET', $this->url . '/packing-transport-document/' . $docId);
            $this->renderPdf($response);
        } catch (ServiceException $e) {
            return $this->responseError('Произошла ошибка: ' . $e->getMessage());
        }
    }

    public function pdfLetterOfAuthorityAction()
    {
        try {
            $docId = $this->params()->fromRoute('id');

            $html = $this->service->apiAction('GET', $this->url . '/letter-of-authority/' . $docId);
            $this->renderPdf($html, 'portrait');
        } catch (ServiceException $e) {
            return $this->responseError('Произошла ошибка: ' . $e->getMessage());
        }
    }

    public function pdfExplosiveRadiationCertificateAction()
    {
        try {
            $docId = $this->params()->fromRoute('id');

            $html = $this->service->apiAction('GET', $this->url . '/explosive-radiation-certificate/' . $docId);
            $this->renderPdf($html, 'portrait');
        } catch (ServiceException $e) {
            return $this->responseError('Произошла ошибка: ' . $e->getMessage());
        }
    }

    private function renderPdf($html, $orientation = 'landscape')
    {
        $domPDF = new Dompdf();
        $domPDF->setPaper('A4', $orientation);
        $domPDF->loadHtml($html, 'UTF-8');
        $domPDF->render();
        $domPDF->stream("document.pdf", ["Attachment" => 0]);
        exit();
    }

    protected function getFilterForm()
    {
        return new SubmitElement(new DateElement());
    }

    public function notAvailableAction()
    {
        //todo Похоже метод никогда не выполняется т к обработка выше
        $message = $this->params()->fromQuery('msg');
        return new ViewModel(['message' => $message]);
    }
}
