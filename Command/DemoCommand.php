<?php

namespace EMS\MakerBundle\Command;

use Symfony\Component\Console\Command\Command;

class DemoCommand extends Command
{
    protected static $defaultName = 'ems:make:demo';

    /**
     * chained call of commands to create a fully functional demo website
     *
     * Launch in this order:
     * * analyser --all
     * * environment --all
     * * contenttype --all
     * * revision --all
     * * user --all
     *
     * By default, don't pass language config (each command has defaults), but make it possible to have a list of languages to support as argument
     * When languages are defined; pass them to
     * * analyser
     * * contenttype
     */
}
