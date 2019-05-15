<?php

namespace EMS\MakerBundle\Command;

use Symfony\Component\Console\Command\Command;

class EnvironmentCommand extends Command
{
    protected static $defaultName = 'ems:make:environment';

    /**
     * create an environment based on a selection (input) in the Resource/make/environment folder.
     * --help should include the list of files in that folder
     *
     * Pass the desired environments to create to the command (or --all ?)
     */
}
