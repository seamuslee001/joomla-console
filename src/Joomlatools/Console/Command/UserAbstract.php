<?php
/**
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		Mozilla Public License, version 2.0
 * @link		http://github.com/joomlatools/joomla-console for the canonical source repository
 */

namespace Joomlatools\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Joomlatools\Console\Joomla\Bootstrapper;

abstract class UserAbstract extends Command
{

    protected $target_dir;
    protected $userParams;
    protected $groups;    

    protected function configure()
    {
        $this->addOption(
            'base',
            'b',
            InputOption::VALUE_OPTIONAL,
            'Base directory for the Joomla Installation. Default is the current directory'
        )
        ->addOption(
            'name',
            null,
            InputOption::VALUE_REQUIRED,
            'First and Last name of this user'
        )
        ->addOption(
            'user',
            null,
            InputOption::VALUE_REQUIRED,
            'Alphanumeric user email'
        )
        ->addOption(
            'pass',
            null,
            InputOption::VALUE_OPTIONAL,
            'Password for the user'
        )
        ->addOption(
            'email',
            null,
            InputOption::VALUE_REQUIRED,
            'Email address for this user'
        )
        ->addOption(
            'group',
            null,
            InputOption::VALUE_OPTIONAL,
            'User group to assign to this user. Choose admin or registered. Default is registered.',
            'registered'
        )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->target_dir = $input->getOption('base');
        
        $this->app = Bootstrapper::getApplication($this->target_dir);

        if (!$input->hasOption('pass')) 
        {
            require_once JPATH_BASE.'/libraries/joomla/user/helper.php';
            $pass = \JUserHelper::genRandomPassword(14);
        } else $pass = $input->getOption('pass');

        $this->groups = $this->getGroups();

        $group = $this->groups[$input->getOption('group')];

        $this->userParams = (object) array(
            'name'=>$input->getOption('name'),
            'user'=>$input->getOption('user'),
            'pass'=>$pass,
            'email'=>$input->getOption('email'),
            'group'=>$group->id
        );
        
    }

    protected function getGroups()
    {
        // Get the list of user groups
        $jDb = $this->app->getDbo();
        $userGroupQuery =  $jDb->getQuery(true);
        $userGroupQuery
          ->select(array('id','lower(title) as title'))
          ->from('#__usergroups');
        $jDb->setQuery($userGroupQuery);
        $accessGroupList = $jDb->loadObjectList('title');

        return $accessGroupList;
    }




}