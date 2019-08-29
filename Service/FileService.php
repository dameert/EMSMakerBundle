<?php
namespace EMS\MakerBundle\Service;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class FileService
{
    
    const JSON_FILES = __DIR__ . '/../Resources/make/';
    const TYPE_ANALYSER = 'analyser';
    const TYPE_CONTENTTYPE = 'contenttype';
    const TYPE_ENVIRONMENT = 'environment';
    const TYPE_REVISION = 'revision';
    const TYPE_USER = 'user';
    const TYPES = [self::TYPE_ANALYSER, self::TYPE_CONTENTTYPE, self::TYPE_ENVIRONMENT, self::TYPE_REVISION, self::TYPE_USER];

    public function getFileNames(string $subDirectory): array
    {
        if (!in_array($subDirectory, self::TYPES)) {
            return [];
        }

        $finder = new Finder();
        $finder = $finder->files()->name('*.json')->in(self::JSON_FILES . $subDirectory);
        $values = [];
        /** @var SplFileInfo $file **/
        foreach ($finder as $file) {
            $values[] = substr($file->getBasename(), 0, strpos($file->getBasename(), $file->getExtension()) - 1);
        }
        return $values;
    }
    
    public function getFileContentsByFileName(string $name, string $subDirectory): ?string
    {
        $finder = new Finder();
        $finder = $finder->files()->name($name . '.json')->in(self::JSON_FILES . $subDirectory);

        if (count($finder) !== 1) {
            return null;// TODO throw error instead
        }

        foreach ($finder as $file) {
            /** @var SplFileInfo $file **/
            return $file->getContents();
        }
    }
}
