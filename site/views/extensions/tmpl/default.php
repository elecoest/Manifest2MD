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
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$user = JFactory::getUser();
$userId = $user->get('id');
$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');
$canCreate = $user->authorise('core.create', 'com_manifest2md') && file_exists(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'extensionform.xml');
$canEdit = $user->authorise('core.edit', 'com_manifest2md') && file_exists(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'extensionform.xml');
$canCheckin = $user->authorise('core.manage', 'com_manifest2md');
$canChange = $user->authorise('core.edit.state', 'com_manifest2md');
$canDelete = $user->authorise('core.delete', 'com_manifest2md');
?>

<form action="<?php echo JRoute::_('index.php?option=com_manifest2md&view=extensions'); ?>" method="post"
      name="adminForm" id="adminForm">

    <?php echo JLayoutHelper::render('default_filter', array('view' => $this), dirname(__FILE__)); ?>
    <table class="table table-striped" id="extensionList">
        <thead>
        <tr>
            <?php if (isset($this->items[0]->state)): ?>
                <th width="5%">
                    <?php echo JHtml::_('grid.sort', 'JPUBLISHED', 'a.state', $listDirn, $listOrder); ?>
                </th>
            <?php endif; ?>

            <th class=''>
                <?php echo JHtml::_('grid.sort', 'COM_MANIFEST2MD_EXTENSIONS_ID', 'a.id', $listDirn, $listOrder); ?>
            </th>
            <th class=''>
                <?php echo JHtml::_('grid.sort', 'COM_MANIFEST2MD_EXTENSIONS_NAME', 'a.name', $listDirn, $listOrder); ?>
            </th>
            <th class=''>
                <?php echo JHtml::_('grid.sort', 'COM_MANIFEST2MD_EXTENSIONS_TYPE', 'a.type', $listDirn, $listOrder); ?>
            </th>
            <th class=''>
                <?php echo JHtml::_('grid.sort', 'COM_MANIFEST2MD_EXTENSIONS_ELEMENT', 'a.element', $listDirn, $listOrder); ?>
            </th>
            <th class=''>
                <?php echo JHtml::_('grid.sort', 'COM_MANIFEST2MD_EXTENSIONS_FOLDER', 'a.folder', $listDirn, $listOrder); ?>
            </th>
            <th class=''>
                <?php echo JHtml::_('grid.sort', 'COM_MANIFEST2MD_EXTENSIONS_IDENTIFIER', 'a.identifier', $listDirn, $listOrder); ?>
            </th>
            <th class=''>
                <?php echo JHtml::_('grid.sort', 'COM_MANIFEST2MD_EXTENSIONS_DOC_ELEMENT', 'a.doc_element', $listDirn, $listOrder); ?>
            </th>
            <th class=''>
                <?php echo JHtml::_('grid.sort', 'COM_MANIFEST2MD_EXTENSIONS_SPECIFIC_HOME', 'a.specific_home', $listDirn, $listOrder); ?>
            </th>
            <th class=''>
                <?php echo JHtml::_('grid.sort', 'COM_MANIFEST2MD_EXTENSIONS_CATEGORY', 'a.category', $listDirn, $listOrder); ?>
            </th>


            <?php if ($canEdit || $canDelete): ?>
                <th class="center">
                    <?php echo JText::_('COM_MANIFEST2MD_EXTENSIONS_ACTIONS'); ?>
                </th>
            <?php endif; ?>

        </tr>
        </thead>
        <tfoot>
        <tr>
            <td colspan="<?php echo isset($this->items[0]) ? count(get_object_vars($this->items[0])) : 10; ?>">
                <?php echo $this->pagination->getListFooter(); ?>
            </td>
        </tr>
        </tfoot>
        <tbody>
        <?php foreach ($this->items as $i => $item) : ?>
            <?php $canEdit = $user->authorise('core.edit', 'com_manifest2md'); ?>

            <?php if (!$canEdit && $user->authorise('core.edit.own', 'com_manifest2md')): ?>
                <?php $canEdit = JFactory::getUser()->id == $item->created_by; ?>
            <?php endif; ?>

            <tr class="row<?php echo $i % 2; ?>">

                <?php if (isset($this->items[0]->state)) : ?>
                    <?php $class = ($canChange) ? 'active' : 'disabled'; ?>
                    <td class="center">
                        <a class="btn btn-micro <?php echo $class; ?>"
                           href="<?php echo ($canChange) ? JRoute::_('index.php?option=com_manifest2md&task=extension.publish&id=' . $item->id . '&state=' . (($item->state + 1) % 2), false, 2) : '#'; ?>">
                            <?php if ($item->state == 1): ?>
                                <i class="icon-publish"></i>
                            <?php else: ?>
                                <i class="icon-unpublish"></i>
                            <?php endif; ?>
                        </a>
                    </td>
                <?php endif; ?>

                <td>

                    <?php echo $item->id; ?>
                </td>
                <td>
                    <?php if (isset($item->checked_out) && $item->checked_out) : ?>
                        <?php echo JHtml::_('jgrid.checkedout', $i, $item->uEditor, $item->checked_out_time, 'extensions.', $canCheckin); ?>
                    <?php endif; ?>
                    <a href="<?php echo JRoute::_('index.php?option=com_manifest2md&view=extension&id=' . (int)$item->id); ?>">
                        <?php echo $this->escape($item->name); ?></a>
                </td>
                <td>

                    <?php echo $item->type; ?>
                </td>
                <td>

                    <?php echo $item->element; ?>
                </td>
                <td>

                    <?php echo $item->folder; ?>
                </td>
                <td>

                    <?php echo $item->identifier; ?>
                </td>
                <td>

                    <?php echo $item->doc_element; ?>
                </td>
                <td>

                    <?php echo $item->specific_home; ?>
                </td>
                <td>

                    <?php echo $item->category; ?>
                </td>


                <?php if ($canEdit || $canDelete): ?>
                    <td class="center">
                        <?php if ($canEdit): ?>
                            <a href="<?php echo JRoute::_('index.php?option=com_manifest2md&task=extensionform.edit&id=' . $item->id, false, 2); ?>"
                               class="btn btn-mini" type="button"><i class="icon-edit"></i></a>
                        <?php endif; ?>
                        <?php if ($canDelete): ?>
                            <a href="<?php echo JRoute::_('index.php?option=com_manifest2md&task=extensionform.remove&id=' . $item->id, false, 2); ?>"
                               class="btn btn-mini delete-button" type="button"><i class="icon-trash"></i></a>
                        <?php endif; ?>
                    </td>
                <?php endif; ?>

            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <?php if ($canCreate) : ?>
        <a href="<?php echo JRoute::_('index.php?option=com_manifest2md&task=extensionform.edit&id=0', false, 0); ?>"
           class="btn btn-success btn-small"><i
                class="icon-plus"></i>
            <?php echo JText::_('COM_MANIFEST2MD_ADD_ITEM'); ?></a>
    <?php endif; ?>

    <input type="hidden" name="task" value=""/>
    <input type="hidden" name="boxchecked" value="0"/>
    <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
    <?php echo JHtml::_('form.token'); ?>
</form>

<?php if ($canDelete) : ?>
    <script type="text/javascript">

        jQuery(document).ready(function () {
            jQuery('.delete-button').click(deleteItem);
        });

        function deleteItem() {

            if (!confirm("<?php echo JText::_('COM_MANIFEST2MD_DELETE_MESSAGE'); ?>")) {
                return false;
            }
        }
    </script>
<?php endif; ?>
