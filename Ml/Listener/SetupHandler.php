<?php
namespace Ml\Listener;

use Tk\Event\Subscriber;
use Ml\Plugin;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class SetupHandler implements Subscriber
{

    /**
     * @param \Tk\Event\GetResponseEvent $event
     * @throws \Tk\Db\Exception
     * @throws \Tk\Exception
     */
    public function onRequest(\Tk\Event\GetResponseEvent $event)
    {
        /* NOTE:
         *  If you require the Institution object for an event
         *  be sure to subscribe events here.
         *  As any events fired before this event do not have access to
         *  the institution object, unless you manually save the id in the
         *  session on first page load?
         */
        $config = \App\Config::getInstance();
        $dispatcher = $config->getEventDispatcher();
        $plugin = Plugin::getInstance();


        $dispatcher->addSubscriber(new \Ml\Listener\MailLogHandler());

    }


    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return array(
            \Tk\Kernel\KernelEvents::REQUEST => array('onRequest', -10)
        );
    }
    
    
}