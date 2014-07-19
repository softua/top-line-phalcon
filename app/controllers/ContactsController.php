<?php
/**
 * Created by Ruslan Koloskov
 * Date: 18.06.14
 * Time: 21:57
 */

namespace App\Controllers;
use App\Models;

class ContactsController extends BaseFrontController
{
	public function initialize()
	{
		parent::initialize();

		$this->tag->appendTitle('Контакты');
		$this->view->active_link = 'contacts';
	}

	public function indexAction()
	{
		$this->view->sidebar_categories = Models\Category::getMainCategories();

		echo $this->view->render('contacts');
	}
} 