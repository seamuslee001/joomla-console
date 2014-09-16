<?php
/**
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		Mozilla Public License, version 2.0
 * @link		http://github.com/joomlatools/joomla-console for the canonical source repository
 */

namespace Joomlatools\Console\Command;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UserCreate extends UserAbstract
{
    

    protected function configure()
    {
        parent::configure();
        $this
            ->setName('user:create')
            ->setDescription('Create a Joomla user');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        
        $this->createUser($this->userParams, $output);
        
    }

 /**
   * Function to create a user of Joomla.
   *
   * @param array  $params associated array
   * @param string $mail email id for cms user
   *
   * @return uid if user exists, false otherwise
   *
   * @access public
   */
  public function createUser(&$params, $output) {

    require_once JPATH_BASE.'/libraries/joomla/user/helper.php';
    require_once JPATH_BASE.'/libraries/joomla/user/user.php';
    require_once JPATH_BASE.'/libraries/cms/component/helper.php';

    $salt = \JUserHelper::genRandomPassword(32);
    $password_clear = $params->pass;
    $crypted  = \JUserHelper::getCryptedPassword($password_clear, $salt);
    $password = $crypted.':'.$salt;
    $instance = \JUser::getInstance();

    $instance->set('id',0);
    $instance->set('name',$params->name);
    $instance->set('username',$params->user);
    $instance->set('password', $password);
    $instance->set('password_clear',$password_clear);
    $instance->set('email',$params->email);
    $instance->set('groups', array($params->group));
    $instance->set('block',0);

    if (!$instance->save())
    {
        // Return exception for instance
    } 
    else 
    {
        $output->writeln("Your Joomla user has been created. You can login using the credentials $params->user / $password_clear");
    }
  }
    


}
