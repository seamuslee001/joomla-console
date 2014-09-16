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

    $baseDir = JPATH_BASE;
    require_once $baseDir . '/components/com_users/models/registration.php';

    $userParams = \JComponentHelper::getParams('com_users');
    $model      = new \UsersModelRegistration();

    // check for a specified usertype, else get the default usertype
    if (isset($params->group)) $userType = $params->group;
    else {
      $userType = $userParams->get('new_usertype');
      if (!$userType) {
        $userType = 2;
      }
    }

    $fullname = trim($params->name);

    // Prepare the values for a new Joomla user.
    $values              = array();
    $values['name']      = $fullname;
    $values['username']  = trim($params->user);
    $values['password1'] = $values['password2'] = $params->pass;
    $values['email1']    = $values['email2'] = trim($params->email);
    $values['groups']    = array($userType);
    $values['block']     = 0;

    // $lang = $this->app->getLanguage();;
    // $lang->load('com_users', $baseDir);

    $register = $model->register($values);

    $uid = \JUserHelper::getUserId($values['username']);
    return $uid;
  }
    


}
