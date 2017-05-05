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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet(JUri::root() . 'media/com_manifest2md/css/form.css');
?>
<script type="text/javascript">
    js = jQuery.noConflict();
    js(document).ready(function () {

    });

    Joomla.submitbutton = function (task) {
        if (task == 'extension.cancel') {
            Joomla.submitform(task, document.getElementById('extension-form'));
        }
        else {

            if (task != 'extension.cancel' && document.formvalidator.isValid(document.id('extension-form'))) {

                Joomla.submitform(task, document.getElementById('extension-form'));
            }
            else {
                alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
            }
        }
    }
</script>

<form
    action="<?php echo JRoute::_('index.php?option=com_manifest2md&layout=edit&id=' . (int)$this->item->id); ?>"
    method="post" enctype="multipart/form-data" name="adminForm" id="extension-form" class="form-validate">

    <div class="form-horizontal">
        <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_MANIFEST2MD_TITLE_EXTENSION', true)); ?>
        <div class="row-fluid">
            <div class="span10 form-horizontal">
                <fieldset class="adminform">

                    <input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>"/>
                    <input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>"/>
                    <input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>"/>
                    <input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>"/>
                    <input type="hidden" name="jform[checked_out_time]"
                           value="<?php echo $this->item->checked_out_time; ?>"/>

                    <?php echo $this->form->renderField('created_by'); ?>
                    <?php echo $this->form->renderField('modified_by'); ?>                <?php echo $this->form->renderField('name'); ?>
                    <?php echo $this->form->renderField('type'); ?>
                    <?php echo $this->form->renderField('element'); ?>
                    <?php echo $this->form->renderField('folder'); ?>
                    <?php echo $this->form->renderField('identifier'); ?>
                    <?php echo $this->form->renderField('doc_element'); ?>
                    <?php echo $this->form->renderField('specific_home'); ?>
                    <?php echo $this->form->renderField('catid'); ?>


                    <?php if ($this->state->params->get('save_history', 1)) : ?>
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('version_note'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('version_note'); ?></div>
                        </div>
                    <?php endif; ?>
                </fieldset>
            </div>
        </div>
        <?php echo JHtml::_('bootstrap.endTab'); ?>

        <?php if (JFactory::getUser()->authorise('core.admin', 'manifest2md')) : ?>
            <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'permissions', JText::_('JGLOBAL_ACTION_PERMISSIONS_LABEL', true)); ?>
            <?php echo $this->form->getInput('rules'); ?>
            <?php echo JHtml::_('bootstrap.endTab'); ?>
        <?php endif; ?>

        <?php echo JHtml::_('bootstrap.endTabSet'); ?>

        <input type="hidden" name="task" value=""/>
        <?php echo JHtml::_('form.token'); ?>

    </div>
</form>
