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

<!-- Modal overlay -->
<div id="cf-modal" class="cf-modal" onclick="cfModalClose(event)">
    <button class="cf-modal__close" onclick="cfModalClose(null, true)">&#10005;</button>
    <div class="cf-modal__box">
        <div class="cf-modal__player" id="cf-modal-player"></div>
        <div class="cf-modal__info">
            <h2 class="cf-modal__title" id="cf-modal-title"></h2>
            <p class="cf-modal__desc" id="cf-modal-desc"></p>
        </div>
    </div>
</div>

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
                <div class="cf-video-card" onclick="cfPlay(<?php echo (int) $v->id; ?>)" style="cursor:pointer">
                    <div class="cf-thumb">
                        <?php if ($thumb) : ?>
                            <img src="<?php echo htmlspecialchars($thumb, ENT_QUOTES, 'UTF-8'); ?>"
                                 alt="<?php echo htmlspecialchars($v->title, ENT_QUOTES, 'UTF-8'); ?>"
                                 loading="lazy">
                        <?php else : ?>
                            <div class="cf-no-thumb">&#127910;</div>
                        <?php endif; ?>
                        <div class="cf-play-overlay"><span class="cf-play-btn">&#9654;</span></div>
                    </div>
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
                'title'  => $v->title,
                'desc'   => (string) $v->description
            ];
        }, $this->videos)); ?>;

        function cfPlay(id) {
            var v = cfVideos.find(function(x){ return x.id === id; });
            if (!v) return;

            var html = '';
            if (v.type === 'youtube') {
                var yid = v.source.match(/(?:v=|youtu\.be\/|embed\/)([a-zA-Z0-9_\-]{11})/);
                if (yid) html = '<iframe src="https://www.youtube.com/embed/' + yid[1] + '?autoplay=1&rel=0" frameborder="0" allowfullscreen allow="autoplay; encrypted-media"></iframe>';
            } else if (v.type === 'vimeo') {
                var vid = v.source.match(/vimeo\.com\/(\d+)/);
                if (vid) html = '<iframe src="https://player.vimeo.com/video/' + vid[1] + '?autoplay=1" frameborder="0" allowfullscreen></iframe>';
            } else if (v.type === 'video') {
                html = '<video src="' + v.source + '" controls autoplay></video>';
            } else {
                // embed — extract or wrap
                var tmp = document.createElement('div');
                tmp.innerHTML = v.source;
                var iframe = tmp.querySelector('iframe');
                if (iframe) {
                    iframe.removeAttribute('style');
                    iframe.removeAttribute('width');
                    iframe.removeAttribute('height');
                    if (iframe.src && !iframe.src.includes('autoplay')) {
                        iframe.src += (iframe.src.includes('?') ? '&' : '?') + 'autoplay=1';
                    }
                    html = iframe.outerHTML;
                } else {
                    html = v.source;
                }
            }

            if (!html) return;

            document.getElementById('cf-modal-player').innerHTML = html;
            document.getElementById('cf-modal-title').textContent = v.title;
            document.getElementById('cf-modal-desc').textContent = v.desc || '';
            document.getElementById('cf-modal-desc').style.display = v.desc ? '' : 'none';

            var modal = document.getElementById('cf-modal');
            modal.classList.add('cf-modal--open');
            document.body.style.overflow = 'hidden';
        }

        function cfModalClose(e, force) {
            if (!force && e && e.target !== document.getElementById('cf-modal')) return;
            var modal = document.getElementById('cf-modal');
            modal.classList.remove('cf-modal--open');
            document.body.style.overflow = '';
            // Stop video/audio by clearing player
            document.getElementById('cf-modal-player').innerHTML = '';
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') cfModalClose(null, true);
        });
        </script>
    <?php endif; ?>
</div>
