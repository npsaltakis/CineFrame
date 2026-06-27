<?php

/**
 * @package     CineFrame
 * @subpackage  com_cineframe
 *
 * @copyright   (C) 2026 WebtechSolutions
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Webtech\Component\Cineframe\Administrator\View\Category;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

\defined('_JEXEC') or die;

class HtmlView extends BaseHtmlView
{
    protected $form;
    protected $item;

    public function display($tpl = null)
    {
        $this->form = $this->get('Form');
        $this->item = $this->get('Item');

        if (\count($errors = $this->get('Errors'))) {
            throw new \Exception(implode("\n", $errors), 500);
        }

        $this->addToolbar();

        parent::display($tpl);
    }

    protected function addToolbar(): void
    {
        Factory::getApplication()->getInput()->set('hidemainmenu', true);

        $isNew = empty($this->item->id);

        ToolbarHelper::title(Text::_($isNew ? 'COM_CINEFRAME_CATEGORY_NEW_TITLE' : 'COM_CINEFRAME_CATEGORY_EDIT_TITLE'), 'folder');

        $toolbar = Factory::getApplication()->getDocument()->getToolbar();

        $toolbar->apply('category.apply');
        $toolbar->save('category.save');
        $toolbar->cancel('category.cancel');
    }
}
