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
 * Composer command that generates platform requirements in config.platform
 * section of composer.json file.
 */
class UpdatePlatformReqsCommand extends BaseCommand
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('update-platform-reqs')
            ->setDescription('Updates config.platform requirements '.
                             'in composer.json')
            ->setDefinition([
                new InputOption('output-console', null,
                    InputOption::VALUE_NONE,
                    'Do not update composer.json, but output to STDOUT'),
            ])
            ->setHelp('Generates requirements and build structure for '.
                      'config.platform section of composer.json file.');
    }

    /**
     * Get current platform requirements
     *
     * @param PlatformRepository $repository    PlatformRepository object
     *
     * @return array
     */
    public function getPlatformReqs(PlatformRepository $repository) {
        $extensions = [];
        foreach ($repository->getPackages() as $package) {
            $n = $package->getPrettyName();
            if (!preg_match(PlatformRepository::PLATFORM_PACKAGE_REGEX, $n)) {
                continue;
            }
            $extensions[strtolower($n)] = $package->getPrettyVersion();
        }
        ksort($extensions);
        return $extensions;
    }

    /**
     * Executes the current command by wiping the previous config.platform
     * section (if any) and filling it with current platform environment
     * (in alphabetic order).
     *
     * @param InputInterface $input    Input context of the command.
     * @param OutputInterface $output  Output result of the command.
     *
     * @return int|null  null or 0 if everything went fine, or an error code.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $content = file_get_contents(Factory::getComposerFile());
        $manipulator = new JsonManipulator($content);
        $manipulator->removeSubNode('config', 'platform');

        $extensions = $this->getPlatformReqs(new PlatformRepository());

        foreach ($extensions as $name => $version) {
            $manipulator->addSubNode('config', 'platform.'.$name, $version);
        }

        if ($input->getOption('output-console')) {
            $json = json_encode($extensions, JSON_PRETTY_PRINT);
            $output->writeln((string)$json);
        } else {
            file_put_contents(Factory::getComposerFile(),
                              $manipulator->getContents());
            $output->writeln('Updated config.platform');
        }
        return 0;
    }
}
