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

$canEdit = JFactory::getUser()->authorise('core.edit', 'com_manifest2md.' . $this->item->id);

if (!$canEdit && JFactory::getUser()->authorise('core.edit.own', 'com_manifest2md' . $this->item->id))
{
	$canEdit = JFactory::getUser()->id == $this->item->created_by;
}
?>

<div class="item_fields">

	<table class="table">
		

		<tr>
			<th><?php echo JText::_('COM_MANIFEST2MD_FORM_LBL_EXTENSION_NAME'); ?></th>
			<td><?php echo $this->item->name; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_MANIFEST2MD_FORM_LBL_EXTENSION_TYPE'); ?></th>
			<td><?php echo $this->item->type; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_MANIFEST2MD_FORM_LBL_EXTENSION_ELEMENT'); ?></th>
			<td><?php echo $this->item->element; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_MANIFEST2MD_FORM_LBL_EXTENSION_FOLDER'); ?></th>
			<td><?php echo $this->item->folder; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_MANIFEST2MD_FORM_LBL_EXTENSION_IDENTIFIER'); ?></th>
			<td><?php echo $this->item->identifier; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_MANIFEST2MD_FORM_LBL_EXTENSION_DOC_ELEMENT'); ?></th>
			<td><?php echo $this->item->doc_element; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_MANIFEST2MD_FORM_LBL_EXTENSION_SPECIFIC_HOME'); ?></th>
			<td><?php echo $this->item->specific_home; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_MANIFEST2MD_FORM_LBL_EXTENSION_CATEGORY'); ?></th>
			<td><?php echo $this->item->category_title; ?></td>
		</tr>

	</table>

</div>

<?php if($canEdit && $this->item->checked_out == 0): ?>

	<a class="btn" href="<?php echo JRoute::_('index.php?option=com_manifest2md&task=extension.edit&id='.$this->item->id); ?>"><?php echo JText::_("COM_MANIFEST2MD_EDIT_ITEM"); ?></a>

<?php endif; ?>

<?php if (JFactory::getUser()->authorise('core.delete','com_manifest2md.extension.'.$this->item->id)) : ?>

	<a class="btn btn-danger" href="#deleteModal" role="button" data-toggle="modal">
		<?php echo JText::_("COM_MANIFEST2MD_DELETE_ITEM"); ?>
	</a>

	<div id="deleteModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="deleteModal" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3><?php echo JText::_('COM_MANIFEST2MD_DELETE_ITEM'); ?></h3>
		</div>
		<div class="modal-body">
			<p><?php echo JText::sprintf('COM_MANIFEST2MD_DELETE_CONFIRM', $this->item->id); ?></p>
		</div>
		<div class="modal-footer">
			<button class="btn" data-dismiss="modal">Close</button>
			<a href="<?php echo JRoute::_('index.php?option=com_manifest2md&task=extension.remove&id=' . $this->item->id, false, 2); ?>" class="btn btn-danger">
				<?php echo JText::_('COM_MANIFEST2MD_DELETE_ITEM'); ?>
			</a>
		</div>
	</div>

<?php endif; ?>