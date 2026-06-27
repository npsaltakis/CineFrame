<?php

/**
 * @package     CineFrame
 * @subpackage  com_cineframe (site)
 *
 * @copyright   (C) 2026 WebtechSolutions
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;
?>
<div class="cf-categories">
    <?php if (empty($this->items)) : ?>
        <p class="cf-empty">Δεν υπάρχουν κατηγορίες βίντεο.</p>
    <?php else : ?>
        <div class="cf-cat-grid">
            <?php foreach ($this->items as $cat) : ?>
                <a class="cf-cat-card" href="<?php echo Route::_('index.php?option=com_cineframe&view=category&id=' . (int) $cat->id); ?>">
                    <span class="cf-cat-name"><?php echo htmlspecialchars($cat->name, ENT_QUOTES, 'UTF-8'); ?></span>
                    <?php if ($cat->video_count) : ?>
                        <span class="cf-cat-count"><?php echo (int) $cat->video_count; ?> βίντεο</span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
