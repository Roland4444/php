<?php

namespace Application\Log;

use Zend\Log\Logger;
use Doctrine\DBAL\Logging\DebugStack;

class SqlLogger extends DebugStack
{
    protected $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function stopQuery()
    {
        parent::stopQuery();

        $q = $this->queries[$this->currentQuery];
        $message = "Executed Query " . count($this->queries) . ': ' . print_r($q, true);
        $this->logger->info($message);
    }
}
