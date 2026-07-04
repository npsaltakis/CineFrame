<?php

/**
 * @package     CineFrame
 * @subpackage  com_cineframe
 *
 * @copyright   (C) 2026 WebtechSolutions
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Webtech\Component\Cineframe\Administrator\Model;

use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\QueryInterface;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Methods supporting a list of video records.
 */
class VideosModel extends ListModel
{
    public function __construct($config = [], ?MVCFactoryInterface $factory = null)
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = [
                'id', 'a.id',
                'title', 'a.title',
                'catid', 'a.catid',
                'category_name', 'c.name',
                'type', 'a.type',
                'published', 'a.published',
                'ordering', 'a.ordering',
            ];
        }

        parent::__construct($config, $factory);
    }

    protected function populateState($ordering = 'a.id', $direction = 'DESC')
    {
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search', '');
        $this->setState('filter.search', $search);

        $published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
        $this->setState('filter.published', $published);

        $catid = $this->getUserStateFromRequest($this->context . '.filter.catid', 'filter_catid', '');
        $this->setState('filter.catid', $catid);

        parent::populateState($ordering, $direction);
    }

    protected function getStoreId($id = '')
    {
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.published');
        $id .= ':' . $this->getState('filter.catid');

        return parent::getStoreId($id);
    }

    public function getCategoryOptions(): array
    {
        $db    = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select([$db->quoteName('id', 'value'), $db->quoteName('name', 'text')])
            ->from($db->quoteName('#__cineframe_categories'))
            ->where($db->quoteName('published') . ' = 1')
            ->order($db->quoteName('ordering') . ' ASC, ' . $db->quoteName('name') . ' ASC');

        return $db->setQuery($query)->loadObjectList() ?: [];
    }

    protected function getListQuery()
    {
        $db    = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select(['a.*', $db->quoteName('c.name', 'category_name')])
            ->from($db->quoteName('#__cineframe_videos', 'a'))
            ->leftJoin(
                $db->quoteName('#__cineframe_categories', 'c') . ' ON ' .
                $db->quoteName('c.id') . ' = ' . $db->quoteName('a.catid')
            );

        // Filter by published state.
        $published = (string) $this->getState('filter.published');

        if (is_numeric($published)) {
            $published = (int) $published;
            $query->where($db->quoteName('a.published') . ' = :published')
                ->bind(':published', $published, \Joomla\Database\ParameterType::INTEGER);
        }

        // Filter by category.
        $catid = $this->getState('filter.catid');

        if (is_numeric($catid) && (int) $catid > 0) {
            $catid = (int) $catid;
            $query->where($db->quoteName('a.catid') . ' = :catid')
                ->bind(':catid', $catid, \Joomla\Database\ParameterType::INTEGER);
        }

        // Filter by keyword search (title, description, source, or id:id).
        $search = $this->getState('filter.search');

        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $sid = (int) substr($search, 3);
                $query->where($db->quoteName('a.id') . ' = :sid')
                    ->bind(':sid', $sid, \Joomla\Database\ParameterType::INTEGER);
            } else {
                $like = '%' . $search . '%';
                $query->where(
                    '('
                    . $db->quoteName('a.title') . ' LIKE :searchTitle OR '
                    . $db->quoteName('a.description') . ' LIKE :searchDescription OR '
                    . $db->quoteName('a.source') . ' LIKE :searchSource'
                    . ')'
                )
                    ->bind(':searchTitle', $like)
                    ->bind(':searchDescription', $like)
                    ->bind(':searchSource', $like);
            }
        }

        // Ordering.
        $orderCol  = $this->state->get('list.ordering', 'a.id');
        $orderDirn = $this->state->get('list.direction', 'DESC');
        $query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

        return $query;
    }
}
