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

jimport('joomla.application.component.controllerform');

/**
 * Extension controller class.
 *
 * @since  1.6
 */
class Manifest2mdControllerExtension extends JControllerForm
{
    /**
     * Constructor
     *
     * @throws Exception
     */
    public function __construct()
    {
        $this->view_list = 'extensions';
        parent::__construct();
    }

    /**
     * Method to run batch operations.
     *
     * @param   object $model The model.
     *
     * @return  boolean   True if successful, false otherwise and internal error is set.
     *
     * @since   1.0.2
     */
    public function batch($model = null)
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Set the model
        /** @var ContactModelContact $model */
        $model = $this->getModel('extension', '', array());

        // Preset the redirect
        $this->setRedirect(JRoute::_('index.php?option=com_manifest2md&view=extensions' . $this->getRedirectToListAppend(), false));

        return parent::batch($model);
    }

    /**
     * Method override to check if you can add a new record.
     *
     * @param   array $data An array of input data.
     * @return  boolean
     * @since   1.0.2
     */
    protected function allowAdd($data = array())
    {
        $categoryId = ArrayHelper::getValue($data, 'catid', $this->input->getInt('filter_category_id'), 'int');
        $allow = null;

        if ($categoryId) {
            // If the category has been passed in the URL check it.
            $allow = JFactory::getUser()->authorise('core.create', $this->option . '.category.' . $categoryId);
        }

        if ($allow === null) {
            // In the absense of better information, revert to the component permissions.
            return parent::allowAdd($data);
        }

        return $allow;
    }

    /**
     * Method override to check if you can edit an existing record.
     *
     * @param   array $data An array of input data.
     * @param   string $key The name of the key for the primary key.
     * @return  boolean
     * @since   1.0.2
     */
    protected function allowEdit($data = array(), $key = 'id')
    {
        $recordId = (int)isset($data[$key]) ? $data[$key] : 0;

        // Since there is no asset tracking, fallback to the component permissions.
        if (!$recordId) {
            return parent::allowEdit($data, $key);
        }

        // Get the item.
        $item = $this->getModel()->getItem($recordId);

        // Since there is no item, return false.
        if (empty($item)) {
            return false;
        }

        $user = JFactory::getUser();

        // Check if can edit own core.edit.own.
        $canEditOwn = $user->authorise('core.edit.own', $this->option . '.category.' . (int)$item->catid) && $item->created_by == $user->id;

        // Check the category core.edit permissions.
        return $canEditOwn || $user->authorise('core.edit', $this->option . '.category.' . (int)$item->catid);
    }
}
