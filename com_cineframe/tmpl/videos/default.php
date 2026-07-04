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

$search    = (string) $this->state->get('filter.search');
$catid     = (string) $this->state->get('filter.catid');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$categoryOptions = array_merge(
    [HTMLHelper::_('select.option', '', Text::_('COM_CINEFRAME_FILTER_CATEGORY_ALL'))],
    $this->categoryOptions ?: []
);
?>
<form action="<?php echo Route::_('index.php?option=com_cineframe&view=videos'); ?>" method="post" name="adminForm" id="adminForm">
    <div class="row">
        <div class="col-md-12">
            <div id="j-main-container" class="j-main-container">
                <div class="js-stools clearfix mb-3">
                    <div class="js-stools-container-bar d-flex flex-wrap gap-2 align-items-center">
                        <label for="filter_search" class="visually-hidden">
                            <?php echo Text::_('COM_CINEFRAME_FILTER_SEARCH_LABEL'); ?>
                        </label>
                        <input
                            type="text"
                            name="filter_search"
                            id="filter_search"
                            class="form-control"
                            style="max-width: 320px;"
                            value="<?php echo $this->escape($search); ?>"
                            placeholder="<?php echo Text::_('COM_CINEFRAME_FILTER_SEARCH_PLACEHOLDER'); ?>"
                        >

                        <label for="filter_catid" class="visually-hidden">
                            <?php echo Text::_('COM_CINEFRAME_FILTER_CATEGORY_LABEL'); ?>
                        </label>
                        <?php echo HTMLHelper::_(
                            'select.genericlist',
                            $categoryOptions,
                            'filter_catid',
                            'class="form-select" style="max-width: 320px;" onchange="this.form.submit()"',
                            'value',
                            'text',
                            $catid,
                            'filter_catid'
                        ); ?>

                        <button type="submit" class="btn btn-primary">
                            <span class="icon-search" aria-hidden="true"></span>
                            <?php echo Text::_('JSEARCH_FILTER_SUBMIT'); ?>
                        </button>
                        <button
                            type="button"
                            class="btn btn-secondary"
                            onclick="document.getElementById('filter_search').value=''; document.getElementById('filter_catid').value=''; this.form.submit();"
                        >
                            <?php echo Text::_('JSEARCH_FILTER_CLEAR'); ?>
                        </button>
                    </div>
                </div>

                <?php if (empty($this->items)) : ?>
                    <div class="alert alert-info mt-3">
                        <span class="icon-info-circle" aria-hidden="true"></span>
                        <?php echo Text::_('COM_CINEFRAME_NO_VIDEOS'); ?>
                    </div>
                <?php else : ?>
                    <table class="table" id="videoList">
                        <caption class="visually-hidden"><?php echo Text::_('COM_CINEFRAME_LIST_CAPTION'); ?></caption>
                        <thead>
                            <tr>
                                <td class="w-1 text-center">
                                    <?php echo HTMLHelper::_('grid.checkall'); ?>
                                </td>
                                <th class="w-1 text-center">
                                    <?php echo HTMLHelper::_('grid.sort', 'COM_CINEFRAME_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                                </th>
                                <th>
                                    <?php echo HTMLHelper::_('grid.sort', 'COM_CINEFRAME_HEADING_TITLE', 'a.title', $listDirn, $listOrder); ?>
                                </th>
                                <th class="w-15">
                                    <?php echo HTMLHelper::_('grid.sort', 'COM_CINEFRAME_HEADING_CATEGORY', 'c.name', $listDirn, $listOrder); ?>
                                </th>
                                <th class="w-10">
                                    <?php echo HTMLHelper::_('grid.sort', 'COM_CINEFRAME_HEADING_TYPE', 'a.type', $listDirn, $listOrder); ?>
                                </th>
                                <th><?php echo Text::_('COM_CINEFRAME_HEADING_SHORTCODE'); ?></th>
                                <th class="w-10 text-center">
                                    <?php echo HTMLHelper::_('grid.sort', 'COM_CINEFRAME_HEADING_STATUS', 'a.published', $listDirn, $listOrder); ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($this->items as $i => $item) : ?>
                            <tr>
                                <td class="text-center">
                                    <?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
                                </td>
                                <td class="text-center"><?php echo (int) $item->id; ?></td>
                                <td>
                                    <a href="<?php echo Route::_('index.php?option=com_cineframe&task=video.edit&id=' . (int) $item->id); ?>">
                                        <?php echo $this->escape($item->title); ?>
                                    </a>
                                </td>
                                <td><?php echo $item->category_name ? $this->escape($item->category_name) : '<span class="text-muted">—</span>'; ?></td>
                                <td><span class="badge bg-secondary"><?php echo $this->escape($item->type); ?></span></td>
                                <td><code>{cineframe videoid=<?php echo (int) $item->id; ?> width=<?php echo (int) ($item->width ?: 640); ?>}</code></td>
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
                <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>">
                <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>">
                <?php echo HTMLHelper::_('form.token'); ?>
            </div>
        </div>
    </div>
</form>
