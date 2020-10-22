<?php

namespace Factoring;

use Factoring\Plugin\FactoringTotalFactory;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getControllerPluginConfig()
    {
        return [
            'factories' => [
                'factoringTotal' => FactoringTotalFactory::class,
            ],
        ];
    }
}
