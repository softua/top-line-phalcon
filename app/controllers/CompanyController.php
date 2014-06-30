<?php
/**
 * Created by Ruslan Koloskov
 * Date: 18.06.14
 * Time: 21:57
 */

namespace App\Controllers;
use App\Models;

class CompanyController extends BaseFrontController
{
	public function initialize()
	{
		parent::initialize();
	}

	public function notFoundAction()
	{
		$this->tag->prependTitle('Ошибка');
		$this->response->setStatusCode(404, 'Not found')->send();
		$sidebarCategories = Models\CategoryModel::find([
			'parent_id = 0',
			'order' => 'sort'
		]);

		if (count($sidebarCategories) > 0)
		{
			$sidebarCategoriesForView = [];
			foreach ($sidebarCategories as $category)
			{
				$tempCategory = [];
				$categoryChildren = Models\CategoryModel::findFirst([
					'parent_id = :id:',
					'bind' => ['id' => $category->id]
				]);
				if ($categoryChildren)
				{
					$tempCategory['path'] = '/catalog/show/' . $category->seo_name . '/';
				} else {
					$tempCategory['path'] = '/products/list/' . $category->seo_name . '/';
				}
				$tempCategory['active'] = false;
				$tempCategory['name'] = $category->name;
				$sidebarCategoriesForView[] = $tempCategory;
			}
			$this->view->sidebar_categories = $sidebarCategoriesForView;
		} else {
			$this->view->sidebar_categories = null;
		}
		echo $this->view->render('company/notfound');
	}

	public function aboutUsAction()
	{
		$this->tag->appendTitle('О компании');

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
			if ($areThereChildrenCats)
			{
				$mainCategoriesForView[$i]['path'] = '/catalog/show/' . $mainCategories[$i]->seo_name . '/';

			} else {

				$mainCategoriesForView[$i]['path'] = '/products/list/' . $mainCategories[$i]->seo_name . '/';
			}

			$catImage = Models\CategoryImageModel::findFirst([
				'category_id = :id:',
				'bind' => ['id' => $mainCategories[$i]->id]
			]);

			if ($catImage && file_exists($catImage->pathname))
			{
				$mainCategoriesForView[$i]['img'] = '/' . $catImage->pathname;
			} else {
				$mainCategoriesForView[$i]['img'] = '/img/no_foto_110x110.png';
			}
		}

		$this->view->active_link = 'company';
		$this->view->sidebar_categories = $mainCategoriesForView;

		echo $this->view->render('company/company');
	}

	public function showAction()
	{
		$seoName = trim(strip_tags($this->dispatcher->getParams()[0]));
		if ($seoName) {
			$page = Models\PageModel::findFirst([
				'seo_name = ?1',
				'bind' => [1 => $seoName]
			]);
			if (!$page) {
				return $this->response->redirect('company/notfound');
			}
		} else {
			return $this->response->redirect('company/notfound');
		}

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
			if ($areThereChildrenCats)
			{
				$mainCategoriesForView[$i]['path'] = '/catalog/show/' . $mainCategories[$i]->seo_name . '/';

			} else {

				$mainCategoriesForView[$i]['path'] = '/products/list/' . $mainCategories[$i]->seo_name . '/';
			}
		}

		$this->tag->prependTitle($page->name);
		$pageForView = [];
		$pageForView['name'] = $page->name;
		$pageForView['full_content'] = $page->full_content;

		$this->view->current_page = $pageForView;
		$this->view->sidebar_categories = $mainCategoriesForView;

		echo $this->view->render('company/description');
	}

	public function newsAction()
	{
		$this->tag->appendTitle('Новости');

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
			if ($areThereChildrenCats)
			{
				$mainCategoriesForView[$i]['path'] = $this->url->get('catalog/show/') . $mainCategories[$i]->seo_name . '/';

			} else {

				$mainCategoriesForView[$i]['path'] = $this->url->get('products/list/') . $mainCategories[$i]->seo_name . '/';
			}
		}

		$this->view->active_link = 'company';
		$this->view->sidebar_categories = $mainCategoriesForView;

		// Описание новости
		if ($this->dispatcher->getParams()[0]) {
			$seoName = $this->dispatcher->getParams()[0];
			$oneNews = Models\PageModel::findFirst([
				'type_id = 4 AND seo_name = ?1',
				'bind' => [1 => $seoName]
			]);
			if ($oneNews) {
				$oneNewsForView = [];
				$oneNewsForView['name'] = $oneNews->name;
				$oneNewsForView['time'] = date('d.m.Y', strtotime($oneNews->time));
				$newsImages = Models\PageImageModel::find([
					'page_id = ?1',
					'bind' => [1 => $oneNews->id],
					'order' => 'sort'
				]);
				if (count($newsImages)) {
					$imgPath = 'staticPages/images/' . $newsImages[0]->id . '__page_description.' . $newsImages[0]->extension;
					if (file_exists($imgPath)) {
						$oneNewsForView['img'] = $this->url->getStatic($imgPath);
					} else {
						$oneNewsForView['img'] = $this->url->getStatic('img/no_foto.png');
					}
				} else {
					$oneNewsForView['img'] = $this->url->getStatic('img/no_foto.png');
				}
				$oneNewsForView['full_content'] = $oneNews->full_content;

				$this->view->news = $oneNewsForView;

				echo $this->view->render('news/description');
			} else {
				return $this->response->redirect('company/notfound');
			}
		} else { // Список новостей
			$sort = $this->request->getQuery('sort', 'int');
			$currentPage = $this->request->getQuery('page', 'int');
			if (!$sort || $sort === 1) {
				$news = Models\PageModel::find([
					'type_id = 4',
					'order' => 'time DESC'
				]);
			} else {
				$news = Models\PageModel::find([
					'type_id = 4',
					'order' => 'time ASC'
				]);
			}
			if (count($news)) {
				$newsForView = [];
				foreach ($news as $item) {
					$tempNews = [];
					$tempNews['name'] = $item->name;
					$tempNews['seo_name'] = $item->seo_name;
					$tempNews['link'] = $this->url->get('company/news/') . $item->seo_name;
					$tempNews['short_content'] = $item->short_content;
					$itemImages = Models\PageImageModel::find([
						'page_id = ?1',
						'bind' => [1 => $item->id],
						'order' => 'sort'
					]);
					if (count($itemImages)) {
						$imgPath = 'staticPages/images/' . $itemImages[0]->id . '__page_list.' . $itemImages[0]->extension;
						if (file_exists($imgPath)) {
							$tempNews['img'] = $this->url->getStatic($imgPath);
						} else {
							$tempNews['img'] = $this->url->getStatic('img/no_foto.png');
						}
					} else {
						$tempNews['img'] = $this->url->getStatic('img/no_foto.png');
					}
					$newsForView[] = $tempNews;
				}
			} else {
				$newsForView = null;
			}
			if ($newsForView) {
				$paginator = new \Phalcon\Paginator\Adapter\NativeArray([
					'data' => $newsForView,
					'limit' => 5,
					'page' => ($currentPage) ? $currentPage : 1
				]);
			}
			$page = $paginator->getPaginate();
			if ($page->total_pages > 1) {
				$page->links = [];
				for ($i = 1; $i <= $page->total_pages; $i++) {
					$page->links[$i - 1]['page'] = $i;
					$page->links[$i - 1]['href'] = $this->url->get('company/news/?page=') . $i;
				}
			}

			$this->view->paginate = $page;

			echo $this->view->render('news/list');
		}
	}
} 