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

//use Joomla\Registry\Registry;
//use Joomla\String\StringHelper;
//use Joomla\Utilities\ArrayHelper;

jimport('joomla.application.component.modeladmin');

/**
 * Manifest2md model.
 *
 * @since  1.6
 */
class Manifest2mdModelExtension extends JModelAdmin
{
    /**
     * @var    string    Alias to manage history control
     * @since   3.2
     */
    public $typeAlias = 'com_manifest2md.extension';
    /**
     * The context used for the associations table
     *
     * @var    string
     * @since  3.4.4
     */
    protected $associationsContext = 'com_manifest2md.item';
    /**
     * @var      string    The prefix to use with controller messages.
     * @since    1.6
     */
    protected $text_prefix = 'COM_MANIFEST2MD';
    /**
     * @var null  Item data
     * @since  1.6
     */
    protected $item = null;

    /**
     * Batch copy/move command. If set to false, the batch copy/move command is not supported
     *
     * @var  string
     */
    protected $batch_copymove = 'category_id';

    /**
     * Allowed batch commands
     *
     * @var array
     */
    protected $batch_commands = array(
        'assetgroup_id' => 'batchAccess',
        'language_id' => 'batchLanguage',
        'tag' => 'batchTag',
        'user_id' => 'batchUser'
    );

    /**
     * Method to get the record form.
     *
     * @param   array $data An optional array of data for the form to interogate.
     * @param   boolean $loadData True if the form is to load its own data (default case), false if not.
     *
     * @return  JForm  A JForm object on success, false on failure
     *
     * @since    1.6
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Initialise variables.
        //$app = JFactory::getApplication();

        // Get the form.
        $form = $this->loadForm(
            'com_manifest2md.extension', 'extension',
            array('control' => 'jform',
                'load_data' => $loadData
            )
        );

        if (empty($form)) {
            return false;
        }

        return $form;
    }

    /**
     * Method to duplicate an Extension
     *
     * @param   array &$pks An array of primary key IDs.
     *
     * @return  boolean  True if successful.
     *
     * @throws  Exception
     */
    public function duplicate(&$pks)
    {
        $user = JFactory::getUser();

        // Access checks.
        if (!$user->authorise('core.create', 'com_manifest2md')) {
            throw new Exception(JText::_('JERROR_CORE_CREATE_NOT_PERMITTED'));
        }

        $dispatcher = JEventDispatcher::getInstance();
        $context = $this->option . '.' . $this->name;

        // Include the plugins for the save events.
        JPluginHelper::importPlugin($this->events_map['save']);

        $table = $this->getTable();

        foreach ($pks as $pk) {
            if ($table->load($pk, true)) {
                // Reset the id to create a new record.
                $table->id = 0;

                if (!$table->check()) {
                    throw new Exception($table->getError());
                }

                // Trigger the before save event.
                $result = $dispatcher->trigger($this->event_before_save, array($context, &$table, true));

                if (in_array(false, $result, true) || !$table->store()) {
                    throw new Exception($table->getError());
                }

                // Trigger the after save event.
                $dispatcher->trigger($this->event_after_save, array($context, &$table, true));
            } else {
                throw new Exception($table->getError());
            }
        }

        // Clean cache
        $this->cleanCache();

        return true;
    }

    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   string $type The table type to instantiate
     * @param   string $prefix A prefix for the table class name. Optional.
     * @param   array $config Configuration array for model. Optional.
     *
     * @return    JTable    A database object
     *
     * @since    1.6
     */
    public function getTable($type = 'Extension', $prefix = 'Manifest2mdTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Discover
     *
     * @return mixed Array of plugins on success, false on failure.
     */
    public function Discover()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select("`name`,`type`,`element`,`folder`, `state`");
        $query->from('`#__extensions` AS a');
        $query->where("`type` in ('component','module','plugin')");
        $query->where("(`name`,`type`,`element`) not in (select `name`,`type`,`element` from #__manifest2md_extensions)");

        // $db->setQuery("delete from #__manifest2md_extensions");
        // $db->execute();

        $db->setQuery($query);
        $loaddb = $db->loadObjectList();

        $table = $this->getTable('extension');

        foreach ((array )$loaddb as $item) {
            $table->id = 0;
            $table->state = $item->state;
            $table->name = $item->name;
            $table->type = $item->type;
            $table->element = $item->element;
            $table->folder = $item->folder;
            $table->identifier = 'nc';
            $table->doc_element = 'config';
            $table->specific_home = '';
            $table->catid = 1;
            $table->check();
            $table->store();
        }

        return $msg;
    }

    /**
     * Batch copy items to a new category or current.
     *
     * @param   integer $value The new category.
     * @param   array $pks An array of row IDs.
     * @param   array $contexts An array of item contexts.
     *
     * @return  mixed  An array of new IDs on success, boolean false on failure.
     *
     * @since   11.1
     */
    protected function batchCopy($value, $pks, $contexts)
    {
        $categoryId = (int)$value;

        $newIds = array();

        if (!parent::checkCategoryId($categoryId)) {
            return false;
        }

        // Parent exists so we proceed
        while (!empty($pks)) {
            // Pop the first ID off the stack
            $pk = array_shift($pks);

            $this->table->reset();

            // Check that the row actually exists
            if (!$this->table->load($pk)) {
                if ($error = $this->table->getError()) {
                    // Fatal error
                    $this->setError($error);

                    return false;
                } else {
                    // Not fatal error
                    $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_BATCH_MOVE_ROW_NOT_FOUND', $pk));
                    continue;
                }
            }

            // Reset the ID because we are making a copy
            $this->table->id = 0;

            // New category ID
            $this->table->catid = $categoryId;

            // Unpublish because we are making a copy
            $this->table->state = 0;

            // Check the row.
            if (!$this->table->check()) {
                $this->setError($this->table->getError());
                return false;
            }

            // Store the row.
            if (!$this->table->store()) {
                $this->setError($this->table->getError());
                return false;
            }

            // Get the new item ID
            $newId = $this->table->get('id');

            // Add the new ID to the array
            $newIds[$pk] = $newId;
        }

        // Clean the cache
        $this->cleanCache();

        return $newIds;
    }

    /**
     * Batch change a linked user.
     *
     * @param   integer $value The new value matching a User ID.
     * @param   array $pks An array of row IDs.
     * @param   array $contexts An array of item contexts.
     *
     * @return  boolean  True if successful, false otherwise and internal error is set.
     *
     * @since   2.5
     */
    protected function batchUser($value, $pks, $contexts)
    {
        foreach ($pks as $pk) {
            if ($this->user->authorise('core.edit', $contexts[$pk])) {
                $this->table->reset();
                $this->table->load($pk);
                $this->table->user_id = (int)$value;

                $this->createTagsHelper($this->tagsObserver, $this->type, $pk, $this->typeAlias, $this->table);

                if (!$this->table->store()) {
                    $this->setError($this->table->getError());

                    return false;
                }
            } else {
                $this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_EDIT'));

                return false;
            }
        }

        // Clean the cache
        $this->cleanCache();

        return true;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return   mixed  The data for the form.
     *
     * @since    1.6
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_manifest2md.edit.extension.data', array());

        if (empty($data)) {
            if ($this->item === null) {
                $this->item = $this->getItem();
            }

            $data = $this->item;
        }

        return $data;
    }

    /**
     * Method to get a single record.
     *
     * @param   integer $pk The id of the primary key.
     *
     * @return  mixed    Object on success, false on failure.
     *
     * @since    1.6
     */
    public function getItem($pk = null)
    {
        if ($item = parent::getItem($pk)) {
            // Do any procesing on fields here if needed
        }

        return $item;
    }

    /**
     * Prepare and sanitise the table prior to saving.
     *
     * @param   JTable $table Table Object
     *
     * @return void
     *
     * @since    1.6
     */
    protected function prepareTable($table)
    {
        jimport('joomla.filter.output');

        if (empty($table->id)) {
            // Set ordering to the last item if not set
            if (@$table->ordering === '') {
                $db = JFactory::getDbo();
                $db->setQuery('SELECT MAX(ordering) FROM #__manifest2md_extensions');
                $max = $db->loadResult();
                $table->ordering = $max + 1;
            }
        }
    }
}
