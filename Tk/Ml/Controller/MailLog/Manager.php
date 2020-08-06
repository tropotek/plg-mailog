<?php
namespace Tk\Ml\Controller\MailLog;

use Tk\Ml\Db\MailLog;
use Tk\Request;
use Dom\Template;
use Tk\Form\Field;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Manager extends \Bs\Controller\AdminManagerIface
{

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $this->setPageTitle('Mail Log');
//        if ($this->getCrumbs())
//            $this->getCrumbs()->reset();
    }


    /**
     * @param Request $request
     * @throws \Exception
     */
    public function doDefault(Request $request, $fkey = 'system', $fid = 0)
    {
//        $fkey = 'system';
//        $fid = 0;
//        // TODO get these from the URL params

        $this->setTable($this->getConfig()->createTable('mail-list'));
        $this->getTable()->setRenderer($this->getConfig()->createTableRenderer($this->getTable()));

        //$this->getTable()->appendCell(new \Tk\Table\Cell\Checkbox('id'));
        $this->getTable()->appendCell(new \Tk\Table\Cell\Text('subject'))->addCss('key')->setUrl(
            \Tk\Uri::create(MailLog::createMailLogUrl('/view.html', $fkey, $fid))
        );
        $this->getTable()->appendCell(new \Tk\Table\Cell\Text('to'));
        //$this->getTable()->appendCell(new \Tk\Table\Cell\Text('from'));
        $this->getTable()->appendCell(new \Tk\Table\Cell\Date('created'))->setFormat(\Tk\Date::FORMAT_LONG_DATETIME);

        // Filters
        $this->getTable()->appendFilter(new Field\Input('keywords'))->setLabel('')->setAttr('placeholder', 'Keywords');

        // Actions
        $this->getTable()->appendAction(new \Tk\Table\Action\Csv($this->getConfig()->getDb()));
        //$this->table->appendAction(new \Tk\Table\Action\Delete());

        $list = \Tk\Ml\Db\MailLogMap::create()->findFiltered($this->getTable()->getFilterValues(), $this->getTable()->getTool('a.created DESC'));
        $this->getTable()->setList($list);

    }

    /**
     * @return \Dom\Template
     * @throws \Exception
     */
    public function show()
    {
        $template = parent::show();

        $template->appendTemplate('table', $this->getTable()->getRenderer()->show());
        
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
<div class="tk-panel" data-panel-title="Mail Log" data-panel-icon="fa fa-envelope-o" var="table"></div>
HTML;

        return \Dom\Loader::load($xhtml);
    }


}