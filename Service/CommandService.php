<?php
namespace EMS\MakerBundle\Service;

use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Finder\SplFileInfo;

class CommandService
{
    
    private $dirDemo = ['ems:make:contenttype' => __DIR__ . '/../Resources/make/contenttype' ];
    
    public function getDemoFiles($commandType): Array
    {
        $finder = new Finder();
        $finder = $finder->files()->name('*.json')->in($this->dirDemo[$commandType]);
        $values = [];
        foreach ($finder as $file) {
            /** @var SplFileInfo $file **/
            $values[substr($file->getBasename(), 0, strpos($file->getBasename(), $file->getExtension()) - 1)] = $file->getBasename();
        }
        return $values;
    }
    
    public function getDemoFile(string $name, $commandType): ?UploadedFile
    {
        /* @TODO try catch to generate error remove return null return array of json */
        $finder = new Finder();
        $finder = $finder->files()->name($name)->in($this->dirDemo[$commandType]);
        if (count($finder) == 1) {
            foreach ($finder as $file) {
                /** @var SplFileInfo $file **/
                if ($path = $file->getRealPath()) {
                    $jsonFile = new UploadedFile($path, $file->getBasename());
                    return $jsonFile;
                }
            }
        }
        return null;
    }
}
