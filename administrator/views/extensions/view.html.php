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
 * View class for a list of Manifest2md.
 *
 * @since  1.6
 */
class Manifest2mdViewExtensions extends JViewLegacy
{
    protected $items;

    protected $pagination;

    protected $state;

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
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }

        Manifest2mdHelpersManifest2mdadmin::addSubmenu('extensions');

        $this->addToolbar();

        $this->sidebar = JHtmlSidebar::render();

        if (empty($this->items)) {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_MANIFEST2MD_NO_EXTENSIONS'), 'warning');
        }

        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return void
     *
     * @since    1.6
     */
    protected function addToolbar()
    {
        $state = $this->get('State');
        $canDo = Manifest2mdHelpersManifest2mdadmin::getActions();

        JToolBarHelper::title(JText::_('COM_MANIFEST2MD_TITLE_EXTENSIONS'), 'extensions.png');

        // Check if the form exists before showing the add/edit buttons
        $formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/extension';

        if (file_exists($formPath)) {
            if ($canDo->get('core.create')) {
                JToolBarHelper::addNew('extension.add', 'JTOOLBAR_NEW');

                if (isset($this->items[0])) {
                    JToolbarHelper::custom('extensions.duplicate', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
                }
            }

            if ($canDo->get('core.edit') && isset($this->items[0])) {
                JToolBarHelper::editList('extension.edit', 'JTOOLBAR_EDIT');
            }
        }

        if ($canDo->get('core.edit.state')) {
            if (isset($this->items[0]->state)) {
                JToolBarHelper::divider();
                JToolBarHelper::custom('extensions.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
                JToolBarHelper::custom('extensions.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
            } elseif (isset($this->items[0])) {
                // If this component does not use state then show a direct delete button as we can not trash
                JToolBarHelper::deleteList('', 'extensions.delete', 'JTOOLBAR_DELETE');
            }

            if (isset($this->items[0]->state)) {
                JToolBarHelper::divider();
                JToolBarHelper::archiveList('extensions.archive', 'JTOOLBAR_ARCHIVE');
            }

            if (isset($this->items[0]->checked_out)) {
                JToolBarHelper::custom('extensions.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
            }
        }

        // Show trash and delete for components that uses the state field
        if (isset($this->items[0]->state)) {
            if ($state->get('filter.state') == -2 && $canDo->get('core.delete')) {
                JToolBarHelper::deleteList('', 'extensions.delete', 'JTOOLBAR_EMPTY_TRASH');
                JToolBarHelper::divider();
            } elseif ($canDo->get('core.edit.state')) {
                JToolBarHelper::trash('extensions.trash', 'JTOOLBAR_TRASH');
                JToolBarHelper::divider();
            }
        }
        JToolBarHelper::custom('extensions.makemd', 'cogs.png', 'cogs.png', 'JTOOLBAR_MAKEMD', false);
        JToolBarHelper::custom('extensions.discover', 'cogs.png', 'cogs.png', 'JTOOLBAR_DISCOVER', false);
        if ($canDo->get('core.admin')) {
            JToolBarHelper::preferences('com_manifest2md');
        }

        // Set sidebar action - New in 3.0
        JHtmlSidebar::setAction('index.php?option=com_manifest2md&view=extensions');

        $this->extra_sidebar = '';
        JHtmlSidebar::addFilter(

            JText::_('JOPTION_SELECT_PUBLISHED'),

            'filter_published',

            JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.state'), true)

        );
        //Filter for the field type
        $select_label = JText::sprintf('COM_MANIFEST2MD_FILTER_SELECT_LABEL', 'Type');
        $options = array();
        $options[0] = new stdClass();
        $options[0]->value = "component";
        $options[0]->text = "component";
        $options[1] = new stdClass();
        $options[1]->value = "module";
        $options[1]->text = "module";
        $options[2] = new stdClass();
        $options[2]->value = "plugin";
        $options[2]->text = "plugin";
        JHtmlSidebar::addFilter(
            $select_label,
            'filter_type',
            JHtml::_('select.options', $options, "value", "text", $this->state->get('filter.type'), true)
        );

        //Filter for the field identifier
        $select_label = JText::sprintf('COM_MANIFEST2MD_FILTER_SELECT_LABEL', 'Identifier');
        $options = array();
        $options[0] = new stdClass();
        $options[0]->value = "nc";
        $options[0]->text = "-";
        $options[1] = new stdClass();
        $options[1]->value = "both";
        $options[1]->text = "both";
        $options[2] = new stdClass();
        $options[2]->value = "site";
        $options[2]->text = "site";
        $options[3] = new stdClass();
        $options[3]->value = "administrator";
        $options[3]->text = "administrator";
        JHtmlSidebar::addFilter(
            $select_label,
            'filter_identifier',
            JHtml::_('select.options', $options, "value", "text", $this->state->get('filter.identifier'), true)
        );

        //Filter for the field doc_element
        $select_label = JText::sprintf('COM_MANIFEST2MD_FILTER_SELECT_LABEL', 'Doc Element');
        $options = array();
        $options[0] = new stdClass();
        $options[0]->value = "all";
        $options[0]->text = "all";
        $options[1] = new stdClass();
        $options[1]->value = "config";
        $options[1]->text = "config";
        $options[2] = new stdClass();
        $options[2]->value = "Items";
        $options[2]->text = "Items";
        $options[3] = new stdClass();
        $options[3]->value = "Views";
        $options[3]->text = "Views";
        JHtmlSidebar::addFilter(
            $select_label,
            'filter_doc_element',
            JHtml::_('select.options', $options, "value", "text", $this->state->get('filter.doc_element'), true)
        );

        JHtmlSidebar::addFilter(
            JText::_("JOPTION_SELECT_CATEGORY"),
            'filter_category',
            JHtml::_('select.options', JHtml::_('category.options', 'com_manifest2md'), "value", "text", $this->state->get('filter.category'))
        );

    }

    /**
     * Method to order fields
     *
     * @return void
     */
    protected function getSortFields()
    {
        return array(
            'a.`id`' => JText::_('JGRID_HEADING_ID'),
            'a.`ordering`' => JText::_('JGRID_HEADING_ORDERING'),
            'a.`state`' => JText::_('JSTATUS'),
            'a.`name`' => JText::_('COM_MANIFEST2MD_EXTENSIONS_NAME'),
            'a.`type`' => JText::_('COM_MANIFEST2MD_EXTENSIONS_TYPE'),
            'a.`element`' => JText::_('COM_MANIFEST2MD_EXTENSIONS_ELEMENT'),
            'a.`folder`' => JText::_('COM_MANIFEST2MD_EXTENSIONS_FOLDER'),
            'a.`identifier`' => JText::_('COM_MANIFEST2MD_EXTENSIONS_IDENTIFIER'),
            'a.`doc_element`' => JText::_('COM_MANIFEST2MD_EXTENSIONS_DOC_ELEMENT'),
            'a.`specific_home`' => JText::_('COM_MANIFEST2MD_EXTENSIONS_SPECIFIC_HOME'),
            'a.`category`' => JText::_('COM_MANIFEST2MD_EXTENSIONS_CATEGORY'),
        );
    }
}
