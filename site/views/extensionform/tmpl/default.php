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

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

// Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_manifest2md', JPATH_SITE);
$doc = JFactory::getDocument();
$doc->addScript(JUri::base() . '/media/com_manifest2md/js/form.js');

$user = JFactory::getUser();
$canEdit = Manifest2mdHelpersManifest2md::canUserEdit($this->item, $user);


?>

<div class="extension-edit front-end-edit">
    <?php if (!$canEdit) : ?>
        <h3>
            <?php throw new Exception(JText::_('COM_MANIFEST2MD_ERROR_MESSAGE_NOT_AUTHORISED'), 403); ?>
        </h3>
    <?php else : ?>
        <?php if (!empty($this->item->id)): ?>
            <h1><?php echo JText::sprintf('COM_MANIFEST2MD_EDIT_ITEM_TITLE', $this->item->id); ?></h1>
        <?php else: ?>
            <h1><?php echo JText::_('COM_MANIFEST2MD_ADD_ITEM_TITLE'); ?></h1>
        <?php endif; ?>

        <form id="form-extension"
              action="<?php echo JRoute::_('index.php?option=com_manifest2md&task=extension.save'); ?>"
              method="post" class="form-validate form-horizontal" enctype="multipart/form-data">

            <input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>"/>

            <input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>"/>

            <input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>"/>

            <input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>"/>

            <input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>"/>

            <?php echo $this->form->getInput('created_by'); ?>
            <?php echo $this->form->getInput('modified_by'); ?>
            <?php echo $this->form->renderField('name'); ?>

            <?php echo $this->form->renderField('type'); ?>

            <?php echo $this->form->renderField('element'); ?>

            <?php echo $this->form->renderField('folder'); ?>

            <?php echo $this->form->renderField('identifier'); ?>

            <?php echo $this->form->renderField('doc_element'); ?>

            <?php echo $this->form->renderField('specific_home'); ?>

            <?php echo $this->form->renderField('category'); ?>
            <div
                class="fltlft" <?php if (!JFactory::getUser()->authorise('core.admin', 'manifest2md')): ?> style="display:none;" <?php endif; ?> >
                <?php echo JHtml::_('sliders.start', 'permissions-sliders-' . $this->item->id, array('useCookie' => 1)); ?>
                <?php echo JHtml::_('sliders.panel', JText::_('ACL Configuration'), 'access-rules'); ?>
                <fieldset class="panelform">
                    <?php echo $this->form->getLabel('rules'); ?>
                    <?php echo $this->form->getInput('rules'); ?>
                </fieldset>
                <?php echo JHtml::_('sliders.end'); ?>
            </div>
            <?php if (!JFactory::getUser()->authorise('core.admin', 'manifest2md')): ?>
                <script type="text/javascript">
                    jQuery.noConflict();
                    jQuery('.tab-pane select').each(function () {
                        var option_selected = jQuery(this).find(':selected');
                        var input = document.createElement("input");
                        input.setAttribute("type", "hidden");
                        input.setAttribute("name", jQuery(this).attr('name'));
                        input.setAttribute("value", option_selected.val());
                        document.getElementById("form-extension").appendChild(input);
                    });
                </script>
            <?php endif; ?>
            <div class="control-group">
                <div class="controls">

                    <?php if ($this->canSave): ?>
                        <button type="submit" class="validate btn btn-primary">
                            <?php echo JText::_('JSUBMIT'); ?>
                        </button>
                    <?php endif; ?>
                    <a class="btn"
                       href="<?php echo JRoute::_('index.php?option=com_manifest2md&task=extensionform.cancel'); ?>"
                       title="<?php echo JText::_('JCANCEL'); ?>">
                        <?php echo JText::_('JCANCEL'); ?>
                    </a>
                </div>
            </div>

            <input type="hidden" name="option" value="com_manifest2md"/>
            <input type="hidden" name="task"
                   value="extensionform.save"/>
            <?php echo JHtml::_('form.token'); ?>
        </form>
    <?php endif; ?>
</div>
