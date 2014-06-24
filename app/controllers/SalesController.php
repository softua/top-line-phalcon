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

		$mainCategories = Models\Category::find([
			'parent_id = 0',
			'order' => 'sort, name'
		]);
		$mainCategoriesForView = [];
		for ($i = 0; $i < count($mainCategories); $i++)
		{
			$mainCategoriesForView[$i]['name'] = $mainCategories[$i]->name;
			$areThereChildrenCats = Models\Category::findFirst([
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
		$sales = Models\Page::find([
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
				$salesImages = Models\PageImage::find([
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
		$page = Models\Page::findFirst([
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
		$pageImages = Models\PageImage::find([
			'page_id = ?1',
			'bind' => [1 => $page->id],
			'order' => 'sort'
		]);
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

		$this->view->data = $pageForView;

		echo $this->view->render('sales/description');
	}
} 