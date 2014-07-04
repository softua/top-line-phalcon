<?php
/**
 * Created by Ruslan Koloskov
 * Date: 25.06.14
 * Time: 0:23
 */

namespace App\Controllers;
use App\Models;
use App;

class SalesController extends BaseFrontController
{
	public function initialize()
	{
		parent::initialize();
		$this->tag->appendTitle('Акционные предложения');

		$this->view->sidebar_categories = \App\Category::getMainCategories($this->di, false);
	}

	public function notFoundAction()
	{
		$this->response->setStatusCode(404, 'Not found');
		echo $this->view->render('sales/notfound');
	}

	public function indexAction()
	{
		$currentPage = $this->request->getQuery('page', 'int');

		$sales = App\Sale::getSales($this->di, true);
		$data = new App\Paginator($this->di, $sales, 10, $currentPage);
		$data->paginate($this->url->get('sales/?page='));

		$this->view->data = $data;

		echo $this->view->render('sales/list');
	}

	public function showAction()
	{
		$seoName = trim(strip_tags($this->dispatcher->getParams()[0]));
		$currentPage = $this->request->getQuery('page', 'int');

		if (!$seoName) {
			return $this->response->redirect('sales/notfound');
		}
		$page = App\Sale::getSaleBySeoName($this->di, $seoName, true);

		if ($page->hasProducts()) {
			$newPaginator = new App\Paginator($this->di, $page->getProducts(), 6, $currentPage);
			$products = $newPaginator->paginate('sales/show/' . $page->seoName . '?page=');
			$this->view->products = $products;
		}

		$this->view->page = $page;

		echo $this->view->render('sales/description');
	}
}