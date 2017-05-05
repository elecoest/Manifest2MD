<?php
/**
 * @version    CVS: 1.0.2
 * @package    Com_Manifest2md
 * @author     Emmanuel Lecoester <elecoest@gmail.com>
 * @copyright  2017 elecoest
 * @license    GNU General Public License version 2 ou version ultérieure ; Voir LICENSE.txt
 */

defined('_JEXEC') or die;

$published = $this->state->get('filter.published');
?>
<div class="modal hide fade" id="collapseModal">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&#215;</button>
        <h3><?php echo JText::_('COM_MANIFEST2MD_BATCH_OPTIONS'); ?></h3>
    </div>
    <div class="modal-body modal-batch">
        <p><?php echo JText::_('COM_MANIFEST2MD_BATCH_TIP'); ?></p>
        <div class="row-fluid">
            <div class="control-group span6">
                <div class="controls">
                    <?php echo JHtml::_('batch.language'); ?>
                </div>
            </div>
            <div class="control-group span6">
                <div class="controls">
                    <?php echo JHtml::_('batch.access'); ?>
                </div>
            </div>
        </div>
        <div class="row-fluid">
            <?php if ($published >= 0) : ?>
                <div class="control-group span6">
                    <div class="controls">
                        <?php echo JHtml::_('batch.item', 'com_contact'); ?>
                    </div>
                </div>
            <?php endif; ?>
            <div class="control-group span6">
                <div class="controls">
                    <?php echo JHtml::_('batch.tag'); ?>
                </div>
            </div>
            <div class="row-fluid">
                <div class="control-group">
                    <div class="controls">
                        <?php echo JHtml::_('batch.user'); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn" type="button" onclick="document.getElementById('batch-category-id').value=''"
                    data-dismiss="modal">
                <?php echo JText::_('JCANCEL'); ?>
            </button>
            <button class="btn btn-primary" type="submit" onclick="Joomla.submitbutton('extension.batch');">
                <?php echo JText::_('JGLOBAL_BATCH_PROCESS'); ?>
            </button>
        </div>
    </div>
