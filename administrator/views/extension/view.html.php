<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Manifest2md
 * @author     Emmanuel Lecoester <elecoest@gmail.com>
 * @copyright  2017 elecoest
 * @license    GNU General Public License version 2 ou version ultérieure ; Voir LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View to edit
 *
 * @since  1.6
 */
class Manifest2mdViewExtension extends JViewLegacy
{
    protected $state;

    protected $item;

    protected $form;

    /**
     * Display the view
     *
     * @param   string $tpl Template name
     *
     * @return void
     *
     * @throws Exception
     */
    public function display($tpl = null)
    {
        $this->state = $this->get('State');
        $this->item = $this->get('Item');
        $this->form = $this->get('Form');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }

        $this->addToolbar();
        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return void
     *
     * @throws Exception
     */
    protected function addToolbar()
    {
        JFactory::getApplication()->input->set('hidemainmenu', true);

        $user = JFactory::getUser();
        $isNew = ($this->item->id == 0);

        if (isset($this->item->checked_out)) {
            $checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
        } else {
            $checkedOut = false;
        }

        $canDo = Manifest2mdHelpersManifest2mdadmin::getActions();

        JToolbarHelper::title(JText::_('COM_MANIFEST2MD_TITLE_EXTENSION'), 'extension.png');

        // If not checked out, can save the item.
        if (!$checkedOut && ($canDo->get('core.edit') || ($canDo->get('core.create')))) {
            JToolbarHelper::apply('extension.apply', 'JTOOLBAR_APPLY');
            JToolbarHelper::save('extension.save', 'JTOOLBAR_SAVE');
        }

        if (!$checkedOut && ($canDo->get('core.create'))) {
            JToolbarHelper::custom('extension.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
        }

        // If an existing item, can save to a copy.
        if (!$isNew && $canDo->get('core.create')) {
            JToolbarHelper::custom('extension.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
        }

        // Button for version control
        if ($this->state->params->get('save_history', 1) && $user->authorise('core.edit')) {
            JToolbarHelper::versions('com_manifest2md.extension', $this->item->id);
        }

        if (empty($this->item->id)) {
            JToolbarHelper::cancel('extension.cancel', 'JTOOLBAR_CANCEL');
        } else {
            JToolbarHelper::cancel('extension.cancel', 'JTOOLBAR_CLOSE');
        }
    }
}
