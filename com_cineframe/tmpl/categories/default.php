<?php

/**
 * @package     CineFrame
 * @subpackage  com_cineframe
 *
 * @copyright   (C) 2026 WebtechSolutions
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.multiselect');
?>
<form action="<?php echo Route::_('index.php?option=com_cineframe&view=categories'); ?>" method="post" name="adminForm" id="adminForm">
    <div class="row">
        <div class="col-md-12">
            <div id="j-main-container" class="j-main-container">
                <?php if (empty($this->items)) : ?>
                    <div class="alert alert-info mt-3">
                        <span class="icon-info-circle" aria-hidden="true"></span>
                        <?php echo Text::_('COM_CINEFRAME_NO_CATEGORIES'); ?>
                    </div>
                <?php else : ?>
                    <table class="table" id="categoryList">
                        <caption class="visually-hidden"><?php echo Text::_('COM_CINEFRAME_CATEGORIES_TITLE'); ?></caption>
                        <thead>
                            <tr>
                                <td class="w-1 text-center"><?php echo HTMLHelper::_('grid.checkall'); ?></td>
                                <th class="w-1 text-center"><?php echo Text::_('COM_CINEFRAME_HEADING_ID'); ?></th>
                                <th><?php echo Text::_('COM_CINEFRAME_FIELD_CAT_NAME_LABEL'); ?></th>
                                <th class="w-10 text-center"><?php echo Text::_('COM_CINEFRAME_FIELD_ORDERING_LABEL'); ?></th>
                                <th class="w-10 text-center"><?php echo Text::_('COM_CINEFRAME_FIELD_STATUS_LABEL'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($this->items as $i => $item) : ?>
                            <tr>
                                <td class="text-center"><?php echo HTMLHelper::_('grid.id', $i, $item->id); ?></td>
                                <td class="text-center"><?php echo (int) $item->id; ?></td>
                                <td>
                                    <a href="<?php echo Route::_('index.php?option=com_cineframe&task=category.edit&id=' . (int) $item->id); ?>">
                                        <?php echo $this->escape($item->name); ?>
                                    </a>
                                </td>
                                <td class="text-center"><?php echo (int) $item->ordering; ?></td>
                                <td class="text-center">
                                    <?php if ($item->published) : ?>
                                        <span class="badge bg-success"><?php echo Text::_('JPUBLISHED'); ?></span>
                                    <?php else : ?>
                                        <span class="badge bg-danger"><?php echo Text::_('JUNPUBLISHED'); ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php echo $this->pagination->getListFooter(); ?>
                <?php endif; ?>

                <input type="hidden" name="task" value="">
                <input type="hidden" name="boxchecked" value="0">
                <?php echo HTMLHelper::_('form.token'); ?>
            </div>
        </div>
    </div>
</form>
