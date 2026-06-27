<?php

/**
 * @package     CineFrame
 * @subpackage  com_cineframe
 *
 * @copyright   (C) 2026 WebtechSolutions
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Webtech\Component\Cineframe\Administrator\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;

\defined('_JEXEC') or die;

class CategoryModel extends AdminModel
{
    public function getTable($type = 'Category', $prefix = 'Administrator', $config = [])
    {
        return parent::getTable($type, $prefix, $config);
    }

    public function getForm($data = [], $loadData = true)
    {
        $form = $this->loadForm(
            'com_cineframe.category',
            'category',
            ['control' => 'jform', 'load_data' => $loadData]
        );

        return $form ?: false;
    }

    protected function loadFormData()
    {
        $app  = Factory::getApplication();
        $data = $app->getUserState('com_cineframe.edit.category.data', []);

        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }
}
