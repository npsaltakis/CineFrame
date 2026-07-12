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

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\ParameterType;

class CategoryModel extends ListModel
{
    public function getCategory(int $id): ?object
    {
        $db    = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__cineframe_categories'))
            ->where($db->quoteName('id') . ' = :id')
            ->where($db->quoteName('published') . ' = 1')
            ->bind(':id', $id, ParameterType::INTEGER);

        return $db->setQuery($query)->loadObject() ?: null;
    }

    protected function populateState($ordering = null, $direction = null)
    {
        $catid = Factory::getApplication()->getInput()->getInt('id', 0);
        $this->setState('filter.catid', $catid);

        parent::populateState($ordering, $direction);
    }

    protected function getStoreId($id = '')
    {
        $id .= ':' . $this->getState('filter.catid');

        return parent::getStoreId($id);
    }

    protected function getListQuery()
    {
        $db    = $this->getDatabase();
        $catid = (int) $this->getState('filter.catid');

        $query = $db->getQuery(true)
            ->select(['id', 'title', 'type', 'source', 'thumb', 'description', 'width'])
            ->from($db->quoteName('#__cineframe_videos'))
            ->where($db->quoteName('catid') . ' = :catid')
            ->where($db->quoteName('published') . ' = 1')
            ->bind(':catid', $catid, ParameterType::INTEGER)
            ->order('ordering ASC, id ASC');

        return $query;
    }
}
