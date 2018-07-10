<?php
namespace Tk\Ml\Db;

use Tk\Db\Map\Model;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class MailLog extends Model implements \Tk\ValidInterface
{

    /**
     * @var int
     */
    public $id = 0;

    /**
     * @var string
     */
    public $to = '';

    /**
     * @var string
     */
    public $from = '';

    /**
     * @var string
     */
    public $subject = '';

    /**
     * @var string
     */
    public $body = '';

    /**
     * @var string
     */
    public $hash = '';

    /**
     * @var string
     */
    public $notes = '';

    /**
     * @var \DateTime
     */
    public $created = null;




    /**
     * User constructor.
     *
     */
    public function __construct()
    {
        $this->created = new \DateTime();
    }

    /**
     * @param \Tk\Mail\Message $message
     * @return MailLog
     */
    public static function createFromMessage($message)
    {
        $obj = new static();
        $obj->to = implode(',', $message->getTo());
        $obj->from = $message->getFrom();
        $obj->subject = $message->getSubject();
        $obj->body = $message->getParsed();
        return $obj;
    }

    /**
     *
     */
    public function save()
    {
        parent::save();
    }


    /**
     * @param $html
     * @return mixed|null|string|string[]
     */
    public function getHtmlBody()
    {
        $dom = new \DOMDocument();
        $dom->loadHTML($this->body);
        $el = $dom->getElementsByTagName('body')->item(0);
        $el->removeAttribute('data-template');  // For EMS v2 messages
        $body = $dom->saveXml($el);

        $body = '<div class="mail-log-body">' . substr($body, strlen('<body>'));
        $body = substr($body, 0, -strlen('</body>')).'</div>';

        $body = str_replace(' class="content"', '', $body);
        $body = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $body);
        $body = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', "", $body);

        $body = str_replace('<p></p>', '', $body);
        $body = str_replace('<p/>', '', $body);
        $body = str_replace('Â', ' ', $body);
        $body = str_replace('â', '', $body);

        return $body;
    }

    /**
     * Validate this object's current state and return an array
     * with error messages. This will be useful for validating
     * objects for use within forms.
     *
     * @return array
     * @throws \Tk\Db\Exception
     */
    public function validate()
    {
        $errors = array();

        if (!$this->to) {
            $errors['to'] = 'Invalid field TO value';
        }
        if (!$this->from) {
            $errors['from'] = 'Invalid field FROM value';
        }
        if (!$this->subject) {
            $errors['subject'] = 'Invalid field SUBJECT value';
        }
        if (!$this->body) {
            $errors['body'] = 'Invalid field BODY value';
        }

        return $errors;
    }
}
