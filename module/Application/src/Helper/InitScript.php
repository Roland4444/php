<?php

namespace Application\Helper;

use Zend\View\Helper\AbstractHelper;

class InitScript extends AbstractHelper
{
    public function __invoke(string $scriptName)
    {
        $fileName = str_replace(['/', '-'], '_', $scriptName);
        $this->view->headScript()->appendFile('/js/' . $fileName . '.js', 'text/javascript');
    }
}
