<?php

/**
 * @package     CineFrame
 * @subpackage  com_cineframe (site)
 *
 * @copyright   (C) 2026 WebtechSolutions
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Webtech\Component\Cineframe\Site\View\Category;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Pagination\Pagination;

class HtmlView extends BaseHtmlView
{
    public ?object $category = null;
    public array   $videos   = [];
    public ?Pagination $pagination = null;

    public function display($tpl = null): void
    {
        $catid = (int) Factory::getApplication()->getInput()->getInt('id', 0);

        /** @var \Webtech\Component\Cineframe\Site\Model\CategoryModel $model */
        $model = $this->getModel();
        $this->category   = $model->getCategory($catid);
        $this->videos     = $model->getItems();
        $this->pagination = $model->getPagination();

        parent::display($tpl);
    }
}
