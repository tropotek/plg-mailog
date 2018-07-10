<?php
namespace Tk\Ml\Controller;

use Tk\Request;
use Tk\Form;
use Tk\Form\Event;
use Tk\Form\Field;
use Tk\Ml\Plugin;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class SystemSettings extends \Bs\Controller\AdminIface
{

    /**
     * @var Form
     */
    protected $form = null;

    /**
     * @var \Tk\Db\Data|null
     */
    protected $data = null;


    /**
     * SystemSettings constructor.
     * @throws \Tk\Db\Exception
     * @throws \Tk\Exception
     */
    public function __construct()
    {
        $this->setPageTitle('Plugin Settings');

        /** @var Plugin $plugin */
        $plugin = Plugin::getInstance();
        $this->data = \Tk\Db\Data::create($plugin->getName());
    }

    /**
     * @param Request $request
     * @throws Form\Exception
     * @throws \Tk\Exception
     */
    public function doDefault(Request $request)
    {
        $this->form = $this->getConfig()->createForm('systemSettings');
        $this->form->setRenderer($this->getConfig()->createFormRenderer($this->form));

        // TODO: What if they have a menu object?????
        $this->form->addField(new Field\Input('plugin.menu.renderer'))->setLabel('Menu Renderer Class')
            ->setRequired(true)->setNotes('Set the renderer class name that we need to catch to renderer the menu');

        $this->form->addField(new Field\Input('plugin.menu.var'))->setLabel('Menu Var')
            ->setRequired(true)->setNotes('Set the template var where the mail log menu items will be appended to in the page template');

        $this->form->addField(new Field\Textarea('plugin.menu.content'))->setLabel('Menu Item')
            ->setRequired(true)->setNotes('The content for the menu');
        
        $this->form->addField(new Event\Button('update', array($this, 'doSubmit')));
        $this->form->addField(new Event\Button('save', array($this, 'doSubmit')));
        $this->form->addField(new Event\LinkButton('cancel', $this->getConfig()->getBackUrl()));

        $this->form->load($this->data->toArray());
        $this->form->execute();
    }

    /**
     * @param \Tk\Form $form
     * @param \Tk\Form\Event\Iface $event
     * @throws \Tk\Db\Exception
     */
    public function doSubmit($form, $event)
    {
        $values = $form->getValues();
        $this->data->replace($values);

        //if (empty($values['plugin.menu.renderer']) || !class_exists($values['plugin.menu.renderer'])) {
        if (empty($values['plugin.menu.renderer']) ) {
            $form->addFieldError('plugin.menu.renderer', 'Please enter a valid class name for the menu renderer');
        }
        if (empty($values['plugin.menu.var'])) {
            $form->addFieldError('plugin.menu.var', 'Please enter the var name for the menu in the page template');
        }
        if (empty($values['plugin.menu.content'])) {
            $form->addFieldError('plugin.menu.content', 'Please enter the menu item content for the page template');
        }
        
        if ($this->form->hasErrors()) {
            return;
        }
        
        $this->data->save();
        
        \Tk\Alert::addSuccess('Site settings saved.');
        $event->setRedirect($this->getConfig()->getBackUrl());
        if ($form->getTriggeredEvent()->getName() == 'save') {
            $event->setRedirect(\Tk\Uri::create());
        }
    }

    /**
     * show()
     *
     * @return \Dom\Template
     */
    public function show()
    {
        $template = parent::show();
        
        // Render the form
        $template->insertTemplate('form', $this->form->getRenderer()->show());

        return $template;
    }

    /**
     * DomTemplate magic method
     *
     * @return \Dom\Template
     */
    public function __makeTemplate()
    {
        $xhtml = <<<XHTML
<div var="content">

  
    <div class="panel panel-default">
      <div class="panel-heading"><i class="fa fa-cog"></i> Site Settings</div>
      <div class="panel-body">
        <div var="form"></div>
      </div>
    </div>
    
</div>
XHTML;

        return \Dom\Loader::load($xhtml);
    }
}