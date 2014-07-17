<?php
/**
 * Created by Ruslan Koloskov
 * Date: 18.06.14
 * Time: 21:57
 */

namespace App\Controllers;
use App\Models,
	App;

class PageController extends BaseFrontController
{
	public function initialize()
	{
		parent::initialize();

		$this->view->active_link = 'main';
		$this->view->sidebar_categories = Models\Category::getMainCategories(false);
	}

	public function showAction()
	{
		$seoName = trim(strip_tags($this->dispatcher->getParams()[0]));
		if (!$seoName) return $this->response->redirect();

		$page = Models\InfoPage::getPageBySeoName($seoName);
		if (!$page) return $this->response->redirect();
		else $this->view->page = $page;

		echo $this->view->render('staticPage');
	}
}