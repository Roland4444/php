<?php

namespace ShipmentDocs;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Zend\Mvc\MvcEvent;

class Module
{
    public function onBootstrap(MvcEvent $e): void
    {
        $e->getApplication()
            ->getEventManager()
            ->attach(MvcEvent::EVENT_DISPATCH_ERROR, [$this, 'catchError']);
    }
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ],
            ],
        ];
    }

    public function catchError(MvcEvent $event): void
    {
        $exception = $event->getParam('exception');
        if (($exception instanceof ConnectException) || ($exception instanceof RequestException)) {
            $this->redirectToNotAvailable($event);
        }
    }

    private function redirectToNotAvailable(MvcEvent $e): void
    {
        $url = $e->getRouter()->assemble([], ['name' => 'shipment_docs_not_available']);
        $response = $e->getResponse();
        $message = $e->getParam('exception')->getMessage();
        $response->setHeaders($response->getHeaders()->addHeaderLine('Location', $url . '?msg=' . $message));
        $response->setStatusCode(302);
        $response->sendHeaders();
        exit;
    }
}
