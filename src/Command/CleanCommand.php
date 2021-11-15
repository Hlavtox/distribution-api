<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class CleanCommand extends Command
{
    private const ACCEPTED_DIRECTORIES = ['all', 'json', 'modules', 'tmp'];

    protected static $defaultName = 'clean';

    private Filesystem $filesystem;
    private string $moduleDir;
    private string $jsonDir;
    private string $tmpDir;

    public function __construct(
        string $moduleDir = __DIR__ . '/../../public/modules',
        string $jsonDir = __DIR__ . '/../../public/json',
        string $tmpDir = __DIR__ . '/../../var/tmp'
    ) {
        parent::__construct();
        $this->filesystem = new Filesystem();
        $this->moduleDir = $moduleDir;
        $this->jsonDir = $jsonDir;
        $this->tmpDir = $tmpDir;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $directory = $input->getArgument('directory');
        if (!in_array($directory, self::ACCEPTED_DIRECTORIES)) {
            $output->writeln(
                sprintf('<error>Directory should be one of this: %s</error>', join(', ', self::ACCEPTED_DIRECTORIES))
            );

            return static::FAILURE;
        }

        switch ($directory) {
            case 'all':
                $this->cleanAll($output);
                break;
            case 'modules':
                $this->cleanModules($output);
                break;
            case 'json':
                $this->cleanJson($output);
                break;
            case 'tmp':
                $this->cleanTmp($output);
                break;
        }

        return static::SUCCESS;
    }

    protected function configure(): void
    {
        $this->addArgument('directory', InputArgument::REQUIRED, 'Directory to clean');
    }

    private function cleanAll(OutputInterface $output): void
    {
        $this->cleanJson($output);
        $this->cleanModules($output);
        $this->cleanTmp($output);
    }

    private function cleanJson(OutputInterface $output): void
    {
        $this->filesystem->remove((new Finder())->in($this->jsonDir));
        $output->writeln('<info>Json folder cleaned</info>');
    }

    private function cleanModules(OutputInterface $output): void
    {
        $this->filesystem->remove((new Finder())->in($this->moduleDir));
        $output->writeln('<info>Modules folder cleaned</info>');
    }

    private function cleanTmp(OutputInterface $output): void
    {
        $this->filesystem->remove((new Finder())->in($this->tmpDir));
        $output->writeln('<info>Tmp folder cleaned</info>');
    }
}