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
class Settings extends \Bs\Controller\AdminIface
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
        $this->setPageTitle('Mail Log Settings');

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
        $this->form = $this->getConfig()->createForm('pluginSettings');
        $this->form->setRenderer($this->getConfig()->createFormRenderer($this->form));

        // TODO: What if they have a menu object?????
        $this->form->addField(new Field\Input('plugin.menu.nav.dropdown'))->setLabel('Dropdown Menu Name')
            ->setRequired(true)->setValue('nav-dropdown');
        $this->form->addField(new Field\Input('plugin.menu.nav.side'))->setLabel('Side Menu Name')
            ->setRequired(true)->setValue('nav-side');



//        $this->form->addField(new Field\Input('plugin.menu.admin.renderer'))->setLabel('Admin Menu Renderer Class')
//            ->setRequired(true)->setNotes('Set the renderer class name that we need to catch to renderer the menu');
//
//        $this->form->addField(new Field\Input('plugin.menu.admin.var'))->setLabel('Admin Menu Var')
//            ->setRequired(true)->setNotes('Set the template var where the mail log menu items will be appended to in the page template');
//
//        $this->form->addField(new Field\Textarea('plugin.menu.admin.content'))->setLabel('Admin Menu Item')
//            ->setRequired(true)->setNotes('The content for the menu');
        
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

//        if (empty($values['plugin.menu.nav.dropdown']) ) {
//            $form->addFieldError('plugin.menu.nav.dropdown', 'Please enter a valid Drop-down Menu name');
//        }
//
//        if (empty($values['plugin.menu.nav.side']) ) {
//            $form->addFieldError('plugin.menu.nav.side', 'Please enter a valid Side Menu name');
//        }

//        //if (empty($values['plugin.menu.admin.renderer']) || !class_exists($values['plugin.menu.admin.renderer'])) {
//        if (empty($values['plugin.menu.admin.renderer']) ) {
//            $form->addFieldError('plugin.menu.admin.renderer', 'Please enter a valid class name for the admin menu renderer');
//        }
//        if (empty($values['plugin.menu.admin.var'])) {
//            $form->addFieldError('plugin.menu.admin.var', 'Please enter the var name for the menu in the admin page template');
//        }
//        if (empty($values['plugin.menu.admin.content'])) {
//            $form->addFieldError('plugin.menu.admin.content', 'Please enter the menu item content link');
//        }
        
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
        $template->appendTemplate('form', $this->form->getRenderer()->show());

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
<div class="tk-panel" data-panel-title="Plugin Settings" data-panel-icon="fa fa-cogs" var="form"></div>
XHTML;

        return \Dom\Loader::load($xhtml);
    }
}