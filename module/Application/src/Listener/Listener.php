<?php

namespace Application\Listener;

use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\JsonModel;

class Listener extends AbstractListenerAggregate
{

    /**
     * Attach one or more listeners
     *
     * Implementors may add an optional $priority argument; the EventManager
     * implementation will pass this to the aggregate.
     *
     * @param EventManagerInterface $events
     * @param int $priority
     * @return void
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        // Registr the method which will be triggered on error
        $this->listeners[] = $events->attach(
            MvcEvent::EVENT_DISPATCH_ERROR,
            [$this, 'handleError'],
            0
        );
    }

    /**
     * @param MvcEvent $e
     */
    public function handleError(MvcEvent $e)
    {
        $request = $e->getParam('application')->getRequest();

        if ($request instanceof ConsoleRequest) {
            return;
        }

        $response = $e->getResponse();
        if ($response instanceof \Zend\Console\Response) {
            return;
        }
        $exception = $e->getResult()->exception;


        $errorType = $e->getError();
        if ($errorType == 'error-router-no-match') {
            $errorCode = $exception && $exception->getCode() ? $exception->getCode() : 404;
            $json = new JsonModel(['message' => 'URL is incorrect']);

            $json->setTerminal(true);
            $response->setStatusCode($errorCode);
            $e->setResult($json);
            $e->setViewModel($json);
        }
    }
}
