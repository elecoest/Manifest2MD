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
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see        JController
	 * @since      1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'ordering', 'a.ordering',
				'state', 'a.state',
				'created_by', 'a.created_by',
				'modified_by', 'a.modified_by',
				'name', 'a.name',
				'type', 'a.type',
				'element', 'a.element',
				'folder', 'a.folder',
				'identifier', 'a.identifier',
				'doc_element', 'a.doc_element',
				'specific_home', 'a.specific_home',
				'category', 'a.category',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   Elements order
	 * @param   string  $direction  Order direction
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since    1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app  = JFactory::getApplication();
		$list = $app->getUserState($this->context . '.list');

		$ordering  = isset($list['filter_order'])     ? $list['filter_order']     : null;
		$direction = isset($list['filter_order_Dir']) ? $list['filter_order_Dir'] : null;

		$list['limit']     = (int) JFactory::getConfig()->get('list_limit', 20);
		$list['start']     = $app->input->getInt('start', 0);
		$list['ordering']  = $ordering;
		$list['direction'] = $direction;

		$app->setUserState($this->context . '.list', $list);
		$app->input->set('list', null);

		// List state information.
		parent::populateState($ordering, $direction);

        $app = JFactory::getApplication();

        $ordering  = $app->getUserStateFromRequest($this->context . '.ordercol', 'filter_order', $ordering);
        $direction = $app->getUserStateFromRequest($this->context . '.orderdirn', 'filter_order_Dir', $ordering);

        $this->setState('list.ordering', $ordering);
        $this->setState('list.direction', $direction);

        $start = $app->getUserStateFromRequest($this->context . '.limitstart', 'limitstart', 0, 'int');
        $limit = $app->getUserStateFromRequest($this->context . '.limit', 'limit', 0, 'int');

        if ($limit == 0)
        {
            $limit = $app->get('list_limit', 0);
        }

        $this->setState('list.limit', $limit);
        $this->setState('list.start', $start);
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
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query
			->select(
				$this->getState(
					'list.select', 'DISTINCT a.*'
				)
			);

		$query->from('`#__manifest2md_extensions` AS a');
		
		// Join over the users for the checked out user.
		$query->select('uc.name AS uEditor');
		$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

		// Join over the created by field 'created_by'
		$query->join('LEFT', '#__users AS created_by ON created_by.id = a.created_by');

		// Join over the created by field 'modified_by'
		$query->join('LEFT', '#__users AS modified_by ON modified_by.id = a.modified_by');
		// Join over the category 'category'
		$query->select('categories_2701718.title AS category');
		$query->join('LEFT', '#__categories AS categories_2701718 ON categories_2701718.id = a.category');
		
		if (!JFactory::getUser()->authorise('core.edit', 'com_manifest2md'))
		{
			$query->where('a.state = 1');
		}

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				$query->where('( a.name LIKE ' . $search . '  OR  a.element LIKE ' . $search . '  OR  a.folder LIKE ' . $search . '  OR  a.specific_home LIKE ' . $search . '  OR categories_2701718.title LIKE ' . $search . ' )');
			}
		}
		

		// Filtering type
		$filter_type = $this->state->get("filter.type");
		if ($filter_type != '') {
			$query->where("a.type = '".$db->escape($filter_type)."'");
		}

		// Filtering identifier
		$filter_identifier = $this->state->get("filter.identifier");
		if ($filter_identifier != '') {
			$query->where("a.identifier = '".$db->escape($filter_identifier)."'");
		}

		// Filtering doc_element
		$filter_doc_element = $this->state->get("filter.doc_element");
		if ($filter_doc_element != '') {
			$query->where("a.doc_element = '".$db->escape($filter_doc_element)."'");
		}

		// Filtering category
		$filter_category = $this->state->get("filter.category");
		if ($filter_category)
		{
			$query->where("a.category = '".$db->escape($filter_category)."'");
		}

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering', 'ordering');
		$orderDirn = $this->state->get('list.direction', 'asc');

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		return $query;
	}

	/**
	 * Method to get an array of data items
	 *
	 * @return  mixed An array of data on success, false on failure.
	 */
	public function getItems()
	{
		$items = parent::getItems();
		
		foreach ($items as $item)
		{

					$item->type = JText::_('COM_MANIFEST2MD_EXTENSIONS_TYPE_OPTION_' . strtoupper($item->type));
					$item->identifier = JText::_('COM_MANIFEST2MD_EXTENSIONS_IDENTIFIER_OPTION_' . strtoupper($item->identifier));
					$item->doc_element = JText::_('COM_MANIFEST2MD_EXTENSIONS_DOC_ELEMENT_OPTION_' . strtoupper($item->doc_element));

			if (isset($item->category))
			{

				// Get the title of that particular template
					$title = Manifest2mdHelpersManifest2md::getCategoryNameByCategoryId($item->category);

					// Finally replace the data object with proper information
					$item->category = !empty($title) ? $title : $item->category;
				}
		}

		return $items;
	}

	/**
	 * Overrides the default function to check Date fields format, identified by
	 * "_dateformat" suffix, and erases the field if it's not correct.
	 *
	 * @return void
	 */
	protected function loadFormData()
	{
		$app              = JFactory::getApplication();
		$filters          = $app->getUserState($this->context . '.filter', array());
		$error_dateformat = false;

		foreach ($filters as $key => $value)
		{
			if (strpos($key, '_dateformat') && !empty($value) && $this->isValidDate($value) == null)
			{
				$filters[$key]    = '';
				$error_dateformat = true;
			}
		}

		if ($error_dateformat)
		{
			$app->enqueueMessage(JText::_("COM_MANIFEST2MD_SEARCH_FILTER_DATE_FORMAT"), "warning");
			$app->setUserState($this->context . '.filter', $filters);
		}

		return parent::loadFormData();
	}

	/**
	 * Checks if a given date is valid and in a specified format (YYYY-MM-DD)
	 *
	 * @param   string  $date  Date to be checked
	 *
	 * @return bool
	 */
	private function isValidDate($date)
	{
		$date = str_replace('/', '-', $date);
		return (date_create($date)) ? JFactory::getDate($date)->format("Y-m-d") : null;
	}
}
