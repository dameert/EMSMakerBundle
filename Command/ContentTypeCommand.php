<?php

namespace EMS\MakerBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use EMS\CoreBundle\Service\EnvironmentService;
use EMS\CoreBundle\Entity\Environment;
use EMS\CoreBundle\Service\ContentTypeService;
use EMS\CoreBundle\Entity\ContentType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use EMS\MakerBundle\Service\CommandService;

class ContentTypeCommand extends Command
{
    protected static $defaultName = 'ems:make:contenttype';
  
    /**
     * create a contenttype based on a selection (input) in the Resource/make/contenttype folder.
     * --help should include the list of files in that folder
     *
     * Pass the desired contenttypes to create to the command (or --all ?)
     * Pass the desired default environment, ask to create if the environment does not exist
     *
     * If the command tries to create a contenttype that links to a contenttype that does not exist, ask interactively if we can
     * create the missing contenttype(s) first. (ask for each one separatly) --> if response is NO, we should still create content type
     */
 
    /**
     * @var EnvironmentService
     */
    protected $environmentService;
    
    /**
     * @var ContentTypeService
     */
    protected $contentTypeService;
    
    /**
     * @var CommandService
     */
    protected $commandService;
    
    public function __construct(EnvironmentService $environmentService, ContentTypeService $contentTypeService, CommandService $commandService)
    {
        $this->environmentService = $environmentService;
        $this->contentTypeService = $contentTypeService;
        $this->commandService = $commandService;
        parent::__construct();
    }
    
    
    protected function configure()
    {
        parent::configure();
        $this->addArgument('contenttypes', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, sprintf('one/more of content types list [%s]', implode(', ', array_keys($this->commandService->getDemoFiles($this->getDefaultName())))))
        ->addOption('environment', null, InputOption::VALUE_REQUIRED, 'Environment default (preview)', 'preview')
        ->addOption('all', null, InputOption::VALUE_NONE, 'create all demo content types');
        /* @TODO add option for group(folder)  */
    }
    
    public function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var array $givenTypes */
        $givenTypes = $input->getArgument('contenttypes');
        /** @var string $givenEnv */
        $givenEnv = $input->getOption('environment');
        $all = $input->getOption('all');

        /** @var EMS\CoreBundle\Entity\Environment $environment */
        $environment = $this->environmentService->getByName($givenEnv);
        if ($environment == false) {
            //TODO test If env not exist propose created?
            $output->writeln('Environment ' . $givenEnv . ' not exist');
            return null;
        }
        
        if ($all and (count($givenTypes) > 0)) {
            $helper = $this->getHelper('question');
            $question = new ConfirmationQuestion(sprintf('Preferes you create all content types ? (yes = all, no = only [%s]) ', implode(', ', $givenTypes)), false);
            if (!$helper->ask($input, $output, $question)) {
                $all = false;
            }
        }
        
        if ($all || (!$all && count($givenTypes) == 0)) {
            $givenTypes = array_keys($this->commandService->getDemoFiles($this->getDefaultName()));
        }

        foreach ($givenTypes as $type) {
            /* @TODO Review $jsonFile must be an array => foreach for $jsonFile */
            $values = $this->commandService->getDemoFiles($this->getDefaultName());
            /** @var UploadedFile $jsonFile */
            $jsonFile = $this->commandService->getDemoFile($values[$type], $this->getDefaultName());
            /** @var ContentType $contentType */
            $contentType = $this->contentTypeService->initFromJson($jsonFile, $environment);
            $contentType = $this->contentTypeService->persistAsNew($contentType);
            $output->writeln('content Type' . $contentType->getName() . ' has been created');
        }
    }
}
