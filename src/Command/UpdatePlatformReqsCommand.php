<?php

namespace Instrumentisto\Composer\Command;

use Composer\Factory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Command\BaseCommand;

use Composer\Json\JsonManipulator;
use Composer\Repository\PlatformRepository;


class UpdatePlatformReqsCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('update-platform-reqs')
            ->setDescription('Update platform requirements in composer.json');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Updating config.platform');

        $content = file_get_contents(Factory::getComposerFile());
        $manipulator = (new JsonManipulator($content));
        $manipulator->removeSubNode('config', 'platform');

        $extensions = [];
        foreach ((new PlatformRepository())->getPackages() as $package) {
            $name = $package->getPrettyName();
            if (!preg_match(PlatformRepository::PLATFORM_PACKAGE_REGEX, $name)) {
                continue;
            }
            $extensions[strtolower($name)] = $package->getPrettyVersion();
        }

        ksort($extensions);
        foreach ($extensions as $name => $version) {
            $manipulator->addSubNode('config', 'platform.'.$name, $version);
        }
        file_put_contents(Factory::getComposerFile(), $manipulator->getContents());

    }

}