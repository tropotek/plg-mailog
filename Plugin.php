<?php
namespace Ml;

use Tk\Event\Dispatcher;


/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 */
class Plugin extends \Tk\Plugin\Iface
{

    const ZONE_INSTITUTION = 'institution';
    const ZONE_COURSE_PROFILE = 'profile';
    const ZONE_COURSE = 'course';

    /**
     * A helper method to get the Plugin instance globally
     *
     * @return \Tk\Plugin\Iface|Plugin
     * @throws \Tk\Exception
     */
    static function getInstance()
    {
        return \App\Config::getInstance()->getPluginFactory()->getPlugin('sample');
    }


    // ---- \Tk\Plugin\Iface Interface Methods ----


    /**
     * Init the plugin
     *
     * This is called when the session first registers the plugin to the queue
     * So it is the first called method after the constructor.....
     */
    function doInit()
    {
        include dirname(__FILE__) . '/config.php';

        // Register the plugin for the different client areas if they are to be enabled/disabled/configured by those roles.
        $this->getPluginFactory()->registerZonePlugin($this, self::ZONE_INSTITUTION);
        $this->getPluginFactory()->registerZonePlugin($this, self::ZONE_COURSE_PROFILE);
        $this->getPluginFactory()->registerZonePlugin($this, self::ZONE_COURSE);

        /** @var Dispatcher $dispatcher */
        $dispatcher = \App\Config::getInstance()->getEventDispatcher();
        $dispatcher->addSubscriber(new \Ml\Listener\SetupHandler());
    }

    /**
     * Activate the plugin, essentially
     * installing any DB and settings required to run
     * Will only be called when activating the plugin in the
     * plugin control panel
     *
     * @throws \Tk\Db\Exception
     */
    function doActivate()
    {
        // TODO: Implement doActivate() method.

        // Init Settings
        $data = \Tk\Db\Data::create($this->getName());
        $data->set('plugin.title', 'EMS III Example Plugin');
        $data->set('plugin.email', 'null@unimelb.edu.au');
        $data->save();
    }

    /**
     * Example upgrade code
     * This will be called when you update the plugin version in the composer.json file
     *
     * Upgrade the plugin
     * Called when the file version is larger than the version in the DB table
     *
     * @param string $oldVersion
     * @param string $newVersion
     */
    function doUpgrade($oldVersion, $newVersion) {
        // Init Plugin Settings
//        $config = \Tk\Config::getInstance();
//        $db = \App\Factory::getDb();

//        $migrate = new \Tk\Util\SqlMigrate($db);
//        $migrate->setTempPath($config->getTempPath());
//        $migrate->migrate(dirname(__FILE__) . '/sql');

//        if (version_compare($oldVersion, '1.0.1', '<')) { ; }
//        if (version_compare($oldVersion, '1.0.2', '<')) { ; }
    }

    /**
     * Deactivate the plugin removing any DB data and settings
     * Will only be called when deactivating the plugin in the
     * plugin control panel
     *
     * @throws \Tk\Db\Exception
     */
    function doDeactivate()
    {
        // TODO: Implement doDeactivate() method.
        
        // Delete any setting in the DB
        $data = \Tk\Db\Data::create($this->getName());
        $data->clear();
        $data->save();
    }

    /**
     * Get the course settings URL, if null then there is none
     *
     * @return string|\Tk\Uri|null
     */
    public function getZoneSettingsUrl($zoneName)
    {
        switch ($zoneName) {
            case self::ZONE_INSTITUTION:
                return \Tk\Uri::create('/sample/institutionSettings.html');
            case self::ZONE_COURSE_PROFILE:
                return \Tk\Uri::create('/sample/courseProfileSettings.html');
            case self::ZONE_COURSE:
                return \Tk\Uri::create('/sample/courseSettings.html');
        }
        return null;
    }

    /**
     * @return \Tk\Uri
     */
    public function getSettingsUrl()
    {
        return \Tk\Uri::create('/sample/adminSettings.html');
    }

}