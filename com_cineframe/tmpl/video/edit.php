<?php

/**
 * @package     CineFrame
 * @subpackage  com_cineframe
 *
 * @copyright   (C) 2026 WebtechSolutions
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.formvalidator');
?>
<form action="<?php echo Route::_('index.php?option=com_cineframe&layout=edit&id=' . (int) $this->item->id); ?>"
      method="post" name="adminForm" id="video-form" class="form-validate">
    <div class="row">
        <div class="col-lg-9">
            <fieldset class="adminform">
                <?php echo $this->form->renderField('title'); ?>
                <?php echo $this->form->renderField('catid'); ?>
                <?php echo $this->form->renderField('type'); ?>
                <?php echo $this->form->renderField('source'); ?>
                <?php echo $this->form->renderField('thumb'); ?>
                <?php echo $this->form->renderField('description'); ?>
                <?php echo $this->form->renderField('width'); ?>
                <?php echo $this->form->renderField('published'); ?>
            </fieldset>
        </div>
        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title"><?php echo Text::_('COM_CINEFRAME_USAGE'); ?></h4>
                    <p class="small mb-2"><?php echo Text::_('COM_CINEFRAME_USAGE_HINT'); ?></p>
                    <?php if (!empty($this->item->id)) : ?>
                        <code>{cineframe videoid=<?php echo (int) $this->item->id; ?> width=540}</code>
                    <?php else : ?>
                        <em class="small"><?php echo Text::_('COM_CINEFRAME_SAVE_FIRST'); ?></em>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="task" value="">
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
