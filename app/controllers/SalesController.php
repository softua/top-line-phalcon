<?php
/**
 * Created by Ruslan Koloskov
 * Date: 25.06.14
 * Time: 0:23
 */

namespace App\Controllers;
use App\Models;

class SalesController extends BaseFrontController
{
	public function initialize()
	{
		parent::initialize();
		$this->tag->appendTitle('Акционные предложения');

		$mainCategories = Models\CategoryModel::find([
			'parent_id = 0',
			'order' => 'sort, name'
		]);
		$mainCategoriesForView = [];
		for ($i = 0; $i < count($mainCategories); $i++)
		{
			$mainCategoriesForView[$i]['name'] = $mainCategories[$i]->name;
			$areThereChildrenCats = Models\CategoryModel::findFirst([
				'parent_id = :id:',
				'bind' => ['id' => $mainCategories[$i]->id]
			]);
			if ($mainCategories[$i]->seo_name === 'servisnyie_uslugi_montaj') {
				$mainCategoriesForView[$i]['path'] = $this->url->get('service');
			}
			elseif ($areThereChildrenCats)
			{
				$mainCategoriesForView[$i]['path'] = '/catalog/show/' . $mainCategories[$i]->seo_name . '/';

			} else {

				$mainCategoriesForView[$i]['path'] = '/products/list/' . $mainCategories[$i]->seo_name . '/';
			}
		}

		$this->view->sidebar_categories = $mainCategoriesForView;
	}

	public function notFoundAction()
	{
		$this->response->setStatusCode(404, 'Not found');
		echo $this->view->render('sales/notfound');
	}

	public function indexAction()
	{
		$currentPage = $this->request->getQuery('page', 'int');
		$sales = Models\PageModel::find([
			'type_id = 5 AND public = 1',
			'order' => 'time, name'
		]);
		$salesArray = [];
		if (count($sales)) {
			foreach ($sales as $sale) {
				$tempSale = [];
				$tempSale['name'] = $sale->name;
				$tempSale['short_description'] = $sale->short_content;
				$tempSale['href'] = $this->url->get('sales/show/') . $sale->seo_name;
				$salesImages = Models\PageImageModel::find([
					'page_id = ?1',
					'bind' => [1 => $sale->id],
					'order' => 'sort'
				]);
				if ($salesImages->count()) {
					$imgPath = 'staticPages/images/' . $salesImages[0]->id . '__page_list.' . $salesImages[0]->extension;
					if (file_exists($imgPath)) {
						$tempSale['img'] = $this->url->getStatic($imgPath);
					} else {
						$tempSale['img'] = $this->url->getStatic('img/no_foto.png');
					}
				} else {
					$tempSale['img'] = $this->url->getStatic('img/no_foto.png');
				}
				$salesArray[] = $tempSale;
			}
		} else {
			$salesArray = null;
		}
		if ($salesArray) {
			$paginator = new \Phalcon\Paginator\Adapter\NativeArray([
				'data' => $salesArray,
				'limit' => 10,
				'page' => ($currentPage) ? $currentPage : 1
			]);
			$data = $paginator->getPaginate();
			$data->links = [];
			while (count($data->links) < $data->total_pages) {
				$data->links[] = $this->url->get('sales/?page=') . (count($data->links) + 1);
			}
		} else {
			$data = null;
		}

		$this->view->data = $data;

		echo $this->view->render('sales/list');
	}

	public function showAction()
	{
		$seoName = trim(strip_tags($this->dispatcher->getParams()[0]));
		if (!$seoName) {
			return $this->response->redirect('sales/notfound');
		}
		$page = Models\PageModel::findFirst([
			'seo_name = ?1',
			'bind' => [1 => $seoName]
		]);
		if ($page) {
			$pageForView = [];
		} else {
			return $this->response->redirect('sales/notfound');
		}
		$pageForView['name'] = $page->name;
		$pageForView['full_content'] = $page->full_content;
		$pageImages = $page->getImages(['order' => 'sort']);
		if (count($pageImages)) {
			$imgPath = 'staticPages/images/' . $pageImages[0]->id . '__page_description.' . $pageImages[0]->extension;
			if (file_exists($imgPath)) {
				$pageForView['img'] = $this->url->getStatic($imgPath);
			} else {
				$pageForView['img'] = $this->url->getStatic('img/no_foto.png');
			}
		} else {
			$pageForView['img'] = $this->url->getStatic('img/no_foto.png');
		}
		$pageProducts = $page->getProducts(['order' => '[\App\Models\ProductModel].price_uah DESC']);
		if (count($pageProducts)) {
			$pageForView['products'] = [];
			foreach ($pageProducts as $prod) {
				$tempProd = [];
				$tempProd['name'] = $prod->name;
				$tempProd['articul'] = $prod->articul;
				$tempProd['short_description'] = $prod->short_description;
				$tempProd['link'] = $this->url->get('products/show/') . $prod->seo_name;
				$imgs = $prod->getImages(['order' => 'sort']);
				if (count($imgs)) {
					$imgPath = 'products/' . $prod->id . '/images/' . $imgs[0]->id . '__product_list.' . $imgs[0]->extension;
					if (file_exists($imgPath)) {
						$tempProd['img'] = $this->url->getStatic($imgPath);
					} else {
						$tempProd['img'] = $this->url->getStatic('img/no_foto.png');
					}
				} else {
					$tempProd['img'] = $this->url->getStatic('img/no_foto.png');
				}
				$pageForView['products'][] = $tempProd;
			}
		} else {
			$pageForView['products'] = null;
		}

		$this->view->data = $pageForView;

		echo $this->view->render('sales/description');
	}
} 