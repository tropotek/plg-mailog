<?php
namespace Tk\Ml;

use Tk\EventDispatcher\EventDispatcher;
use Tk\Ml\Db\MailLog;


/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 */
class Plugin extends \Tk\Plugin\Iface
{

    /**
     * @var string
     */
    public static $DB_TABLE = 'mail_log';


    public function __construct($id, $name)
    {
        parent::__construct($id, $name);
        if ($this->getConfig()->get('plg.mail_log.table'))
            self::$DB_TABLE = $this->getConfig()->get('plg.mail_log.table');
    }


    /**
     * A helper method to get the Plugin instance globally
     *
     * @return \Tk\Plugin\Iface|Plugin
     * @throws \Tk\Exception
     */
    static function getInstance()
    {
        return \App\Config::getInstance()->getPluginFactory()->getPlugin('mailog');
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

        /** @var EventDispatcher $dispatcher */
        $dispatcher = \App\Config::getInstance()->getEventDispatcher();
        $dispatcher->addSubscriber(new \Tk\Ml\Listener\MailLogHandler());
        $dispatcher->addSubscriber(new \Tk\Ml\Listener\MenuHandler());

    }

    /**
     * Activate the plugin, essentially
     * installing any DB and settings required to run
     * Will only be called when activating the plugin in the
     * plugin control panel
     *
     * @throws \Exception
     */
    function doActivate()
    {
        // TODO: Implement doActivate() method.

        $db = $this->getConfig()->getDb();

        $table = self::$DB_TABLE;
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `$table` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fkey` VARCHAR(64) NOT NULL DEFAULT '',           -- A foreign key as a string (usually the object name or group name)
  `fid` INTEGER NOT NULL DEFAULT 0,                 -- foreign_id
  `to` text,
  `from` text,
  `subject` text,
  `body` text,
  `hash` varchar(64) DEFAULT NULL,
  `notes` text,
  `del` TINYINT(1) NOT NULL DEFAULT 0,
  `created` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fkey` (`fkey`),
  KEY `fid` (`fkey`, `fid`)
  KEY (`hash`)
) ENGINE=InnoDB;
SQL;
        $db->exec($sql);

        // Init Settings
        $data = $this->getData();
        $data->set('plugin.menu.admin.renderer', '\App\Ui\Menu\AdminSideNav');
        $data->set('plugin.menu.admin.var', 'nav');
        $url = \Bs\Uri::createHomeUrl('/mailLogManager.html');
        $data->set('plugin.menu.admin.content', '<li><a href="'.htmlentities($url->toString()).'"><i class="fa fa-envelope-o fa-fw"></i> <span>Email Log</span></a></li>');
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
    function doUpgrade($oldVersion, $newVersion)
    {
        // Init Plugin Settings
//        $config = \Tk\Config::getInstance();
//        $db = $config->getDb();

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
        $db = $this->getConfig()->getDb();

        // Remove migration track
        $sql = sprintf('DELETE FROM %s WHERE %s LIKE %s', $db->quoteParameter(\Tk\Util\SqlMigrate::$DB_TABLE), $db->quoteParameter('path'),
            $db->quote('/plugin/' . $this->getName().'/%'));
        $db->query($sql);

        // Clear the data table of all plugin data
        $sql = sprintf('DELETE FROM %s WHERE %s LIKE %s', $db->quoteParameter(\Tk\Db\Data::$DB_TABLE), $db->quoteParameter('fkey'),
            $db->quote($this->getName().'%'));
        $db->query($sql);
        // OR
//        $data = \Tk\Db\Data::create($this->getName());
//        $data->clear();
//        $data->save();
        if ($db->hasTable(self::$DB_TABLE))
            $db->query('DROP TABLE '. self::$DB_TABLE);

    }


    /**
     * @return \Tk\Uri
     */
    public function getSettingsUrl()
    {
        return \Bs\Uri::createHomeUrl(MailLog::createMailLogUrl('/settings.html'));
    }

}