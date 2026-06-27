<?php

/**
 * @package     CineFrame
 * @subpackage  com_cineframe (site)
 *
 * @copyright   (C) 2026 WebtechSolutions
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Webtech\Component\Cineframe\Site\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\BaseDatabaseModel;

class CategoriesModel extends BaseDatabaseModel
{
    public function getItems(): array
    {
        $db    = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select(['c.id', 'c.name', 'c.ordering', $db->quoteName('vc.total', 'video_count')])
            ->from($db->quoteName('#__cineframe_categories', 'c'))
            ->leftJoin(
                '(' . $db->getQuery(true)
                    ->select(['catid', 'COUNT(*) AS total'])
                    ->from($db->quoteName('#__cineframe_videos'))
                    ->where('published = 1')
                    ->group('catid') . ') AS vc ON vc.catid = c.id'
            )
            ->where($db->quoteName('c.published') . ' = 1')
            ->order('c.ordering ASC, c.name ASC');

        return $db->setQuery($query)->loadObjectList() ?: [];
    }
}
