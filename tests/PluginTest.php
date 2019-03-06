<?php

namespace Instrumentisto\Composer\Tests;

use Instrumentisto\Composer\Plugin;
use Instrumentisto\Composer\Command\UpdatePlatformReqsCommand;

use PHPUnit\Framework\TestCase;


/**
 * Contains unit tests for Plugin class
 */
class PluginTest extends TestCase
{
    public function testAutoload() {
        $this->assertTrue(class_exists(Plugin::class));
        $this->assertTrue(class_exists(UpdatePlatformReqsCommand::class));
    }
}
