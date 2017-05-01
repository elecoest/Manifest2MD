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

jimport('joomla.application.component.controlleradmin');

use Joomla\Utilities\ArrayHelper;

/**
 * Extensions list controller class.
 *
 * @since  1.6
 */
class Manifest2mdControllerExtensions extends JControllerAdmin
{
	/**
	 * Method to clone existing Extensions
	 *
	 * @return void
	 */
	public function duplicate()
	{
		// Check for request forgeries
		Jsession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Get id(s)
		$pks = $this->input->post->get('cid', array(), 'array');

		try
		{
			if (empty($pks))
			{
				throw new Exception(JText::_('COM_MANIFEST2MD_NO_ELEMENT_SELECTED'));
			}

			ArrayHelper::toInteger($pks);
			$model = $this->getModel();
			$model->duplicate($pks);
			$this->setMessage(Jtext::_('COM_MANIFEST2MD_ITEMS_SUCCESS_DUPLICATED'));
		}
		catch (Exception $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
		}

		$this->setRedirect('index.php?option=com_manifest2md&view=extensions');
	}

	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    Optional. Model name
	 * @param   string  $prefix  Optional. Class prefix
	 * @param   array   $config  Optional. Configuration array for model
	 *
	 * @return  object	The Model
	 *
	 * @since    1.6
	 */
	public function getModel($name = 'extension', $prefix = 'Manifest2mdModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}

	/**
	 * Method to save the submitted ordering values for records via AJAX.
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public function saveOrderAjax()
	{
		// Get the input
		$input = JFactory::getApplication()->input;
		$pks   = $input->post->get('cid', array(), 'array');
		$order = $input->post->get('order', array(), 'array');

		// Sanitize the input
		ArrayHelper::toInteger($pks);
		ArrayHelper::toInteger($order);

		// Get the model
		$model = $this->getModel();

		// Save the ordering
		$return = $model->saveorder($pks, $order);

		if ($return)
		{
			echo "1";
		}

		// Close the application
		JFactory::getApplication()->close();
	}

/**
     * Manifest2mdControllerMain::MakeMD()
     *
     */
    function MakeMD()
    {
        require_once(JPATH_SITE . '/administrator/components/com_manifest2md/helpers/MakeMD.php');
        $g_se_MD = new AllEventsClassMD();
		$msg = 'ca marchera' ; 
		
		$model =  $this->getModel('extensions');
        $items = $model->getComponentsConfig();
		foreach ($items as $item) {
			$msg .= '<br/>, ' . $g_se_MD->MakeMDConfig($item->element, $item->category);
		}
		
		$items = $model->getModules();
		foreach ($items as $item) {
			$msg .= '<br/>, ' . $g_se_MD->MakeMD($item->element, 'modules', '', $item->category);
		}
		
		$items = $model->getPlugins();
		foreach ($items as $item) {
			$msg .= '<br/>, ' . $g_se_MD->MakeMD($item->element, 'plugins', $item->folder, $item->category);
		}
		

        // $msg .= '<br/>, ' . $g_se_MD->MakeMDObject('com_allevents', 'activity');
       
        // $msg .= '<br/>, ' . $g_se_MD->MakeMD('aerating', 'plugins', 'allevents');
        // $msg .= '<br/>, ' . $g_se_MD->MakeMD('aesocial', 'plugins', 'allevents');
        // $msg .= '<br/>, ' . $g_se_MD->MakeMD('aevote', 'plugins', 'ajax');
        // $msg .= '<br/>, ' . $g_se_MD->MakeMD('aevote', 'plugins', 'allevents');
        // $msg .= '<br/>, ' . $g_se_MD->MakeMD('allevents', 'plugins', 'acymailing');
        // $msg .= '<br/>, ' . $g_se_MD->MakeMD('allevents', 'plugins', 'content');
        // $msg .= '<br/>, ' . $g_se_MD->MakeMD('allevents', 'plugins', 'editors-xtd');
        // $msg .= '<br/>, ' . $g_se_MD->MakeMD('allevents', 'plugins', 'finder');
        // $msg .= '<br/>, ' . $g_se_MD->MakeMD('allevents', 'plugins', 'quickicon');
        // $msg .= '<br/>, ' . $g_se_MD->MakeMD('allevents', 'plugins', 'search');
        // $msg .= '<br/>, ' . $g_se_MD->MakeMD('alleventsupdate', 'plugins', 'quickicon');
        // $msg .= '<br/>, ' . $g_se_MD->MakeMD('bycheck', 'plugins', 'payment');
        // $msg .= '<br/>, ' . $g_se_MD->MakeMD('byorder', 'plugins', 'payment');
        // $msg .= '<br/>, ' . $g_se_MD->MakeMD('cbusers', 'plugins', 'allevents');
        // $msg .= '<br/>, ' . $g_se_MD->MakeMD('default', 'views', 'activities');
        // $msg .= '<br/>, ' . $g_se_MD->MakeMD('default', 'views', 'activity');
        // $msg .= '<br/>, ' . $g_se_MD->MakeMD('default', 'views', 'agenda');
        // $msg .= '<br/>, ' . $g_se_MD->MakeMD('default', 'views', 'agendas');
        // $msg .= '<br/>, ' . $g_se_MD->MakeMD('default', 'views', 'bootstrapcalendar');
        // $msg .= '<br/>, ' . $g_se_MD->MakeMD('default', 'views', 'categories');
        // $msg .= '<br/>, ' . $g_se_MD->MakeMD('default', 'views', 'category');		
        // $msg .= '<br/>, ' . $g_se_MD->MakeMD('default', 'views', 'event');
        // $msg .= '<br/>, ' . $g_se_MD->MakeMD('default', 'views', 'eventform');
        // $msg .= '<br/>, ' . $g_se_MD->MakeMD('default', 'views', 'events');
        // $msg .= '<br/>, ' . $g_se_MD->MakeMD('default', 'views', 'fullcalendar');
        // $msg .= '<br/>, ' . $g_se_MD->MakeMD('default', 'views', 'place');
        // $msg .= '<br/>, ' . $g_se_MD->MakeMD('default', 'views', 'places');
        // $msg .= '<br/>, ' . $g_se_MD->MakeMD('default', 'views', 'public');
        // $msg .= '<br/>, ' . $g_se_MD->MakeMD('default', 'views', 'ressource');
        // $msg .= '<br/>, ' . $g_se_MD->MakeMD('default', 'views', 'section');
        // $msg .= '<br/>, ' . $g_se_MD->MakeMD('default', 'views', 'sections');
        // $msg .= '<br/>, ' . $g_se_MD->MakeMD('jcomments', 'plugins', 'allevents');
        // $msg .= '<br/>, ' . $g_se_MD->MakeMD('mod_aebanner', 'modules');
        // $msg .= '<br/>, ' . $g_se_MD->MakeMD('mod_aecalendar', 'modules');
        // $msg .= '<br/>, ' . $g_se_MD->MakeMD('mod_aecustom', 'modules');
        // $msg .= '<br/>, ' . $g_se_MD->MakeMD('mod_aedeck', 'modules');
        // $msg .= '<br/>, ' . $g_se_MD->MakeMD('mod_aedrag', 'modules');
        // $msg .= '<br/>, ' . $g_se_MD->MakeMD('mod_aefilters', 'modules');
        // $msg .= '<br/>, ' . $g_se_MD->MakeMD('mod_aefullcalendar', 'modules');
        // $msg .= '<br/>, ' . $g_se_MD->MakeMD('mod_aelist', 'modules');
        // $msg .= '<br/>, ' . $g_se_MD->MakeMD('mod_aeslide', 'modules');
        // $msg .= '<br/>, ' . $g_se_MD->MakeMD('mod_aeslider', 'modules');
        // $msg .= '<br/>, ' . $g_se_MD->MakeMD('mod_aeuikit', 'modules');
        // $msg .= '<br/>, ' . $g_se_MD->MakeMD('opengraph', 'plugins', 'allevents');
        // $msg .= '<br/>, ' . $g_se_MD->MakeMD('paypal', 'plugins', 'payment');
        // $msg .= '<br/>, ' . $g_se_MD->MakeMD('richsnippets', 'plugins', 'allevents');
        // $msg .= '<br/>, ' . $g_se_MD->MakeMD('twittercard', 'plugins', 'allevents');
        // $msg .= '<br/>, ' . $g_se_MD->MakeMD('default', 'views', 'buy');
        // $msg .= '<br/>, ' . $g_se_MD->MakeMD('aegoogle', 'plugins', 'allevents');
        // $msg .= '<br/>, ' . $g_se_MD->MakeMD('alphauserpoints', 'plugins', 'allevents');
        // $msg .= '<br/>, ' . $g_se_MD->MakeMD('cb.allevents', 'plugins', 'cb/plug_cballevents');
        // $msg .= '<br/>, '. $g_se_MD->MakeMD('default','views','orders');
        // $msg .= '<br/>, '. $g_se_MD->MakeMD('default','views','payment');

        $this->setRedirect('index.php?option=com_manifest2md', $msg);
    }
	
}
