<?php

namespace EMS\MakerBundle\Command;

use EMS\CoreBundle\Entity\ContentType;
use EMS\CoreBundle\Entity\Environment;
use EMS\CoreBundle\Service\EnvironmentService;
use EMS\CoreBundle\Service\ContentTypeService;
use EMS\MakerBundle\Service\FileService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

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
 
    /** @var EnvironmentService */
    protected $environmentService;
    /** @var ContentTypeService */
    protected $contentTypeService;
    /** @var FileService */
    protected $fileService;
    
    public function __construct(EnvironmentService $environmentService, ContentTypeService $contentTypeService, FileService $fileService)
    {
        $this->environmentService = $environmentService;
        $this->contentTypeService = $contentTypeService;
        $this->fileService = $fileService;
        parent::__construct();
    }
    
    
    protected function configure()
    {
        parent::configure();
        $fileNames = implode(', ', $this->fileService->getFileNames(FileService::TYPE_CONTENTTYPE));
        $this
            ->addArgument(
                'contenttypes',
                InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
                sprintf('Optional array of contenttypes to create. Allowed values: [%s]', $fileNames)
            )
            ->addOption(
                'environment',
                null,
                InputOption::VALUE_REQUIRED,
                'Default environment for the contenttypes',
                'preview'
            )
            ->addOption(
                'all',
                null,
                InputOption::VALUE_NONE,
                sprintf('Make all contenttypes: [%s]', $fileNames)
            )
        ;
    }
    
    public function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var array $givenTypes */
        $givenTypes = $input->getArgument('contenttypes');
        /** @var string $givenEnv */
        $givenEnv = $input->getOption('environment');
        $all = $input->getOption('all');

        /** @var Environment $environment */
        $environment = $this->environmentService->getByName($givenEnv);
        if ($environment == false) {
            //TODO test If env not exist propose created?
            $output->writeln('Environment ' . $givenEnv . ' does not exist');
            return null;
        }
        
        if ($all and (count($givenTypes) > 0)) {
            $helper = $this->getHelper('question');
            $question = new ConfirmationQuestion(sprintf('Preferes you create all content types ? (yes = all, no = only [%s]) ', implode(', ', $givenTypes)), false);
            if (!$helper->ask($input, $output, $question)) {
                $all = false;
            }
        }

        $allTypes = $this->fileService->getFileNames(FileService::TYPE_CONTENTTYPE);
        if ($all) {
            $givenTypes = $allTypes;
        }

        if (!$all && count($givenTypes) == 0) {
            $output->writeln('Pass at least one contenttype, or the option --all');
            return null;
        }

        foreach ($givenTypes as $type) {
            /* @TODO Review $json must be an array => foreach for $jsonFile */
            /** @var string|null $json */
            $json = $this->fileService->getFileContentsByFileName($type, FileService::TYPE_CONTENTTYPE);
            if ($json === null) {
                $output->writeln(sprintf('Skipped %s, because no file was found with that name', $type));
                continue;
            }

            try{
                /** @var ContentType $contentType */
                $contentType = $this->contentTypeService->contentTypeFromJson($json, $environment);
                $contentType = $this->contentTypeService->importContentType($contentType);
            } catch (\Exception $e) {
                $output->writeln($e->getMessage());
                continue;
            }
            $output->writeln(sprintf('Contenttype %s has been created', $contentType->getName()));
        }
    }
}
