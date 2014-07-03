<?php
/**
 * Created by Ruslan Koloskov
 * Date: 18.06.14
 * Time: 21:57
 */

namespace App\Controllers;
use App\Models;

class ServiceController extends BaseFrontController
{
	public function initialize()
	{
		parent::initialize();

		$this->tag->appendTitle('Сервис');
		$this->view->active_link = 'service';
	}

	public function indexAction()
	{
		$this->view->sidebar_categories = \App\Category::getMainCategories($this->di, false, ['servisnyie_uslugi_montaj']);

		echo $this->view->render('service');
	}
} 