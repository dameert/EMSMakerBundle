<?php

namespace EMS\MakerBundle\Command;

use Symfony\Component\Console\Command\Command;

class RevisionCommand extends Command
{
    protected static $defaultName = 'ems:make:revision';

    /**
     * create a revision based on a selection (input) in the Resource/make/revision folder.
     * --help should include the list of files in that folder
     *
     * Pass the desired revisions to create to the command (or --all ?)
     *
     * If the command tries to create a revision for a contenttype that does not exist, ask interactively if we can
     * create the missing contenttype(s) first. (ask for each one separatly), if NO --> stop execution
     *
     * If a specific revision is created, with an internal link, and that link does not exist, ask if the missing
     * revision can be created first (ask for each one separatly), if NO --> continue execution
     *
     * Languages used are deduced from the contenttype used for the revision.
     * (what if we created a contenttype in IT, and the revision has no predefined content for that language?
     */
}
