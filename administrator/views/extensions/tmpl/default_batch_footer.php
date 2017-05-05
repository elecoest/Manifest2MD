<?php
/**
 * @version    CVS: 1.0.2
 * @package    Com_Manifest2md
 * @author     Emmanuel Lecoester <elecoest@gmail.com>
 * @copyright  2017 elecoest
 * @license    GNU General Public License version 2 ou version ultérieure ; Voir LICENSE.txt
 */
defined('_JEXEC') or die;

?>
<a class="btn" type="button" onclick="document.getElementById('batch-category-id').value=''" data-dismiss="modal">
    <?php echo JText::_('JCANCEL'); ?>
</a>
<button class="btn btn-success" type="submit" onclick="Joomla.submitbutton('extension.batch');">
    <?php echo JText::_('JGLOBAL_BATCH_PROCESS'); ?>
</button>