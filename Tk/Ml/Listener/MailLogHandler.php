<?php
namespace Tk\Ml\Listener;

use Tk\Event\Subscriber;



/**
 * Handler to save a MailLog record on email sent
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class MailLogHandler implements Subscriber
{

    /**
     * @param \Tk\Mail\MailEvent $event
     */
    public function preSend(\Tk\Mail\MailEvent $event)
    {
        $config = \App\Config::getInstance();
        $message = $event->getMessage();
        $headers = $message->getHeadersList();
        if ($message && !array_key_exists('X-Exception', $headers)) {

            $title = $config->get('site.title');
            if (!$title) $title = 'Tk2 Site';

            $message->addHeader('X-System-Message', $title);
        }

    }

    /**
     * @param \Tk\Mail\MailEvent $event
     */
    public function postSend(\Tk\Mail\MailEvent $event)
    {
        $config = \App\Config::getInstance();
        $message = $event->getMessage();

        vd();
        if (!$message || array_key_exists('X-Exception', $message->getHeadersList())) {
            return;
        }
        vd();
        if (!$config->isDebug() && current($message->getTo()) == $config->get('site.email')) {
            return;
        }
        vd();
        $mailLog = \Tk\Ml\Db\MailLog::createFromMessage($message);
        $mailLog->save();
    }

    /**
     * getSubscribedEvents
     * 
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            \Tk\Mail\MailEvents::PRE_SEND => array('preSend', 0),
            \Tk\Mail\MailEvents::POST_SEND => array('postSend', 0)
        );
    }

}


