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
use Joomla\CMS\Uri\Uri;

$cssUrl = Uri::root(true) . '/media/plg_content_cineframe/css/cineframe.css';
?>
<link rel="stylesheet" href="<?php echo $cssUrl; ?>"><?php // injected here since addStyleSheet fires after <head> in some templates ?>
<div class="cf-page">
    <div class="cf-page-header">
        <div class="cf-page-header__icon">&#127910;</div>
        <div>
            <h1 class="cf-page-header__title">Βιντεοθήκη</h1>
            <p class="cf-page-header__sub">Επιλέξτε φεστιβάλ για να δείτε τα βίντεο</p>
        </div>
    </div>

    <?php if (empty($this->items)) : ?>
        <p class="cf-empty">Δεν υπάρχουν κατηγορίες βίντεο.</p>
    <?php else : ?>
        <div class="cf-cat-grid">
            <?php foreach ($this->items as $cat) :
                $count = (int) $cat->video_count;
            ?>
                <a class="cf-cat-card" href="<?php echo Route::_('index.php?option=com_cineframe&view=category&id=' . (int) $cat->id); ?>">
                    <div class="cf-cat-card__icon">&#127902;</div>
                    <span class="cf-cat-name"><?php echo htmlspecialchars($cat->name, ENT_QUOTES, 'UTF-8'); ?></span>
                    <span class="cf-cat-count"><?php echo $count ?: 0; ?> <?php echo $count === 1 ? 'βίντεο' : 'βίντεο'; ?></span>
                    <span class="cf-cat-arrow">&#8594;</span>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
