<?php

namespace EMS\MakerBundle\Command;

use Symfony\Component\Console\Command\Command;

class ContentTypeCommand extends Command
{
    protected static $defaultName = 'ems:make:contenttype';

    /**
     * create a contenttype based on a selection (input) in the Resource/make/contenttype folder.
     * --help should include the list of files in that folder
     *
     * Pass the desired languages to the command (default EN only)
     * Pass the desired contenttypes to create to the command (or --all ?)
     * Pass the desired default environment, ask to create if the environment does not exist
     *
     * If the command tries to create a contenttype that links to a contenttype that does not exist, ask interactively if we can
     * create the missing contenttype(s) first. (ask for each one separatly) --> if response is NO, we should still create content type
     */
}
