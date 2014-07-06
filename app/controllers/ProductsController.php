<?php
/**
 * Created by Ruslan Koloskov
 * Date: 13.06.14
 * Time: 9:48
 */

namespace App\Controllers;
use App\Category;
use App\Models;
use App\Page;
use App\Paginator;
use App\Product;

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

		$this->view->sidebar_categories = Category::getMainCategories($this->di);

		echo $this->view->render('products/notfound');
	}

	public function listAction()
	{
		$catSeoName = $this->dispatcher->getParams()[0];
		if (!$catSeoName) return $this->response->redirect('catalog/');

		$category = Category::getCategoryBySeoName($this->di, $catSeoName, true);
		if (!$category) {
			return $this->response->redirect('catalog/');
		}

		// Список товаров
		$sort = $this->request->getQuery('sort', ['trim', 'striptags']);
		$page = $this->request->getQuery('page', ['trim', 'int']);

		$products = Product::getProductsByCategories($this->di, [$category], false, true, true, $sort);
		if ($products) {
			$paginator = new Paginator($this->di, $products, 10, $page);
			$data = $paginator->paginate('products/list/' . $category->seo_name . '/?sort=' . $sort . '&page=');
		} else $data = null;

		$this->view->breadcrumbs = $category->getParentsCategories();
		$this->view->name = $category->name;
		$this->view->sidebar_categories = $category::getMainCategories($this->di, false, [$category->seo_name]);
		$this->view->data = $data;
		$this->view->sort = ($sort) ? $sort : 'DESC';

		echo $this->view->render('products/list');
	}

	public function showAction()
	{
		$productSeoName = trim(strip_tags($this->dispatcher->getParams()[0]));
		if (!$productSeoName) {
			return $this->response->redirect('products/notfound');
		}
		$product = Product::getProductBySeoName($this->di, $productSeoName, true);
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
		$currentProductForView['country'] = Models\CountryModel::findFirst([$product->country_id])->name;
		if ($product->brand)
		{
			$currentProductForView['brand'] = $product->brand;
		}
		if ($product->short_description)
		{
			$currentProductForView['short_desc'] = $product->short_description;
		}
		$prodParams = Models\ProductParamModel::getParamsByProductId($product->id);
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
		$this->view->sidebar_categories = Category::getMainCategories($this->di, false, $product->getCategories());

		echo $this->view->render('products/product');
	}

	public function sortAction()
	{
		if (!$this->request->isAjax() || !$this->request->isPost()) {
			return $this->dispatcher->forward([
				'controller' => 'products',
				'action' => 'notfound'
			]);
		}
		$category = trim(strip_tags($this->request->getPost('category')));
		$sort = trim(strip_tags($this->request->getPost('sort')));
		$currentCategory = Models\CategoryModel::findFirst([
			'seo_name = ?1',
			'bind' => [1 => $category]
		]);
		if (!$currentCategory) {
			echo 'false';
			return false;
		}
		// Список товаров
		$prodCats = Models\ProductCategoryModel::find([
			'category_id = :categoryId:',
			'bind' => ['categoryId' => $currentCategory->id]
		]);

		if (count($prodCats) > 0)
		{
			$queryString = '';
			$queryStringArray = [];
			foreach ($prodCats as $prodCat)
			{
				$queryStringArray[] = $prodCat->product_id;
			}
			$queryStringArray = array_unique($queryStringArray);
			for ($j = 0; $j < count($queryStringArray); $j++)
			{
				if ($j == 0)
					$queryString = 'id = ' . $queryStringArray[$j];
				else
					$queryString .= ' OR id = ' . $queryStringArray[$j];
			}

			$productList = Models\ProductModel::find([
				$queryString,
				'order' => 'price_uah ' . $sort
			]);

			$productsForView = [];
			foreach ($productList as $product)
			{
				if (!$product->public)
					continue;
				$tempProd['name'] = $product->name;
				$tempProd['articul'] = $product->articul;
				$tempProd['short_desc'] = $product->short_description;
				$tempProd['path'] = '/products/show/' . $product->seo_name;

				$prodImages = Models\ProductImageModel::find([
					'product_id = :id:',
					'bind' => ['id' => $product->id],
					'order' => 'sort'
				]);

				if (count($prodImages) > 0)
				{
					$pathname = 'products/' . $prodImages[0]->product_id . '/images/' . $prodImages[0]->id . '__product_list.' . $prodImages[0]->extension;
					if (file_exists($pathname))
					{
						$tempProd['img'] = '/' . $pathname;
					} else {
						$tempProd['img'] = '/img/no_foto.png';
					}
				} else {
					$tempProd['img'] = '/img/no_foto.png';
				}
				$productsForView[] = $tempProd;
			}
		} else {
			$productsForView = null;
		}

		if ($productsForView) {
			echo json_encode($productsForView);
			return true;
		} else {
			echo 'false';
			return false;
		}
	}
}