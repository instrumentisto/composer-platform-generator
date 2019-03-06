<?php

namespace Instrumentisto\Composer\Tests;

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

    public function testPhpVersion() {
        $this->assertArrayHasKey('php', self::$extension);
        $this->assertEquals(PHP_VERSION, self::$extension['php']);
    }

    public function testJsonVersion() {
        $this->assertArrayHasKey('ext-json', self::$extension);
        $this->assertEquals(phpversion('json'), self::$extension['ext-json']);
    }

    public function testPcreLibVersion() {
        $this->assertArrayHasKey('lib-pcre', self::$extension);
        $this->assertEquals(explode(' ',PCRE_VERSION)[0],
                                                self::$extension['lib-pcre']);
    }

    public function testPcreExtVersion() {
        if (version_compare(PHP_VERSION, '5', '<=')) {
            $this->assertEquals("0", self::$extension['ext-pcre']);
        }
        if (version_compare(PHP_VERSION, '7', '>=')) {
            $this->assertEquals(PHP_VERSION, self::$extension['ext-pcre']);
        }
    }
}
