<?php

namespace EMS\MakerBundle\Command;

use EMS\CoreBundle\Entity\ContentType;
use EMS\CoreBundle\Entity\Environment;
use EMS\CoreBundle\Service\EnvironmentService;
use EMS\CoreBundle\Service\ContentTypeService;
use EMS\MakerBundle\Service\FileService;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class ContentTypeCommand extends MakeCommand
{
    protected static $defaultName = 'ems:make:contenttype';

    /** @var EnvironmentService */
    private $environmentService;
    /** @var ContentTypeService */
    private $contentTypeService;
    /** @var Environment */
    private $environment;

    const ARGUMENT_CONTENTTYPES = 'contenttypes';
    const OPTION_ALL = 'all';
    const OPTION_ENV = 'environment';

    public function __construct(EnvironmentService $environmentService, ContentTypeService $contentTypeService, FileService $fileService)
    {
        $this->environmentService = $environmentService;
        $this->contentTypeService = $contentTypeService;
        parent::__construct($fileService, FileService::TYPE_CONTENTTYPE);
    }

    protected function configure()
    {
        parent::configure();
        $this
            ->addArgument(
                self::ARGUMENT_CONTENTTYPES,
                InputArgument::IS_ARRAY,
                sprintf('Optional array of contenttypes to create. Allowed values: [%s]', (string) $this->fileNames)
            )
            ->addOption(
                'environment',
                null,
                InputOption::VALUE_REQUIRED,
                'Default environment for the contenttypes',
                'preview'
            )
            ->addOption(
                self::OPTION_ALL,
                null,
                InputOption::VALUE_NONE,
                sprintf('Make all contenttypes: [%s]', (string) $this->fileNames)
            );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var array $types */
        $types = $input->getArgument(self::ARGUMENT_CONTENTTYPES);

        foreach ($types as $typeName) {
            try {
                /** @var string $json */
                $json = $this->fileService->getFileContentsByFileName($typeName, FileService::TYPE_CONTENTTYPE);
                /** @var ContentType $contentType */
                $contentType = $this->contentTypeService->contentTypeFromJson($json, $this->environment);
                $contentType = $this->contentTypeService->importContentType($contentType);
                $this->io->success(sprintf('Contenttype %s has been created', $contentType->getName()));
            } catch (\Exception $e) {
                $this->io->error($e->getMessage());
            }
        }
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $this->io->title('Make contenttypes');
        $this->io->section('Checking input');

        /** @var array $types */
        $types = $input->getArgument(self::ARGUMENT_CONTENTTYPES);

        if (!$input->getOption(self::OPTION_ALL) && count($types) == 0) {
            $this->chooseTypes($input, $output);
        }

        if ($input->getOption(self::OPTION_ALL)) {
            $this->optionAll($input);
        }

        $this->checkEnvironment($input);
    }

    private function chooseTypes(InputInterface $input, OutputInterface $output): void
    {
        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion(
            'Select the contenttypes you want to import',
            array_merge([self::OPTION_ALL], $this->fileNames->toArray())
        );
        $question->setMultiselect(true);

        $types = $helper->ask($input, $output, $question);
        if (in_array(self::OPTION_ALL, $types)) {
            $input->setOption(self::OPTION_ALL, true);
            $this->io->note(sprintf('Continuing with option --%s', self::OPTION_ALL));
        } else {
            $input->setArgument(self::ARGUMENT_CONTENTTYPES, $types);
            $this->io->note(['Continuing with contenttypes:', implode(', ', $types)]);
        }
    }

    private function optionAll(InputInterface $input): void
    {
        $input->setArgument(self::ARGUMENT_CONTENTTYPES, $this->fileNames->toArray());
        $this->io->note(['Continuing with contenttypes:', (string) $this->fileNames]);
    }

    private function checkEnvironment(InputInterface $input): void
    {
        /** @var string $env */
        $env = $input->getOption(self::OPTION_ENV);
        /** @var Environment|false $environment */
        $environment = $this->environmentService->getByName($env);

        if ($environment === false) {
            $this->io->caution('Environment ' . $env . ' does not exist');
            $env = $this->io->choice('Select an existing environment as default', $this->environmentService->getEnvironmentNames());
            $input->setOption(self::OPTION_ENV, $env);
            $this->checkEnvironment($input);
            return;
        }

        $this->environment = $environment;
        $this->io->note(sprintf('Continuing with environment %s', $env));
    }
}
