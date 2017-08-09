<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_plugins
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Joomla\Component\Plugins\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Controller\Controller as BaseController;
use Joomla\Component\Plugins\Administrator\Helper\PluginsHelper;

/**
 * Plugins master display controller.
 *
 * @since  1.5
 */
class Controller extends BaseController
{
	/**
	 * The default view.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $default_view = 'plugins';

	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe URL parameters and their variable types, for valid values see {@link \JFilterInput::clean()}.
	 *
	 * @return  static   This object to support chaining.
	 *
	 * @since   1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		// Load the submenu.
		PluginsHelper::addSubmenu($this->input->get('view', 'plugins'));

		$view   = $this->input->get('view', 'plugins');
		$layout = $this->input->get('layout', 'default');
		$id     = $this->input->getInt('extension_id');

		// Check for edit form.
		if ($view == 'plugin' && $layout == 'edit' && !$this->checkEditId('com_plugins.edit.plugin', $id))
		{
			// Somehow the person just went to the form - we don't allow that.
			$this->setMessage(\JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id), 'error');
			$this->setRedirect(\JRoute::_('index.php?option=com_plugins&view=plugins', false));

			return false;
		}

		parent::display();
	}
}
