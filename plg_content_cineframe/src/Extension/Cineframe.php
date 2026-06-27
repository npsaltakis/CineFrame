<?php

/**
 * @package     CineFrame
 * @subpackage  plg_content_cineframe
 *
 * @copyright   (C) 2026 WebtechSolutions
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Webtech\Plugin\Content\Cineframe\Extension;

use Joomla\CMS\Document\HtmlDocument;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Uri\Uri;
use Joomla\Database\DatabaseAwareInterface;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Database\ParameterType;
use Joomla\Event\SubscriberInterface;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Replaces {cineframe ...} (and legacy {avsplayer ...}) shortcodes with
 * responsive YouTube / Vimeo / direct video URL / iframe embed players.
 */
final class Cineframe extends CMSPlugin implements SubscriberInterface, DatabaseAwareInterface
{
    use DatabaseAwareTrait;

    /**
     * Whether the player stylesheet has already been queued.
     *
     * @var bool
     */
    private $assetsLoaded = false;

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return ['onContentPrepare' => 'onContentPrepare'];
    }

    /**
     * Parse the shortcodes in the prepared content text.
     */
    public function onContentPrepare($event): void
    {
        if (\is_object($event) && method_exists($event, 'getItem')) {
            $row = $event->getItem();
        } else {
            $args = \is_object($event) && method_exists($event, 'getArguments') ? $event->getArguments() : [];
            $row  = $args['subject'] ?? ($args[1] ?? null);
        }

        if (!\is_object($row) || empty($row->text)) {
            return;
        }

        if (stripos($row->text, '{cineframe') === false && stripos($row->text, '{avsplayer') === false) {
            return;
        }

        $row->text = preg_replace_callback(
            '/\{(?:cineframe|avsplayer)\s+([^}]+)\}/i',
            [$this, 'replaceTag'],
            $row->text
        );
    }

    /**
     * Build the player markup for a single shortcode match.
     */
    private function replaceTag(array $match): string
    {
        $attrs  = $this->parseAttributes($match[1]);
        $type   = '';
        $source = '';
        $width  = isset($attrs['width']) ? (int) $attrs['width'] : 0;

        if (!empty($attrs['videoid'])) {
            $video = $this->loadVideo((int) $attrs['videoid']);

            if (!$video) {
                return '';
            }

            $type   = $video->type;
            $source = $video->source;

            if (!$width) {
                $width = (int) $video->width;
            }
        } elseif (!empty($attrs['url'])) {
            $source = $attrs['url'];
            $type   = $this->detectType($source);
        } elseif (!empty($attrs['youtube'])) {
            $type   = 'youtube';
            $source = $attrs['youtube'];
        } elseif (!empty($attrs['vimeo'])) {
            $type   = 'vimeo';
            $source = $attrs['vimeo'];
        } elseif (!empty($attrs['embed'])) {
            $type   = 'embed';
            $source = $attrs['embed'];
        }

        if ($type === '' || $source === '') {
            return '';
        }

        if (!$width) {
            $width = (int) $this->params->get('default_width', 640);
        }

        return $this->renderPlayer($type, $source, $width);
    }

    /**
     * Render the responsive player wrapper.
     */
    private function renderPlayer(string $type, string $source, int $width): string
    {
        $this->loadAssets();

        $maxWidth = $width > 0 ? ' style="max-width:' . $width . 'px"' : '';

        if ($type === 'embed') {
            $iframe = $this->cleanIframeEmbed($source);

            if ($iframe === '') {
                return '';
            }

            return '<div class="cineframe cineframe--embed"' . $maxWidth . '>' . $iframe . '</div>';
        }

        if ($type === 'video') {
            $videoUrl = $this->cleanVideoUrl($source);

            if ($videoUrl === '') {
                return '';
            }

            return '<div class="cineframe cineframe--video"' . $maxWidth . '>'
                . '<div class="cineframe__frame">'
                . '<video src="' . htmlspecialchars($videoUrl, ENT_QUOTES) . '" controls preload="metadata"></video>'
                . '</div></div>';
        }

        $embedUrl = '';

        if ($type === 'youtube') {
            $id = $this->youtubeId($source);

            if ($id !== '') {
                $embedUrl = 'https://www.youtube-nocookie.com/embed/' . rawurlencode($id);
            }
        } elseif ($type === 'vimeo') {
            $id = $this->vimeoId($source);

            if ($id !== '') {
                $embedUrl = 'https://player.vimeo.com/video/' . rawurlencode($id);
            }
        }

        if ($embedUrl === '') {
            return '';
        }

        return '<div class="cineframe"' . $maxWidth . '>'
            . '<div class="cineframe__frame">'
            . '<iframe src="' . htmlspecialchars($embedUrl, ENT_QUOTES) . '"'
            . ' frameborder="0" loading="lazy"'
            . ' allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"'
            . ' allowfullscreen referrerpolicy="strict-origin-when-cross-origin"></iframe>'
            . '</div></div>';
    }

    /**
     * Queue the self-contained player stylesheet once per request.
     */
    private function loadAssets(): void
    {
        if ($this->assetsLoaded) {
            return;
        }

        $this->assetsLoaded = true;

        $app = $this->getApplication();

        if (!$app || !$app->getDocument() instanceof HtmlDocument) {
            return;
        }

        $app->getDocument()->addStyleSheet(
            Uri::root(true) . '/media/plg_content_cineframe/css/cineframe.css',
            ['version' => '1.0.0']
        );
    }

    /**
     * Load a published video row from the library.
     */
    private function loadVideo(int $id)
    {
        if ($id <= 0) {
            return null;
        }

        $db    = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select($db->quoteName(['type', 'source', 'width']))
            ->from($db->quoteName('#__cineframe_videos'))
            ->where($db->quoteName('id') . ' = :id')
            ->where($db->quoteName('published') . ' = 1')
            ->bind(':id', $id, ParameterType::INTEGER);

        $db->setQuery($query);

        try {
            return $db->loadObject();
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Parse "key=value key2=value2" attribute strings.
     */
    private function parseAttributes(string $string): array
    {
        $attrs = [];

        if (preg_match_all('/(\w+)\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|(\S+))/', $string, $m, PREG_SET_ORDER)) {
            foreach ($m as $pair) {
                $value = $pair[2] !== '' ? $pair[2] : ($pair[3] !== '' ? $pair[3] : $pair[4]);
                $attrs[strtolower($pair[1])] = trim($value);
            }
        }

        return $attrs;
    }

    private function detectType(string $url): string
    {
        if (preg_match('/youtu\.?be/i', $url)) {
            return 'youtube';
        }

        if (stripos($url, 'vimeo') !== false) {
            return 'vimeo';
        }

        return 'video';
    }

    private function cleanVideoUrl(string $url): string
    {
        $url = trim($url);

        if ($url === '') {
            return '';
        }

        $scheme = parse_url($url, PHP_URL_SCHEME);

        if ($scheme !== null && !\in_array(strtolower($scheme), ['http', 'https'], true)) {
            return '';
        }

        return $url;
    }

    private function cleanIframeEmbed(string $source): string
    {
        $source = trim($source);

        if (preg_match('~^<iframe\b[^>]*>.*?</iframe>$~is', $source) !== 1) {
            return '';
        }

        return $source;
    }

    private function youtubeId(string $value): string
    {
        $value = trim($value);

        if (preg_match('/^[A-Za-z0-9_-]{11}$/', $value)) {
            return $value;
        }

        if (preg_match('~(?:youtube\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/|v/)|youtu\.be/)([A-Za-z0-9_-]{11})~i', $value, $m)) {
            return $m[1];
        }

        return '';
    }

    private function vimeoId(string $value): string
    {
        $value = trim($value);

        if (preg_match('/^\d+$/', $value)) {
            return $value;
        }

        if (preg_match('~vimeo\.com/(?:video/|channels/[^/]+/|groups/[^/]+/videos/)?(\d+)~i', $value, $m)) {
            return $m[1];
        }

        return '';
    }
}
