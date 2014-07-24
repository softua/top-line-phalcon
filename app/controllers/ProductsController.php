<?php
/**
 * Created by Ruslan Koloskov
 * Date: 13.06.14
 * Time: 9:48
 */

namespace App\Controllers;
use App\Paginator,
	App\Models;

class ProductsController extends BaseFrontController
{
	public function initialize()
	{
		parent::initialize();

		$this->tag->appendTitle('Каталог');
		$this->view->active_link = 'catalog';
	}

	public function notFoundAction()
	{
		$this->response->setStatusCode(404, 'Not found')->send();

		$this->view->sidebar_categories = Models\Category::getMainCategories($this->di);

		echo $this->view->render('products/notfound');
	}

	public function listAction()
	{
		$catSeoName = $this->dispatcher->getParams()[0];
		if (!$catSeoName) return $this->response->redirect('catalog/');

		$category = Models\Category::getCategoryBySeoName($catSeoName, true);
		if (!$category) {
			return $this->response->redirect('catalog/');
		}

		// Список товаров
		$sort = $this->request->getQuery('sort', ['trim', 'striptags']);
		$page = $this->request->getQuery('page', ['trim', 'int']);

		$products = Models\Product::getProductsByCategories([$category], false, true, true, $sort);
		if ($products) {
			$paginator = new Paginator($products, 10, $page);
			$data = $paginator->paginate('products/list/' . $category->seo_name . '/?sort=' . $sort . '&page=');
		}
		else $data = null;

		$this->view->breadcrumbs = $category->getParentsCategories();
		$this->view->name = $category->name;
		$this->view->sidebar_categories = Models\Category::getMainCategories(false, [$category->seo_name]);
		$this->view->data = $data;
		$this->view->sort = ($sort) ? $sort : 'DESC';

		echo $this->view->render('products/list');
	}

	public function list2Action()
	{
		$catSeoName = $this->dispatcher->getParams()[0];
		if (!$catSeoName) return $this->response->redirect('catalog/');

		$category = Models\Category::getCategoryBySeoName($catSeoName, true);
		if (!$category) {
			return $this->response->redirect('catalog/');
		}

		// Список товаров
		$sort = $this->request->getQuery('sort', ['trim', 'striptags']);

		$products = Models\Product::getProductsByCategories([$category], false, false, true, $sort);

		$this->view->breadcrumbs = $category->getParentsCategories();
		$this->view->name = $category->name;
		$this->view->sidebar_categories = Models\Category::getMainCategories(false, [$category->seo_name]);
		$this->view->products = $products;
		$this->view->sort = ($sort) ? $sort : 'DESC';

		echo $this->view->render('products/list-simple');
	}

	public function showAction()
	{
		$productSeoName = trim(strip_tags($this->dispatcher->getParams()[0]));
		if (!$productSeoName) {
			return $this->response->redirect('products/notfound');
		}
		$product =Models\Product::getProductBySeoName($productSeoName, true);
		if (!$product) {
			return $this->response->redirect('products/notfound');
		}

		// Формируем хлебные крошки
		$breadcrumbs = $product->getMainCategory()->getParentsCategories();
		$breadcrumbs[] = $product;

		//Формируем данные товара для представления TODO: не знаю, зачем так сделал. Потом нужно будет переделать.

		$currentProductForView = [];
		$currentProductForView['name'] = $product->name;
		$currentProductForView['articul'] = $product->articul;
		$currentProductForView['seo_name'] = $product->seo_name;
		$currentProductForView['main_curancy'] = $product->main_curancy;
		$priceName = 'price_' . $product->main_curancy;
		if ($product->$priceName == 0)
		{
			$currentProductForView['alt_price'] = $product->price_alternative;
		} else {
			$currentProductForView['price'] = number_format($product->$priceName, 2, '.', ' ');
		}
		$currentProductForView['country'] = Models\Country::findFirst([$product->country_id])->name;
		if ($product->brand)
		{
			$currentProductForView['brand'] = $product->brand;
		}
		if ($product->short_description)
		{
			$currentProductForView['short_desc'] = $product->short_description;
		}
		$prodParams = Models\ProductParam::getParamsByProductId($product->id);
		if ($prodParams)
		{
			foreach ($prodParams as $param)
			{
				$currentProductForView['parameters'][$param->name] =  $param->value;
			}
		}
		if ($product->full_description)
		{
			$currentProductForView['full_desc'] = $product->full_description;
		}
		$currentProductForView['sales'] = $product->hasSales();
		$currentProductForView['novelty'] = $product->novelty;

		// Картинки
		$currentProductForView['images'] = $product->getImages();

		// Видео
		$currentProductForView['video'] = $product->getVideos();

		// Файлы
		$currentProductForView['files'] = $product->getFiles();

		$this->view->breadcrumbs = $breadcrumbs;
		$this->view->product = $currentProductForView;
		$this->view->sidebar_categories = Models\Category::getMainCategories(false, $product->getCategories());

		echo $this->view->render('products/product');
	}
}