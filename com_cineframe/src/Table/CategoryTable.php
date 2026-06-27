<?php

/**
 * @package     CineFrame
 * @subpackage  com_cineframe
 *
 * @copyright   (C) 2026 WebtechSolutions
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Webtech\Component\Cineframe\Administrator\Table;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use Joomla\Event\DispatcherInterface;

\defined('_JEXEC') or die;

class CategoryTable extends Table
{
    public function __construct(DatabaseDriver $db, ?DispatcherInterface $dispatcher = null)
    {
        $this->typeAlias = 'com_cineframe.category';
        parent::__construct('#__cineframe_categories', 'id', $db, $dispatcher);
    }

    public function check(): bool
    {
        $this->name = trim((string) $this->name);

        if ($this->name === '') {
            $this->setError(Text::_('COM_CINEFRAME_ERR_CAT_NAME_REQUIRED'));
            return false;
        }

        return parent::check();
    }
}
