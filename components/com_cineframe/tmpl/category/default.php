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

// ID extraction aligned with plg_content_cineframe: accepts bare IDs and all URL forms.
$cfYoutubeId = function (string $source): string {
    $source = trim($source);

    if (preg_match('/^[A-Za-z0-9_-]{11}$/', $source)) {
        return $source;
    }

    if (preg_match('~(?:youtube\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/|v/)|youtu\.be/)([A-Za-z0-9_-]{11})~i', $source, $m)) {
        return $m[1];
    }

    return '';
};

$cfVimeoId = function (string $source): string {
    $source = trim($source);

    if (preg_match('/^\d+$/', $source)) {
        return $source;
    }

    if (preg_match('~vimeo\.com/(?:video/|channels/[^/]+/|groups/[^/]+/videos/)?(\d+)~i', $source, $m)) {
        return $m[1];
    }

    return '';
};
?>
<link rel="stylesheet" href="<?php echo $cssUrl; ?>">

<!-- Modal overlay -->
<div id="cf-modal" class="cf-modal" onclick="cfModalClose(event)">
    <button class="cf-modal__close" onclick="cfModalClose(null, true)">&#10005;</button>
    <div class="cf-modal__box">
        <div class="cf-modal__player" id="cf-modal-player"></div>
        <div class="cf-modal__info">
            <h2 class="cf-modal__title" id="cf-modal-title"></h2>
            <div class="cf-modal__desc" id="cf-modal-desc"></div>
        </div>
    </div>
</div>

<div class="cf-page">

    <div class="cf-page-header">
        <a class="cf-back-link" href="<?php echo Route::_('index.php?option=com_cineframe&view=categories'); ?>">&#8592; <?php echo Text::_('COM_CINEFRAME_SITE_TITLE'); ?></a>
        <?php if ($this->category) : ?>
            <h1 class="cf-page-header__title"><?php echo htmlspecialchars($this->category->name, ENT_QUOTES, 'UTF-8'); ?></h1>
        <?php endif; ?>
    </div>

    <?php if (empty($this->videos)) : ?>
        <p class="cf-empty"><?php echo Text::_('COM_CINEFRAME_SITE_NO_VIDEOS'); ?></p>
    <?php else : ?>
        <div class="cf-video-grid">
            <?php foreach ($this->videos as $v) :
                $thumb = $v->thumb;
                if (!$thumb && $v->type === 'youtube') {
                    $ytId = $cfYoutubeId($v->source);
                    if ($ytId !== '') {
                        $thumb = 'https://img.youtube.com/vi/' . $ytId . '/hqdefault.jpg';
                    }
                }
                if (!$thumb && $v->type === 'vimeo') {
                    $vmId = $cfVimeoId($v->source);
                    if ($vmId !== '') {
                        $thumb = 'https://vumbnail.com/' . $vmId . '.jpg';
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
                        <?php $descPreview = trim(strip_tags($v->description ?? '')); ?>
                        <?php if ($descPreview !== '') : ?>
                            <p class="cf-video-desc"><?php echo htmlspecialchars($descPreview, ENT_QUOTES, 'UTF-8'); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($this->pagination && $this->pagination->pagesTotal > 1) : ?>
            <nav class="cf-pagination">
                <?php echo $this->pagination->getPagesLinks(); ?>
            </nav>
        <?php endif; ?>

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

        // ID extraction aligned with plg_content_cineframe: accepts bare IDs and all URL forms.
        function cfYoutubeId(source) {
            source = source.trim();
            if (/^[A-Za-z0-9_-]{11}$/.test(source)) return source;
            var m = source.match(/(?:youtube\.com\/(?:watch\?(?:.*&)?v=|embed\/|shorts\/|v\/)|youtu\.be\/)([A-Za-z0-9_-]{11})/i);
            return m ? m[1] : '';
        }

        function cfVimeoId(source) {
            source = source.trim();
            if (/^\d+$/.test(source)) return source;
            var m = source.match(/vimeo\.com\/(?:video\/|channels\/[^\/]+\/|groups\/[^\/]+\/videos\/)?(\d+)/i);
            return m ? m[1] : '';
        }

        function cfPlay(id) {
            var v = cfVideos.find(function(x){ return x.id === id; });
            if (!v) return;

            var html = '';
            if (v.type === 'youtube') {
                var yid = cfYoutubeId(v.source);
                if (yid) html = '<iframe src="https://www.youtube-nocookie.com/embed/' + yid + '?autoplay=1&rel=0" frameborder="0" allowfullscreen allow="autoplay; encrypted-media"></iframe>';
            } else if (v.type === 'vimeo') {
                var vid = cfVimeoId(v.source);
                if (vid) html = '<iframe src="https://player.vimeo.com/video/' + vid + '?autoplay=1" frameborder="0" allowfullscreen></iframe>';
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
            // Description is admin-authored HTML (safehtml-filtered on save)
            var hasDesc = v.desc && v.desc.replace(/<[^>]*>/g, '').trim() !== '';
            document.getElementById('cf-modal-desc').innerHTML = hasDesc ? v.desc : '';
            document.getElementById('cf-modal-desc').style.display = hasDesc ? '' : 'none';

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
