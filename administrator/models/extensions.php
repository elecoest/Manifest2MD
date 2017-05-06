<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Manifest2md
 * @author     Emmanuel Lecoester <elecoest@gmail.com>
 * @copyright  2017 elecoest
 * @license    GNU General Public License version 2 ou version ultérieure ; Voir LICENSE.txt
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Manifest2md records.
 *
 * @since  1.6
 */
class Manifest2mdModelExtensions extends JModelList
{
    /**
     * Constructor.
     *
     * @param   array $config An optional associative array of configuration settings.
     *
     * @see        JController
     * @since      1.6
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'a.`id`',
                'ordering', 'a.`ordering`',
                'state', 'a.`state`',
                'created_by', 'a.`created_by`',
                'modified_by', 'a.`modified_by`',
                'name', 'a.`name`',
                'type', 'a.`type`',
                'element', 'a.`element`',
                'folder', 'a.`folder`',
                'identifier', 'a.`identifier`',
                'doc_element', 'a.`doc_element`',
                'specific_home', 'a.`specific_home`',
                'category', 'a.`catid`',
            );
        }

        parent::__construct($config);
    }

    /**
     * Get an array of data items
     *
     * @return mixed Array of data items on success, false on failure.
     */
    public function getItems()
    {
        $items = parent::getItems();

        foreach ($items as $oneItem) {
            $oneItem->type = JText::_('COM_MANIFEST2MD_EXTENSIONS_TYPE_OPTION_' . strtoupper($oneItem->type));
            $oneItem->identifier = JText::_('COM_MANIFEST2MD_EXTENSIONS_IDENTIFIER_OPTION_' . strtoupper($oneItem->identifier));
            $oneItem->doc_element = JText::_('COM_MANIFEST2MD_EXTENSIONS_DOC_ELEMENT_OPTION_' . strtoupper($oneItem->doc_element));
        }
        return $items;
    }

    /**
     * Get an array of data items like config
     *
     * @return mixed Array of data items on success, false on failure.
     */
    public function getComponentsConfig()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select("a.*");
        $query->from('`#__manifest2md_extensions` AS a');
        $query->where("state = 1");
        $query->where("doc_element = 'config'");
        $query->where("type = 'component'");

        $query->select('`category`.alias AS `category`');
        $query->join('LEFT', '#__categories AS `category` ON `category`.id = a.`catid`');

        $db->setQuery($query);
        $loaddb = $db->loadObjectList();

        return ((array )$loaddb);
    }

    /**
     * Get an array of modules
     *
     * @return mixed Array of modules on success, false on failure.
     */
    public function getModules()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select("a.*");
        $query->from('`#__manifest2md_extensions` AS a');
        $query->where("state = 1");
        $query->where("type = 'module'");

        $query->select('`category`.alias AS `category`');
        $query->join('LEFT', '#__categories AS `category` ON `category`.id = a.`catid`');

        $db->setQuery($query);
        $loaddb = $db->loadObjectList();

        return ((array )$loaddb);
    }

    /**
     * Get an array of plugins
     *
     * @return mixed Array of plugins on success, false on failure.
     */
    public function getPlugins()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select("a.*");
        $query->from('`#__manifest2md_extensions` AS a');
        $query->where("state = 1");
        $query->where("type = 'plugin'");

        $query->select('`category`.alias AS `category`');
        $query->join('LEFT', '#__categories AS `category` ON `category`.id = a.`catid`');

        $db->setQuery($query);
        $loaddb = $db->loadObjectList();

        return ((array )$loaddb);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @param   string $ordering Elements order
     * @param   string $direction Order direction
     *
     * @return void
     *
     * @throws Exception
     */
    protected function populateState($ordering = null, $direction = null)
    {
        // Initialise variables.
        $app = JFactory::getApplication('administrator');

        // Load the filter state.
        $search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $published = $app->getUserStateFromRequest($this->context . '.filter.state', 'filter_published', '', 'string');
        $this->setState('filter.state', $published);
        // Filtering type
        $this->setState('filter.type', $app->getUserStateFromRequest($this->context . '.filter.type', 'filter_type', '', 'string'));

        // Filtering identifier
        $this->setState('filter.identifier', $app->getUserStateFromRequest($this->context . '.filter.identifier', 'filter_identifier', '', 'string'));

        // Filtering doc_element
        $this->setState('filter.doc_element', $app->getUserStateFromRequest($this->context . '.filter.doc_element', 'filter_doc_element', '', 'string'));

        // Filtering category
        $this->setState('filter.category', $app->getUserStateFromRequest($this->context . '.filter.category', 'filter_category', '', 'string'));


        // Load the parameters.
        $params = JComponentHelper::getParams('com_manifest2md');
        $this->setState('params', $params);

        // List state information.
        parent::populateState('a.name', 'asc');
    }

    /**
     * Method to get a store id based on model configuration state.
     *
     * This is necessary because the model is used by the component and
     * different modules that might need different sets of data or different
     * ordering requirements.
     *
     * @param   string $id A prefix for the store id.
     *
     * @return   string A store id.
     *
     * @since    1.6
     */
    protected function getStoreId($id = '')
    {
        // Compile the store id.
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.state');

        return parent::getStoreId($id);
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return   JDatabaseQuery
     *
     * @since    1.6
     */
    protected function getListQuery()
    {
        // Create a new query object.
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
            $this->getState(
                'list.select', 'DISTINCT a.*'
            )
        );
        $query->from('`#__manifest2md_extensions` AS a');

        // Join over the users for the checked out user
        $query->select("uc.name AS uEditor");
        $query->join("LEFT", "#__users AS uc ON uc.id=a.checked_out");

        // Join over the user field 'created_by'
        $query->select('`created_by`.name AS `created_by`');
        $query->join('LEFT', '#__users AS `created_by` ON `created_by`.id = a.`created_by`');

        // Join over the user field 'modified_by'
        $query->select('`modified_by`.name AS `modified_by`');
        $query->join('LEFT', '#__users AS `modified_by` ON `modified_by`.id = a.`modified_by`');
        // Join over the category 'category'
        $query->select('`category`.title AS `category`');
        $query->join('LEFT', '#__categories AS `category` ON `category`.id = a.`catid`');

        // Filter by published state
        $published = $this->getState('filter.state');

        if (is_numeric($published)) {
            $query->where('a.state = ' . (int)$published);
        } elseif ($published === '') {
            $query->where('(a.state IN (0, 1))');
        }

        // Filter by search in title
        $search = $this->getState('filter.search');

        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = ' . (int)substr($search, 3));
            } else {
                $search = $db->quote('%' . $db->escape($search, true) . '%');
                $query->where('( a.name LIKE ' . $search . '  OR  a.type LIKE ' . $search . '  OR  a.element LIKE ' . $search . '  OR  a.folder LIKE ' . $search . '  OR  a.identifier LIKE ' . $search . '  OR  a.doc_element LIKE ' . $search . '  OR  a.specific_home LIKE ' . $search . '  OR  a.catid LIKE ' . $search . ' )');
            }
        }

        //Filtering type
        $filter_type = $this->state->get("filter.type");
        if ($filter_type !== null && !empty($filter_type)) {
            $query->where("a.`type` = '" . $db->escape($filter_type) . "'");
        }

        //Filtering identifier
        $filter_identifier = $this->state->get("filter.identifier");
        if ($filter_identifier !== null && !empty($filter_identifier)) {
            $query->where("a.`identifier` = '" . $db->escape($filter_identifier) . "'");
        }

        //Filtering doc_element
        $filter_doc_element = $this->state->get("filter.doc_element");
        if ($filter_doc_element !== null && !empty($filter_doc_element)) {
            $query->where("a.`doc_element` = '" . $db->escape($filter_doc_element) . "'");
        }

        //Filtering category
        $filter_category = $this->state->get("filter.category");
        if ($filter_category !== null && !empty($filter_category)) {
            $query->where("a.`catid` = '" . $db->escape($filter_category) . "'");
        }
        // Add the list ordering clause.
        $orderCol = $this->state->get('list.ordering');
        $orderDirn = $this->state->get('list.direction');

        if ($orderCol && $orderDirn) {
            $query->order($db->escape($orderCol . ' ' . $orderDirn));
        }

        return $query;
    }
}
