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
<link rel="stylesheet" href="<?php echo $cssUrl; ?>">
<div class="cf-page">

    <div class="cf-page-header">
        <a class="cf-back-link" href="<?php echo Route::_('index.php?option=com_cineframe&view=categories'); ?>">&#8592; Βιντεοθήκη</a>
        <?php if ($this->category) : ?>
            <h1 class="cf-page-header__title"><?php echo htmlspecialchars($this->category->name, ENT_QUOTES, 'UTF-8'); ?></h1>
        <?php endif; ?>
    </div>

    <?php if (empty($this->videos)) : ?>
        <p class="cf-empty">Δεν υπάρχουν βίντεο σε αυτή την κατηγορία.</p>
    <?php else : ?>
        <div class="cf-video-grid">
            <?php foreach ($this->videos as $v) :
                $thumb = $v->thumb;
                if (!$thumb && $v->type === 'youtube') {
                    preg_match('/(?:v=|youtu\.be\/|embed\/)([a-zA-Z0-9_\-]{11})/', $v->source, $m);
                    if (!empty($m[1])) {
                        $thumb = 'https://img.youtube.com/vi/' . $m[1] . '/hqdefault.jpg';
                    }
                }
                if (!$thumb && $v->type === 'vimeo') {
                    preg_match('/vimeo\.com\/(\d+)/', $v->source, $m);
                    if (!empty($m[1])) {
                        $thumb = 'https://vumbnail.com/' . $m[1] . '.jpg';
                    }
                }
            ?>
                <div class="cf-video-card" id="cf-card-<?php echo (int) $v->id; ?>">
                    <div class="cf-thumb" onclick="cfPlay(<?php echo (int) $v->id; ?>, this)">
                        <?php if ($thumb) : ?>
                            <img src="<?php echo htmlspecialchars($thumb, ENT_QUOTES, 'UTF-8'); ?>"
                                 alt="<?php echo htmlspecialchars($v->title, ENT_QUOTES, 'UTF-8'); ?>"
                                 loading="lazy">
                        <?php else : ?>
                            <div class="cf-no-thumb">&#127910;</div>
                        <?php endif; ?>
                        <div class="cf-play-overlay"><span class="cf-play-btn">&#9654;</span></div>
                    </div>
                    <div class="cf-video-embed" id="cf-embed-<?php echo (int) $v->id; ?>"></div>
                    <div class="cf-video-info">
                        <h3 class="cf-video-title"><?php echo htmlspecialchars($v->title, ENT_QUOTES, 'UTF-8'); ?></h3>
                        <?php if ($v->description) : ?>
                            <p class="cf-video-desc"><?php echo htmlspecialchars($v->description, ENT_QUOTES, 'UTF-8'); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <script>
        var cfVideos = <?php echo json_encode(array_map(function($v) {
            return [
                'id'     => (int) $v->id,
                'type'   => $v->type,
                'source' => $v->source,
                'width'  => (int) $v->width ?: 640
            ];
        }, $this->videos)); ?>;

        function cfPlay(id, thumbEl) {
            var v = cfVideos.find(function(x){ return x.id === id; });
            if (!v) return;

            var card  = document.getElementById('cf-card-' + id);
            var embed = document.getElementById('cf-embed-' + id);
            var html  = '';

            if (v.type === 'youtube') {
                var yid = v.source.match(/(?:v=|youtu\.be\/|embed\/)([a-zA-Z0-9_\-]{11})/);
                if (yid) {
                    html = '<div class="cf-embed-wrap"><iframe src="https://www.youtube.com/embed/' + yid[1] + '?autoplay=1&rel=0" frameborder="0" allowfullscreen allow="autoplay; encrypted-media"></iframe></div>';
                }
            } else if (v.type === 'vimeo') {
                var vid = v.source.match(/vimeo\.com\/(\d+)/);
                if (vid) {
                    html = '<div class="cf-embed-wrap"><iframe src="https://player.vimeo.com/video/' + vid[1] + '?autoplay=1" frameborder="0" allowfullscreen></iframe></div>';
                }
            } else if (v.type === 'video') {
                html = '<div class="cf-embed-wrap"><video src="' + v.source + '" controls autoplay style="width:100%;height:100%;background:#000"></video></div>';
            } else {
                // embed type: wrap raw HTML in responsive container
                html = '<div class="cf-embed-raw">' + v.source + '</div>';
            }

            if (html) {
                card.querySelector('.cf-thumb').style.display = 'none';
                embed.innerHTML = html;
                embed.style.display = 'block';
                // Smooth scroll to card
                card.scrollIntoView({behavior: 'smooth', block: 'nearest'});
            }
        }
        </script>
    <?php endif; ?>
</div>
