<?php
namespace Ml\Controller;

use Tk\Request;
use Tk\Form;
use Tk\Form\Event;
use Tk\Form\Field;
use App\Controller\Iface;
use Ml\Plugin;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class ProfileSettings extends Iface
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
     * TODO: we need to abstract out the Profile/Institution and subject form the plugins
     * @var \Uni\Db\ProfileIface
     */
    private $profile = null;


    /**
     * ProfileSettings constructor.
     */
    public function __construct()
    {
        $this->setPageTitle('Subject Profile Settings');
    }

    /**
     * @param Request $request
     * @throws Form\Exception
     * @throws \Tk\Db\Exception
     * @throws \Tk\Exception
     */
    public function doDefault(Request $request)
    {
        /** @var Plugin $plugin */
        $plugin = Plugin::getInstance();

        $this->profile = \App\Db\ProfileMap::create()->find($request->get('zoneId'));
        $this->data = \Tk\Db\Data::create($plugin->getName() . '.course.profile', $this->profile->getId());

        $this->form = $this->getConfig()->createForm('profileSettings');
        $this->form->setRenderer($this->getConfig()->createFormRenderer($this->form));

        $this->form->addField(new Field\Input('plugin.title'))->setLabel('Site Title')->setRequired(true);
        $this->form->addField(new Field\Input('plugin.email'))->setLabel('Site Email')->setRequired(true);
        
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
        
        if (empty($values['plugin.title']) || strlen($values['plugin.title']) < 3) {
            $form->addFieldError('plugin.title', 'Please enter your name');
        }
        if (empty($values['plugin.email']) || !filter_var($values['plugin.email'], \FILTER_VALIDATE_EMAIL)) {
            $form->addFieldError('plugin.email', 'Please enter a valid email address');
        }
        
        if ($this->form->hasErrors()) {
            return;
        }
        
        $this->data->save();
        
        \Tk\Alert::addSuccess('Settings saved.');

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
        $template->insertTemplate('form', $this->form->getRenderer()->show()->getTemplate());

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
      <div class="panel-heading"><i class="fa fa-cogs fa-fw"></i> Actions</div>
      <div class="panel-body " var="action-panel">
        <a href="javascript: window.history.back();" class="btn btn-default"><i class="fa fa-arrow-left"></i> <span>Back</span></a>
      </div>
    </div>
  
    <div class="panel panel-default">
      <div class="panel-heading"><i class="fa fa-cog"></i> Settings</div>
      <div class="panel-body">
        <div var="form"></div>
      </div>
    </div>
    
</div>
XHTML;

        return \Dom\Loader::load($xhtml);
    }
}