<?php
namespace Tk\Ml\Db;

use Bs\Db\Traits\CreatedTrait;
use Bs\Db\Traits\ForeignModelTrait;
use Tk\Db\Map\Model;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class MailLog extends Model implements \Tk\ValidInterface
{
    use ForeignModelTrait;
    use CreatedTrait;

    /**
     * @var int
     */
    public $id = 0;

    /**
     * @var string
     */
    public $fkey = 'system';

    /**
     * @var int
     */
    public $fid = 0;

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
        $this->_CreatedTrait();
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
     * @param string $baseurl  The url page
     * @param string|\Tk\Db\ModelInterface $fkey
     * @param int $fid
     * @return string
     */
    public static function createMailLogUrl($baseurl, $fkey = 'system', $fid = 0)
    {
        if (is_object($fkey))
            $fkey = \Tk\ObjectUtil::basename($fkey);
        if (class_exists($fkey))
            $fkey = \Tk\ObjectUtil::basename($fkey);
        return '/mailLog/'.$fkey.'/'.$fid. '/' . trim($baseurl, '/');
    }

    /**
     * @return string
     */
    public function getTo(): string
    {
        return $this->to;
    }

    /**
     * @param string $to
     * @return $this
     */
    public function setTo(string $to): MailLog
    {
        $this->to = $to;
        return $this;
    }

    /**
     * @return string
     */
    public function getFrom(): string
    {
        return $this->from;
    }

    /**
     * @param string $from
     * @return $this
     */
    public function setFrom(string $from): MailLog
    {
        $this->from = $from;
        return $this;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     * @return $this
     */
    public function setSubject(string $subject): MailLog
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @param string $body
     * @return $this
     */
    public function setBody(string $body): MailLog
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     * @return $this
     */
    public function setHash(string $hash): MailLog
    {
        $this->hash = $hash;
        return $this;
    }

    /**
     * @return string
     */
    public function getNotes(): string
    {
        return $this->notes;
    }

    /**
     * @param string $notes
     * @return $this
     */
    public function setNotes(string $notes): MailLog
    {
        $this->notes = $notes;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreated(): \DateTime
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     * @return $this
     */
    public function setCreated(\DateTime $created): MailLog
    {
        $this->created = $created;
        return $this;
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
