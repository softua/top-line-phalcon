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

		$this->view->sidebar_categories = Models\Category::getMainCategories($this->di, false);
	}

	public function notFoundAction()
	{
		$this->response->setStatusCode(404, 'Not found');
		echo $this->view->render('sales/notfound');
	}

	public function indexAction()
	{
		$currentPage = $this->request->getQuery('page', 'int');

		$sales = Models\Sale::getSales(true);
		$data = new App\Paginator($sales, 10, $currentPage);
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
		$page = Models\Sale::getSaleBySeoName($seoName, true);

		if ($page->hasProducts()) {
			$newPaginator = new App\Paginator($page->getProducts(), 6, $currentPage);
			$products = $newPaginator->paginate('sales/show/' . $page->seoName . '?page=');
			$this->view->products = $products;
		}

		$this->view->page = $page;

		echo $this->view->render('sales/description');
	}

	public function productAction()
	{
		$seoName = trim(strip_tags($this->dispatcher->getParams()[0]));
		$currentPage = $this->request->getQuery('page', 'int');
		if (!$seoName) return $this->response->redirect('sales/notfound');

		$product = Models\Product::getProductBySeoName($seoName, false, false);
		if (!$product) return $this->response->redirect('sales/notfound');

		if (!$product->getSales()) return $this->response->redirect('sales/');
		if (count($product->getSales()) == 1) {
			/** @var Models\Sale $page */
			$page = $product->getSales()[0];
			$page->setImages();

			if ($page->hasProducts()) {
				$newPaginator = new App\Paginator($page->getProducts(), 6, $currentPage);
				$products = $newPaginator->paginate('sales/product/' . $product->seo_name . '?page=');
				$this->view->products = $products;
			}

			$this->view->page = $page;

			echo $this->view->render('sales/description');
		}
		else {
			$currentPage = $this->request->getQuery('page', 'int');
			$sales = $product->getSales();
			$newPaginator = new App\Paginator($sales, 10, $currentPage);

			$this->view->data = $newPaginator->paginate('sales/product/' . $product->seo_name . '?page=');

			echo $this->view->render('sales/list');
		}
	}
}