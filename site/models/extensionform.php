<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Manifest2md
 * @author     Emmanuel Lecoester <elecoest@gmail.com>
 * @copyright  2017 elecoest
 * @license    GNU General Public License version 2 ou version ultérieure ; Voir LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modelform');
jimport('joomla.event.dispatcher');

use Joomla\Utilities\ArrayHelper;

/**
 * Manifest2md model.
 *
 * @since  1.6
 */
class Manifest2mdModelExtensionForm extends JModelForm
{
    private $item = null;

    /**
     * Get an item by alias
     *
     * @param   string $alias Alias string
     *
     * @return int Element id
     */
    public function getItemIdByAlias($alias)
    {
        $table = $this->getTable();
        $properties = $table->getProperties();

        if (!in_array('alias', $properties)) {
            return null;
        }

        $table->load(array('alias' => $alias));

        return $table->id;
    }

    /**
     * Method to get the table
     *
     * @param   string $type Name of the JTable class
     * @param   string $prefix Optional prefix for the table class name
     * @param   array $config Optional configuration array for JTable object
     *
     * @return  JTable|boolean JTable if found, boolean false on failure
     */
    public function getTable($type = 'Extension', $prefix = 'Manifest2mdTable', $config = array())
    {
        $this->addTablePath(JPATH_ADMINISTRATOR . '/components/com_manifest2md/tables');

        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to check in an item.
     *
     * @param   integer $id The id of the row to check out.
     *
     * @return  boolean True on success, false on failure.
     *
     * @since    1.6
     */
    public function checkin($id = null)
    {
        // Get the id.
        $id = (!empty($id)) ? $id : (int)$this->getState('extension.id');

        if ($id) {
            // Initialise the table
            $table = $this->getTable();

            // Attempt to check the row in.
            if (method_exists($table, 'checkin')) {
                if (!$table->checkin($id)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Method to check out an item for editing.
     *
     * @param   integer $id The id of the row to check out.
     *
     * @return  boolean True on success, false on failure.
     *
     * @since    1.6
     */
    public function checkout($id = null)
    {
        // Get the user id.
        $id = (!empty($id)) ? $id : (int)$this->getState('extension.id');

        if ($id) {
            // Initialise the table
            $table = $this->getTable();

            // Get the current user object.
            $user = JFactory::getUser();

            // Attempt to check the row out.
            if (method_exists($table, 'checkout')) {
                if (!$table->checkout($user->get('id'), $id)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Method to get the profile form.
     *
     * The base form is loaded from XML
     *
     * @param   array $data An optional array of data for the form to interogate.
     * @param   boolean $loadData True if the form is to load its own data (default case), false if not.
     *
     * @return    JForm    A JForm object on success, false on failure
     *
     * @since    1.6
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm('com_manifest2md.extension', 'extensionform', array(
                'control' => 'jform',
                'load_data' => $loadData
            )
        );

        if (empty($form)) {
            return false;
        }

        return $form;
    }

    /**
     * Method to save the form data.
     *
     * @param   array $data The form data
     *
     * @return bool
     *
     * @throws Exception
     * @since 1.6
     */
    public function save($data)
    {
        $id = (!empty($data['id'])) ? $data['id'] : (int)$this->getState('extension.id');
        $state = (!empty($data['state'])) ? 1 : 0;
        $user = JFactory::getUser();

        if ($id) {
            // Check the user can edit this item
            $authorised = $user->authorise('core.edit', 'com_manifest2md.extension.' . $id) || $authorised = $user->authorise('core.edit.own', 'com_manifest2md.extension.' . $id);
        } else {
            // Check the user can create new items in this section
            $authorised = $user->authorise('core.create', 'com_manifest2md');
        }

        if ($authorised !== true) {
            throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 403);
        }

        $table = $this->getTable();

        if ($table->save($data) === true) {
            return $table->id;
        } else {
            return false;
        }
    }

    /**
     * Method to delete data
     *
     * @param   int $pk Item primary key
     *
     * @return  int  The id of the deleted item
     *
     * @throws Exception
     *
     * @since 1.6
     */
    public function delete($pk)
    {
        $user = JFactory::getUser();

        if (empty($pk)) {
            $pk = (int)$this->getState('extension.id');
        }

        if ($pk == 0 || $this->getData($pk) == null) {
            throw new Exception(JText::_('COM_MANIFEST2MD_ITEM_DOESNT_EXIST'), 404);
        }

        if ($user->authorise('core.delete', 'com_manifest2md.extension.' . $id) !== true) {
            throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 403);
        }

        $table = $this->getTable();

        if ($table->delete($pk) !== true) {
            throw new Exception(JText::_('JERROR_FAILED'), 501);
        }

        return $pk;
    }

    /**
     * Check if data can be saved
     *
     * @return bool
     */
    public function getCanSave()
    {
        $table = $this->getTable();

        return $table !== false;
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @return void
     *
     * @since  1.6
     */
    protected function populateState()
    {
        $app = JFactory::getApplication('com_manifest2md');

        // Load state from the request userState on edit or from the passed variable on default
        if (JFactory::getApplication()->input->get('layout') == 'edit') {
            $id = JFactory::getApplication()->getUserState('com_manifest2md.edit.extension.id');
        } else {
            $id = JFactory::getApplication()->input->get('id');
            JFactory::getApplication()->setUserState('com_manifest2md.edit.extension.id', $id);
        }

        $this->setState('extension.id', $id);

        // Load the parameters.
        $params = $app->getParams();
        $params_array = $params->toArray();

        if (isset($params_array['item_id'])) {
            $this->setState('extension.id', $params_array['item_id']);
        }

        $this->setState('params', $params);
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return    mixed    The data for the form.
     *
     * @since    1.6
     */
    protected function loadFormData()
    {
        $data = JFactory::getApplication()->getUserState('com_manifest2md.edit.extension.data', array());

        if (empty($data)) {
            $data = $this->getData();
        }


        return $data;
    }

    /**
     * Method to get an ojbect.
     *
     * @param   integer $id The id of the object to get.
     *
     * @return Object|boolean Object on success, false on failure.
     *
     * @throws Exception
     */
    public function &getData($id = null)
    {
        if ($this->item === null) {
            $this->item = false;

            if (empty($id)) {
                $id = $this->getState('extension.id');
            }

            // Get a level row instance.
            $table = $this->getTable();

            // Attempt to load the row.
            if ($table !== false && $table->load($id)) {
                $user = JFactory::getUser();
                $id = $table->id;

                if ($id) {
                    $canEdit = $user->authorise('core.edit', 'com_manifest2md.extension.' . $id) || $user->authorise('core.create', 'com_manifest2md.extension.' . $id);
                } else {
                    $canEdit = $user->authorise('core.edit', 'com_manifest2md') || $user->authorise('core.create', 'com_manifest2md');
                }

                if (!$canEdit && $user->authorise('core.edit.own', 'com_manifest2md.extension.' . $id)) {
                    $canEdit = $user->id == $table->created_by;
                }

                if (!$canEdit) {
                    throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 500);
                }

                // Check published state.
                if ($published = $this->getState('filter.published')) {
                    if (isset($table->state) && $table->state != $published) {
                        return $this->item;
                    }
                }

                // Convert the JTable to a clean JObject.
                $properties = $table->getProperties(1);
                $this->item = ArrayHelper::toObject($properties, 'JObject');
            }
        }

        return $this->item;
    }

}
