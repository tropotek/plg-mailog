<?php
namespace Tk\Ml\Controller\MailLog;

use Tk\Request;
use Dom\Template;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class View extends \Bs\Controller\AdminIface
{

    /**
     * @var \Tk\Ml\Db\MailLog
     */
    private $mailLog = null;


    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $this->setPageTitle('Mail Log View');
    }

    /**
     *
     * @param Request $request
     * @throws \Exception
     */
    public function doDefault(Request $request)
    {
        $this->mailLog = \Tk\Ml\Db\MailLogMap::create()->find($request->get('mailLogId'));
    }

    /**
     * @return \Dom\Template
     * @throws \Exception
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

        $css = <<<CSS
.message-head {
  padding: 10px;
  border: 1px solid #CCC;
  background: #EFEFEF;
}
CSS;

        $template->appendCss($css);

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

<div class="tk-panel" data-panel-title="Mail Log" data-panel-icon="fa fa-envelope-o" var="panel">

  <div class="message-head">
    <b>Sent:</b> <span var="created"></span> <br/>
    <b>From:</b> <a href="#" var="from"></a> <br/>
    <b>To:</b> <a href="#" var="to"></a> <br/>
    <b>Subject:</b> <span var="subject">Mail Log</span>
  </div>
  <p>&nbsp;</p>
  <div class="message-body" var="body"></div>

</div>
HTML;

        return \Dom\Loader::load($xhtml);
    }

}