<?php
namespace Tk\Ml\Listener;

use Tk\Event\Subscriber;
use Tk\Event\Event;
use Tk\Ml\Plugin;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class MenuHandler implements Subscriber
{

    /**
     * @var \App\Controller\Iface
     */
    protected $controller = null;


    /**
     * @param Event $event
     */
    public function onControllerInit(Event $event)
    {
        /** @var \App\Controller\Iface $controller */
        $this->controller = $event->get('controller');
    }

    /**
     * @param \Dom\Event\DomEvent $event
     * @throws \Dom\Exception
     * @throws \Tk\Db\Exception
     * @throws \Tk\Exception
     */
    public function onTemplateLoad(\Dom\Event\DomEvent $event)
    {
        // Get the page template
        $plugin = Plugin::getInstance();
        $template = $event->getTemplate();

        $var = $plugin->getData()->get('plugin.menu.admin.var');
        $rendererClass = trim($plugin->getData()->get('plugin.menu.admin.renderer'), '\\');
        if ($rendererClass != $event->get('callingClass') && !in_array($rendererClass, class_parents($event->get('callingClass')))) {
            return;
        }
        if (!$template->has($var)) {
            return;
        }

        $template->appendHtml($var, $plugin->getData()->get('plugin.menu.admin.content'));
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
            //\Tk\PageEvents::CONTROLLER_INIT => array('onControllerInit', 0),
            \Dom\DomEvents::DOM_TEMPLATE_LOAD => array('onTemplateLoad', 0)
        );
    }
    
}