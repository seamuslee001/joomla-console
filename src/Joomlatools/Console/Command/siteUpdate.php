<?php
/**
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		Mozilla Public License, version 2.0
 * @link		http://github.com/joomlatools/joomla-console for the canonical source repository
 */

namespace Joomlatools\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Joomlatools\Console\Joomla\Bootstrapper;

class SiteUpdate extends SiteAbstract
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('site:update')
            ->setDescription('Update a site to the latest stable Joomla version in the same branch');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $this->check($input, $output);
        $this->update($input, $output);
    }

    public function check(InputInterface $input, OutputInterface $output)
    {
        if (!file_exists($this->target_dir)) {
            throw new \RuntimeException(sprintf('Site not found: %s', $this->site));
        }
    }


    public function update(InputInterface $input, OutputInterface $output)
    {
        $app = Bootstrapper::getApplication($this->target_dir);

        // Output buffer is used as a guard against Joomla including ._ files when searching for adapters
        // See: http://kadin.sdf-us.org/weblog/technology/software/deleting-dot-underscore-files.html
        ob_start();

        define('JPATH_COMPONENT_ADMINISTRATOR', $app->getPath().'/administrator/components/com_joomlaupdate');
        require_once JPATH_COMPONENT_ADMINISTRATOR . '/models/default.php';

        $model = new \JoomlaupdateModelDefault();

        // Perform update source preference check and refresh update information
        $output->writeln("Purge: ".$model->purge());
        $model->applyUpdateSite();
        $model->refreshUpdates();

        $updateinfo = $model->getUpdateInformation();

        if(!version_compare($updateinfo['installed'], $updateinfo['latest'], '<'))
        {
            $output->writeln("<info>Site ". $this->site ." already has latest (". $updateinfo['latest'] .") or newer version: ". $updateinfo['installed'] .".</info>");
        }
        else
        {

        }

        ob_end_clean();
    }
}