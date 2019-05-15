<?php

namespace EMS\MakerBundle\Command;

use Symfony\Component\Console\Command\Command;

class AnalyserCommand extends Command
{
    protected static $defaultName = 'ems:make:analyser';

    /**
     * create an analyser based on a selection (input) in the Resource/make/analyser folder.
     * --help should include the list of files in that folder
     *
     * Pass the desired languages to the command (default EN only) (--all ?)
     */
}
