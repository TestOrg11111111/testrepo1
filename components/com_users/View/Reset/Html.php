<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Joomla\Component\Users\Site\View\Reset;

defined('_JEXEC') or die;

use Joomla\CMS\View\HtmlView;

/**
 * Reset view class for Users.
 *
 * @since  1.5
 */
class Html extends HtmlView
{
	/**
	 * The \JForm object
	 *
	 * @var  \JForm
	 */
	protected $form;

	/**
	 * The page parameters
	 *
	 * @var  \Joomla\Registry\Registry|null
	 */
	protected $params;

	/**
	 * The model state
	 *
	 * @var  \JObject
	 */
	protected $state;

	/**
	 * The page class suffix
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	protected $pageclass_sfx = '';

	/**
	 * Method to display the view.
	 *
	 * @param   string  $tpl  The template file to include
	 *
	 * @return  mixed
	 *
	 * @since   1.5
	 */
	public function display($tpl = null)
	{
		// This name will be used to get the model
		$name = $this->getLayout();

		// Check that the name is valid - has an associated model.
		if (!in_array($name, array('confirm', 'complete')))
		{
			$name = 'default';
		}

		if ('default' === $name)
		{
			$formname = 'Form';
		}
		else
		{
			$formname = ucfirst($this->_name) . ucfirst($name) . 'Form';
		}

		// Get the view data.
		$this->form   = $this->get($formname);
		$this->state  = $this->get('State');
		$this->params = $this->state->params;

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new \JViewGenericdataexception(implode("\n", $errors), 500);
		}

		// Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'), ENT_COMPAT, 'UTF-8');

		$this->prepareDocument();

		parent::display($tpl);
	}

	/**
	 * Prepares the document.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function prepareDocument()
	{
		$app   = \JFactory::getApplication();
		$menus = $app->getMenu();
		$title = null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', \JText::_('COM_USERS_RESET'));
		}

		$title = $this->params->get('page_title', '');

		if (empty($title))
		{
			$title = $app->get('sitename');
		}
		elseif ($app->get('sitename_pagetitles', 0) == 1)
		{
			$title = \JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
		}
		elseif ($app->get('sitename_pagetitles', 0) == 2)
		{
			$title = \JText::sprintf('JPAGETITLE', $title, $app->get('sitename'));
		}

		$this->document->setTitle($title);

		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetaData('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetaData('robots', $this->params->get('robots'));
		}
	}
}
