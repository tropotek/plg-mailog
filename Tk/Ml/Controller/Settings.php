<?php
namespace Tk\Ml\Controller;

use Tk\Ml\Db\MailLog;
use Tk\Request;
use Tk\Form\Event;
use Tk\Form\Field;
use Tk\Ml\Plugin;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Settings extends \Bs\Controller\AdminEditIface
{

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
     * @throws \Exception
     */
    public function doDefault(Request $request)
    {
        if (!$this->getAuthUser()->isAdmin()) {
            \Tk\
            Alert::addWarning('You do not have permission to view this page');
            \Uni\Uri::createHomeUrl('/index.html');
        }

        $this->setForm($this->getConfig()->createForm('pluginSettings'));
        $this->getForm()->setRenderer($this->getConfig()->createFormRenderer($this->getForm()));

        // TODO: What if they have a menu object?????
        $this->getForm()->appendField(new Field\Input('plugin.menu.nav.dropdown'))->setLabel('Dropdown Menu Name')
            ->setRequired(true)->setValue('nav-dropdown');
        $this->getForm()->appendField(new Field\Input('plugin.menu.nav.side'))->setLabel('Side Menu Name')
            ->setRequired(true)->setValue('nav-side');

        $this->getForm()->appendField(new Event\Button('update', array($this, 'doSubmit')));
        $this->getForm()->appendField(new Event\Button('save', array($this, 'doSubmit')));
        $this->getForm()->appendField(new Event\LinkButton('cancel', $this->getConfig()->getBackUrl()));

        $this->getForm()->load($this->data->toArray());
        $this->getForm()->execute();
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

        if ($form->hasErrors()) {
            return;
        }
        
        $this->data->save();
        
        \Tk\Alert::addSuccess('Site settings saved.');
        $event->setRedirect($this->getConfig()->getBackUrl());
        if ($form->getTriggeredEvent()->getName() == 'save') {
            $event->setRedirect(\Tk\Uri::create());
        }
    }

    public function initActionPanel()
    {
        $this->getActionPanel()->append(
            \Tk\Ui\Link::createBtn('View Log',
                \Bs\Uri::createHomeUrl(MailLog::createMailLogUrl('/manager.html')), 'fa fa-envelope')
        );
    }

    /**
     * show()
     *
     * @return \Dom\Template
     */
    public function show()
    {
        $this->initActionPanel();
        $template = parent::show();
        
        // Render the form
        $template->appendTemplate('form', $this->getForm()->getRenderer()->show());

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