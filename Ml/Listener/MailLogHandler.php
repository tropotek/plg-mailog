<?php
namespace Ml\Listener;

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
     * @throws \Tk\Db\Exception
     * @throws \Tk\Exception
     */
    public function preSend(\Tk\Mail\MailEvent $event)
    {
        $message = $event->getMessage();
        $headers = $message->getHeadersList();
        if ($message && !array_key_exists('X-Exception', $headers)) {
            $message->addHeader('X-System-Message', 'Safe Soda');
        }

    }

    public function postSend(\Tk\Mail\MailEvent $event)
    {
        $config = \App\Config::getInstance();

        if (current($event->getMessage()->getTo()) == $config->get('site.email')) {
            return;
        }

        $mailLog = \App\Db\MailLog::createFromMessage($event->getMessage());
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


