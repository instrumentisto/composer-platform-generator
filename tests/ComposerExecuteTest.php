<?php

namespace Instrumentisto\Composer\Tests;

use Composer\Console;
use Composer\Factory;

use Symfony\Component\Console\Input;
use Symfony\Component\Console\Output;

use Instrumentisto\Composer\Plugin;

use PHPUnit\Framework\TestCase;


/**
 * End-to-End tests.
 */
class ComposerExecuteTest extends TestCase
{
    /** @var $app Console\Application */
    private $app;

    /** @var $output Output\NullOutput */
    private $output;

    protected function setUp()
    {
        $this->app = new Console\Application();
        $this->app->setAutoExit(false);
        $this->app->getComposer()->getPluginManager()->addPlugin(new Plugin());
        $this->output = new Output\NullOutput();
    }

    public function testUpdatePlatformReqsCommand() {
        $input = new Input\ArrayInput(['command' => 'update-platform-reqs']);
        $this->app->run($input,$this->output);

        $composerConfigJson = file_get_contents(Factory::getComposerFile());
        $this->assertJson($composerConfigJson);

        $platform = json_decode($composerConfigJson,true)['config']['platform'];
        $this->assertNotEmpty($platform);
        $this->assertGreaterThan( 2, count($platform));

        $extensions = array_reduce(
            array_keys($platform),
            function ($m, $str) {
                if (preg_match('/ext-(.*)/', $str, $matches)) {
                    $m[] = $matches[1];
                }
                return $m;
            },
            []
        );
        $this->assertContains('json',$extensions);
        $this->assertContains('pcre',$extensions);

        $currentPlatformExt = array_map(
            function($s) {
                return strtolower(str_replace(" ","-",$s));
            },
            get_loaded_extensions()
        );
        foreach ($extensions as $ext) {
            $this->assertContains($ext, $currentPlatformExt);
        }
    }

    protected function tearDown()
    {
        $input = new Input\ArrayInput(
            ['command' => 'update-platform-reqs', '--clear' => ' ']
        );
        $this->app->run($input, $this->output);
        $this->app = null;
        $this->output = null;
    }
}
