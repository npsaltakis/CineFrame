<?php

/**
 * @package     CineFrame
 * @subpackage  com_cineframe
 *
 * @copyright   (C) 2026 WebtechSolutions
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Webtech\Component\Cineframe\Administrator\View\Videos;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Videos list view.
 */
class HtmlView extends BaseHtmlView
{
    protected $items;
    protected $pagination;
    protected $state;

    public function display($tpl = null)
    {
        $this->items      = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state      = $this->get('State');

        if (\count($errors = $this->get('Errors'))) {
            throw new \Exception(implode("\n", $errors), 500);
        }

        $this->addToolbar();

        parent::display($tpl);
    }

    protected function addToolbar(): void
    {
        ToolbarHelper::title(Text::_('COM_CINEFRAME_VIDEOS_TITLE'), 'play');

        $toolbar = Factory::getApplication()->getDocument()->getToolbar();

        $toolbar->addNew('video.add');
        $toolbar->edit('video.edit');
        $toolbar->publish('videos.publish')->listCheck(true);
        $toolbar->unpublish('videos.unpublish')->listCheck(true);
        $toolbar->delete('videos.delete')
            ->message(Text::_('COM_CINEFRAME_CONFIRM_DELETE'))
            ->listCheck(true);
    }
}
