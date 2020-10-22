<?php

namespace FactoringTest;

use Factoring\Module;
use PHPUnit\Framework\TestCase;

class ModuleTest extends TestCase
{
    /**
     * @var Module;
     *
     */
    private $module;

    public function setUp(): void
    {
        $this->module = new Module();
    }

    public function testGetConfig()
    {
        $config = $this->module->getConfig();
        $this->assertIsArray($config);
    }

    public function testFetControllerPluginConfig()
    {
        $plugins = $this->module->getControllerPluginConfig();
        $this->assertIsArray($plugins);
    }
}
