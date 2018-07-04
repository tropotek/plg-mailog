<?php
namespace Ml\Controller\MailLog;

use Tk\Request;
use Dom\Template;
use Tk\Form;
use App\Controller\AdminIface;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class View extends AdminIface
{

    /**
     * @var Form
     */
    protected $form = null;

    /**
     * @var \Ml\Db\MailLog
     */
    private $mailLog = null;


    /**
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();
        $this->setPageTitle('Mail Log View');
    }



    /**
     *
     * @param Request $request
     * @throws Form\Exception
     * @throws \ReflectionException
     * @throws \Tk\Db\Exception
     * @throws \Tk\Exception
     */
    public function doDefault(Request $request)
    {

        $this->mailLog = \Ml\Db\MailLog::getMapper()->find($request->get('mailLogId'));

    }

    /**
     * @return \Dom\Template
     * @throws \Dom\Exception
     */
    public function show()
    {
        $template = parent::show();

        $template->insertText('subject', $this->mailLog->subject);
        $template->insertText('created', $this->mailLog->created->format(\Tk\Date::FORMAT_LONG_DATETIME));

        $template->setAttr('from', 'href', 'mailto:'.$this->mailLog->from);
        $template->insertText('from', $this->mailLog->from);

        // TODO: This will b a problem for multiple to`s
        $template->setAttr('to', 'href', 'mailto:'.$this->mailLog->to);
        $template->insertText('to', $this->mailLog->to);

        $template->insertHtml('body', $this->mailLog->getHtmlBody());

        return $template;
    }


    /**
     * DomTemplate magic method
     *
     * @return Template
     */
    public function __makeTemplate()
    {
        $xhtml = <<<HTML
<div>

  <div class="panel panel-default">
    <div class="panel-heading clearfix">
        Subject: <span var="subject">Mail Log</span> <br/>
        Sent: <span var="created"></span> <br/>
        From: <a href="#" var="from"></a> <br/>
        To: <a href="#" var="to"></a>
    </div>
    <div class="panel-body">
        <div var="body"></div>
    </div>
  </div>
    
</div>
HTML;

        return \Dom\Loader::load($xhtml);
    }

}