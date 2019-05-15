<?php

namespace EMS\MakerBundle\Command;

use Symfony\Component\Console\Command\Command;

class UserCommand extends Command
{
    protected static $defaultName = 'ems:make:user';

    /**
     * create a user based on a selection (input) in the Resource/make/user folder.
     * --help should include the list of files in that folder
     *
     * By default, create a user that has super-admin access.
     *
     * If one or more arguments are given, create the all the specified users
     *
     * In this first phase, create 1 super admin user, not from config file.
     */
}
