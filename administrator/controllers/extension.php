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
}
