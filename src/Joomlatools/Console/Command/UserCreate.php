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
        
        $this->createUser($this->userParams);
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
  public function createUser(&$params) {

    $userParams = \JComponentHelper::getParams('com_users');

    // check for a specified usertype, else get the default usertype
    if (isset($params->group)) $userType = $params->group;
    else {
      $userType = $userParams->get('new_usertype');
      if (!$userType) {
        $userType = 2;
      }
    }

    // Prepare the data for the user object.
    $data = array();
    $data['name']      = trim($params->name);
    $data['username']  = trim($params->user);
    $data['password1'] = $data['password2'] = $params->pass;
    $data['email1']    = $data['email2'] = trim($params->email);
    $data['groups']    = array($userType);
    $data['block']     = 0;
    $data['email'] = \JStringPunycode::emailToPunycode($data['email1']);
    $data['password']  = $data['password1'];
    $data['activation'] = \JApplication::getHash(\JUserHelper::genRandomPassword());

    // Initialise the table with JUser; bind the data.
    $user = new \JUser;
    if (!$user->bind($data))
    {
      echo \JText::sprintf('COM_USERS_REGISTRATION_BIND_FAILED', $user->getError()); // FIXME $output
      return false;
    }

    // Store the data.
    if (!$user->save())
    {
      echo $user->getError(); // FIXME $output
      return false;
    }

    return $user->id;
  }
    


}
