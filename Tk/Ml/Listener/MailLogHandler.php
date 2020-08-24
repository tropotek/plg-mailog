<?php
namespace Tk\Ml\Listener;

use Tk\ConfigTrait;
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
    use ConfigTrait;

    /**
     * @param \Tk\Mail\MailEvent $event
     */
    public function preSend(\Tk\Mail\MailEvent $event)
    {
        $message = $event->getMessage();
        $headers = $message->getHeadersList();
        if ($message && !array_key_exists('X-Exception', $headers)) {
            $title = $this->getConfig()->get('site.title');
            if (!$title) $title = 'Tk2 Site';
            $message->addHeader('X-System-Message', $title);
        }

    }

    /**
     * @param \Tk\Mail\MailEvent $event
     */
    public function postSend(\Tk\Mail\MailEvent $event)
    {
        $message = $event->getMessage();

        if (!$message || array_key_exists('X-Exception', $message->getHeadersList())) {
            return;
        }
        if (!$this->getConfig()->isDebug() && current($message->getTo()) == $this->getConfig()->get('site.email')) {
            return;
        }

        $mailLog = $event->get('mailLog');
        if (!$mailLog) {
            $mailLog = \Tk\Ml\Db\MailLog::createFromMessage($message);
            $event->set('mailLog', $mailLog);
        }
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


