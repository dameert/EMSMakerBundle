<?php

namespace EMS\MakerBundle\Command;

use EMS\MakerBundle\Service\FileService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MakeCommand extends Command
{
    /** @var FileService */
    protected $fileService;
    /** @var SymfonyStyle */
    protected $io;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
        parent::__construct();
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
    }
}
