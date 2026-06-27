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

class CategoryModel extends BaseDatabaseModel
{
    public function getCategory(int $id): ?object
    {
        $db    = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__cineframe_categories'))
            ->where($db->quoteName('id') . ' = :id')
            ->where($db->quoteName('published') . ' = 1')
            ->bind(':id', $id, \Joomla\Database\ParameterType::INTEGER);

        return $db->setQuery($query)->loadObject() ?: null;
    }

    public function getVideos(int $catid): array
    {
        $db    = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select(['id', 'title', 'type', 'source', 'thumb', 'description', 'width'])
            ->from($db->quoteName('#__cineframe_videos'))
            ->where($db->quoteName('catid') . ' = :catid')
            ->where($db->quoteName('published') . ' = 1')
            ->bind(':catid', $catid, \Joomla\Database\ParameterType::INTEGER)
            ->order('ordering ASC, id ASC');

        return $db->setQuery($query)->loadObjectList() ?: [];
    }
}
