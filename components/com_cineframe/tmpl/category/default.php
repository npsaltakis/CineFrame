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

$this->getDocument()->addStyleSheet(\Joomla\CMS\Uri\Uri::root(true) . '/media/plg_content_cineframe/css/cineframe.css');
?>
<div class="cf-category">
    <?php if ($this->category) : ?>
        <h2 class="cf-cat-title"><?php echo htmlspecialchars($this->category->name, ENT_QUOTES, 'UTF-8'); ?></h2>
    <?php endif; ?>

    <?php if (empty($this->videos)) : ?>
        <p class="cf-empty">Δεν υπάρχουν βίντεο σε αυτή την κατηγορία.</p>
    <?php else : ?>
        <div class="cf-video-grid">
            <?php foreach ($this->videos as $v) :
                // Extract YouTube/Vimeo ID for thumbnail fallback
                $thumb = $v->thumb;
                if (!$thumb && $v->type === 'youtube') {
                    preg_match('/(?:v=|youtu\.be\/|embed\/)([a-zA-Z0-9_\-]{11})/', $v->source, $m);
                    if (!empty($m[1])) {
                        $thumb = 'https://img.youtube.com/vi/' . $m[1] . '/hqdefault.jpg';
                    }
                }
            ?>
                <div class="cf-video-card" data-id="<?php echo (int) $v->id; ?>">
                    <div class="cf-thumb" onclick="cfPlay(<?php echo (int) $v->id; ?>, this)" style="cursor:pointer">
                        <?php if ($thumb) : ?>
                            <img src="<?php echo htmlspecialchars($thumb, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($v->title, ENT_QUOTES, 'UTF-8'); ?>" loading="lazy">
                        <?php else : ?>
                            <div class="cf-no-thumb"><span class="icon-play"></span></div>
                        <?php endif; ?>
                        <span class="cf-play-btn">&#9654;</span>
                    </div>
                    <div class="cf-video-embed" id="cf-embed-<?php echo (int) $v->id; ?>" style="display:none"></div>
                    <h3 class="cf-video-title"><?php echo htmlspecialchars($v->title, ENT_QUOTES, 'UTF-8'); ?></h3>
                    <?php if ($v->description) : ?>
                        <p class="cf-video-desc"><?php echo htmlspecialchars($v->description, ENT_QUOTES, 'UTF-8'); ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <script>
        var cfVideos = <?php echo json_encode(array_map(function($v) {
            return ['id' => (int)$v->id, 'type' => $v->type, 'source' => $v->source, 'width' => (int)$v->width ?: 640];
        }, $this->videos)); ?>;

        function cfPlay(id, thumbEl) {
            var v = cfVideos.find(function(x){ return x.id === id; });
            if (!v) return;
            var embed = document.getElementById('cf-embed-' + id);
            var src = v.source;
            var html = '';

            if (v.type === 'youtube') {
                var yid = src.match(/(?:v=|youtu\.be\/|embed\/)([a-zA-Z0-9_\-]{11})/);
                if (yid) html = '<iframe width="100%" height="315" src="https://www.youtube.com/embed/' + yid[1] + '?autoplay=1" frameborder="0" allowfullscreen allow="autoplay"></iframe>';
            } else if (v.type === 'vimeo') {
                var vid = src.match(/vimeo\.com\/(\d+)/);
                if (vid) html = '<iframe width="100%" height="315" src="https://player.vimeo.com/video/' + vid[1] + '?autoplay=1" frameborder="0" allowfullscreen></iframe>';
            } else {
                html = src;
            }

            if (html) {
                thumbEl.closest('.cf-video-card').querySelector('.cf-thumb').style.display = 'none';
                embed.innerHTML = html;
                embed.style.display = 'block';
            }
        }
        </script>
    <?php endif; ?>
</div>
