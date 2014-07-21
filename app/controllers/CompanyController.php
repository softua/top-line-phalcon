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

		$this->view->sidebar_categories = Models\Category::getMainCategories();

		echo $this->view->render('company/notfound');
	}

	public function aboutUsAction()
	{
		$this->tag->appendTitle('О компании');

		$this->view->active_link = 'company';
		$this->view->sidebar_categories = Models\Category::getMainCategories();

		echo $this->view->render('company/company');
	}

	public function showAction()
	{
		$seoName = trim(strip_tags($this->dispatcher->getParams()[0]));
		if ($seoName) {
			$page = Models\CompanyPage::getPageBySeoName($seoName);
			if (!$page) {
				return $this->response->redirect('company/notfound');
			}
		} else {
			return $this->response->redirect('company/notfound');
		}

		$mainCategories = Models\Category::getMainCategories();

		$this->tag->prependTitle($page->name);

		$this->view->current_page = $page;
		$this->view->sidebar_categories = $mainCategories;

		echo $this->view->render('company/description');
	}

	public function newsAction()
	{
		$this->tag->appendTitle('Новости');

		$this->view->active_link = 'company';
		$this->view->sidebar_categories = Models\Category::getMainCategories();

		// Описание новости
		if ($this->dispatcher->getParams()[0]) {
			$seoName = $this->dispatcher->getParams()[0];
			$oneNews = Models\News::getPageBySeoName($seoName);
			if ($oneNews) {
				$oneNews->setImages();

				$this->view->news = $oneNews;
				echo $this->view->render('news/description');
			}
			else {
				return $this->dispatcher->forward([
					'controller' => 'company',
					'action' => 'notfound'
				]);
			}
		} else { // Список новостей
			$sort = $this->request->getQuery('sort', 'int');
			$currentPage = $this->request->getQuery('page', 'int');
			if (!$sort || $sort === 1) {
				/** @var Models\News[] | null $news */
				$news = Models\News::find([
					'type_id = 4',
					'order' => 'time DESC'
				]);
			} else {
				/** @var Models\News[] | null $news */
				$news = Models\News::find([
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
					$tempNews['img'] = $item->getMainImage();
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