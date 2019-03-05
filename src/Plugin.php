<?php

namespace Instrumentisto\Composer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\Capability\CommandProvider;
use Composer\Plugin\Capable;
use Composer\Plugin\PluginInterface;


/**
 * Composer plugin implementation for 'update-platform-reqs' Composer command.
 */
class Plugin implements PluginInterface, Capable, CommandProvider
{
    /**
     * Does nothing, as this plugin doesn't modify Composer anyhow.
     *
     * @param Composer $composer  Not used.
     * @param IOInterface $io     Not used.
     */
    public function activate(Composer $composer, IOInterface $io)
    {
    }

    /**
     * Announces this plugin's commands.
     *
     * @return array|string[]  The only class name of
     *                         'update-platform-reqs' command.
     */
    public function getCapabilities()
    {
        return [
            CommandProvider::class => static::class,
        ];
    }

    /**
     * Returns this plugin's commands.
     *
     * @return array|\Composer\Command\BaseCommand[]
     *                                  The only 'update-platform-reqs' command.
     */
    public function getCommands()
    {
        return [new Command\UpdatePlatformReqsCommand()];
    }
}
