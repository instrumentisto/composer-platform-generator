<?php

namespace Instrumentisto\Composer\Command;


use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Composer\Command\BaseCommand;
use Composer\Factory;
use Composer\Json\JsonManipulator;
use Composer\Repository\PlatformRepository;


/**
 * Generate platform requirements
 */
class UpdatePlatformReqsCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('update-platform-reqs')
            ->setDescription('Update platform requirements in composer.json')
            ->setDefinition(array(
                new InputOption('output-console', null,
                    InputOption::VALUE_NONE, '
                    Don\'t update composer.json, only output to console')
            ))
            ->setHelp(
                <<<EOT
Generate platform requirements and build structure for config.platform options
EOT
            );

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
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

        if ($input->getOption('output-console')) {
            $output->writeln(json_encode($extensions, JSON_PRETTY_PRINT));
        } else {
            file_put_contents(Factory::getComposerFile(),
                                            $manipulator->getContents());
            $output->writeln('Updated config.platform');
        }
    }
}
