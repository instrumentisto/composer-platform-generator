<?php

namespace Instrumentisto\Composer\Tests;

use Composer\Factory;
use Composer\Repository\PlatformRepository;

use Instrumentisto\Composer\Command\UpdatePlatformReqsCommand;

use PHPUnit\Framework\TestCase;


/**
 * Contains unit tests for Command/UpdatePlatformReqsCommand class
 */
class PlatformReqsTest extends TestCase
{
    private static $extension;

    public static function setUpBeforeClass() {
        self::$extension = (new UpdatePlatformReqsCommand)->
                                    getPlatformReqs(new PlatformRepository());
    }

    public function testComposerFile() {
        $this->assertFileExists(Factory::getComposerFile());
    }

    public function testComposerPluginApiVersion() {
        $this->assertArrayHasKey('composer-plugin-api', self::$extension);
        $this->assertTrue(version_compare('1.1',
                        self::$extension['composer-plugin-api'], '<='));
    }

    public function testPhpVersion() {
        $this->assertArrayHasKey('php', self::$extension);
        $this->assertEquals(phpversion(), self::$extension['php']);
    }

    public function testJsonVersion() {
        $this->assertArrayHasKey('ext-json', self::$extension);
        $this->assertEquals(phpversion('json'), self::$extension['ext-json']);
    }

    public function testPcreVersion() {
        $this->assertArrayHasKey('lib-pcre', self::$extension);
        $this->assertEquals(explode(' ',PCRE_VERSION)[0],
                                                self::$extension['lib-pcre']);
        $this->assertArrayHasKey('ext-pcre', self::$extension);
        $this->assertEquals(phpversion('pcre'), self::$extension['ext-pcre']);
    }
}
