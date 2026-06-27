<?php

/**
 * @package     CineFrame
 * @subpackage  com_cineframe
 *
 * @copyright   (C) 2026 WebtechSolutions
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Webtech\Component\Cineframe\Administrator\Table;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use Joomla\Event\DispatcherInterface;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Video table.
 */
class VideoTable extends Table
{
    public function __construct(DatabaseDriver $db, ?DispatcherInterface $dispatcher = null)
    {
        $this->typeAlias = 'com_cineframe.video';

        parent::__construct('#__cineframe_videos', 'id', $db, $dispatcher);
    }

    public function store($updateNulls = true)
    {
        $date = Factory::getDate()->toSql();
        $user = Factory::getApplication()->getIdentity();

        if (!$this->id) {
            if (empty($this->created)) {
                $this->created = $date;
            }

            if (empty($this->created_by) && $user) {
                $this->created_by = (int) $user->id;
            }
        } else {
            $this->modified = $date;
        }

        return parent::store($updateNulls);
    }

    public function check(): bool
    {
        $this->title = trim((string) $this->title);

        if ($this->title === '') {
            $this->setError(Text::_('COM_CINEFRAME_ERR_TITLE_REQUIRED'));

            return false;
        }

        if (!\in_array($this->type, ['youtube', 'vimeo', 'video', 'embed'], true)) {
            $this->type = 'youtube';
        }

        if ($this->type === 'embed' && !$this->isIframeEmbed((string) $this->source)) {
            $this->setError(Text::_('COM_CINEFRAME_ERR_IFRAME_REQUIRED'));

            return false;
        }

        return parent::check();
    }

    private function isIframeEmbed(string $source): bool
    {
        return preg_match('~^\s*<iframe\b[^>]*>.*?</iframe>\s*$~is', $source) === 1;
    }
}
