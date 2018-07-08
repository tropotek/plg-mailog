<?php
namespace Ml\Controller\MailLog;

use Tk\Request;
use Dom\Template;
use Tk\Form\Field;

/**
 *
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Manager extends \Bs\Controller\AdminIface
{

    /**
     * @var \Tk\Table
     */
    protected $table = null;


    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $this->setPageTitle('Mail Log');
        if ($this->getCrumbs())
            $this->getCrumbs()->reset();
    }


    /**
     * @param Request $request
     * @throws \Tk\Db\Exception
     * @throws \Tk\Exception
     * @throws \Tk\Form\Exception
     */
    public function doDefault(Request $request)
    {

        $this->table = $this->getConfig()->createTable('mail-list');
        $this->table->setRenderer($this->getConfig()->createTableRenderer($this->table));

        //$this->table->addCell(new \Tk\Table\Cell\Checkbox('id'));
        $this->table->addCell(new \Tk\Table\Cell\Text('subject'))->addCss('key')->setUrl(\Tk\Uri::create('admin/mailLogView.html'));
        $this->table->addCell(new \Tk\Table\Cell\Text('to'));
        //$this->table->addCell(new \Tk\Table\Cell\Text('from'));
        $this->table->addCell(new \Tk\Table\Cell\Date('created'))->setFormat(\Tk\Date::FORMAT_LONG_DATETIME);

        // Filters
        $this->table->addFilter(new Field\Input('keywords'))->setLabel('')->setAttr('placeholder', 'Keywords');

        // Actions
        $this->table->addAction(new \Tk\Table\Action\Csv($this->getConfig()->getDb()));
        //$this->table->addAction(new \Tk\Table\Action\Delete());

        $list = \Ml\Db\MailLogMap::create()->findFiltered($this->table->getFilterValues(), $this->table->getTool('a.created DESC'));
        $this->table->setList($list);

    }

    /**
     * @return \Dom\Template
     * @throws \Dom\Exception
     */
    public function show()
    {
        $template = parent::show();

        $template->replaceTemplate('table', $this->table->getRenderer()->show());
        
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
    <div class="panel-heading">
      <i class="fa fa-envelope-o fa-fw"></i> Mail Log
    </div>
    <div class="panel-body">
      <div var="table"></div>
    </div>
  </div>

</div>
HTML;

        return \Dom\Loader::load($xhtml);
    }


}