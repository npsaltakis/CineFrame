<?php

/**
 * @package     CineFrame
 * @subpackage  com_cineframe (site)
 *
 * @copyright   (C) 2026 WebtechSolutions
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

$cssUrl = Uri::root(true) . '/media/plg_content_cineframe/css/cineframe.css?v=1.2.4';
?>
<link rel="stylesheet" href="<?php echo $cssUrl; ?>"><?php // injected here since addStyleSheet fires after <head> in some templates ?>
<div class="cf-page">
    <div class="cf-page-header">
        <div class="cf-page-header__icon">&#127910;</div>
        <div>
            <h1 class="cf-page-header__title"><?php echo Text::_('COM_CINEFRAME_SITE_TITLE'); ?></h1>
            <p class="cf-page-header__sub"><?php echo Text::_('COM_CINEFRAME_SITE_CATEGORIES_SUB'); ?></p>
        </div>
    </div>

    <?php if (empty($this->items)) : ?>
        <p class="cf-empty"><?php echo Text::_('COM_CINEFRAME_SITE_NO_CATEGORIES'); ?></p>
    <?php else : ?>
        <div class="cf-cat-grid">
            <?php foreach ($this->items as $cat) :
                $count = (int) $cat->video_count;
            ?>
                <a class="cf-cat-card" href="<?php echo Route::_('index.php?option=com_cineframe&view=category&id=' . (int) $cat->id); ?>">
                    <div class="cf-cat-card__icon">&#127902;</div>
                    <span class="cf-cat-name"><?php echo htmlspecialchars($cat->name, ENT_QUOTES, 'UTF-8'); ?></span>
                    <span class="cf-cat-count"><?php echo $count ?: 0; ?> <?php echo Text::_('COM_CINEFRAME_SITE_VIDEO_COUNT'); ?></span>
                    <span class="cf-cat-arrow">&#8594;</span>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
