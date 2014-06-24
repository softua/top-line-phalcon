<?php
/**
 * Created by Ruslan Koloskov
 * Date: 18.06.14
 * Time: 21:57
 */

namespace App\Controllers;
use App\Models;

class ProjectsController extends BaseFrontController
{
	public function initialize()
	{
		parent::initialize();

		$this->tag->appendTitle('Проекты');
		$this->view->active_link = 'projects';
	}

	public function indexAction()
	{
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
			if ($areThereChildrenCats)
			{
				$mainCategoriesForView[$i]['path'] = '/catalog/show/' . $mainCategories[$i]->seo_name . '/';

			} else {

				$mainCategoriesForView[$i]['path'] = '/products/list/' . $mainCategories[$i]->seo_name . '/';
			}

			$catImage = Models\CategoryImage::findFirst([
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

		// Список проектов
		$projects = Models\Page::find([
			'type_id = 2 AND public = 1'
		]);
		$projectsForView = [];
		if (count($projects)) {
			foreach ($projects as $project) {
				$tempProject = [];
				$tempProject['name'] = $project->name;
				$tempProject['href'] = '/projects/show/' . $project->seo_name;
				$tempProject['short_content'] = $project->short_content;
				$images = Models\PageImage::find([
					'page_id = ?1',
					'bind' => [1 => $project->id],
					'order' => 'sort'
				]);
				if (count($images)) {
					$imgPath = 'staticPages/images/' . $images[0]->id . '__page_list.' . $images[0]->extension;
					if (file_exists($imgPath)) {
						$tempProject['img'] = '/' . $imgPath;
					} else {
						$tempProject['img'] = '/img/no_foto.png';
					}
				} else {
					$tempProject['img'] = '/img/no_foto.png';
				}
				$projectsForView[] = $tempProject;
			}
		}

		$this->view->projects = $projectsForView;
		$this->view->sidebar_categories = $mainCategoriesForView;

		echo $this->view->render('projects/list');
	}

	public function showAction()
	{
		$seoName = trim(strip_tags($this->dispatcher->getParams()[0]));
		if (!$seoName) {
			return $this->response->redirect('projects/notfound');
		} else {
			$page = Models\Page::findFirst([
				'seo_name = ?1 AND public = 1',
				'bind' => [1 => $seoName]
			]);
			if (!$page) {
				return $this->response->redirect('projects/notfound');
			}
		}
		$projectForView = [];
		$projectForView['name'] = $page->name;
		$images = Models\PageImage::find([
			'page_id = ?1',
			'bind' => [1 => $page->id],
			'order' => 'sort'
		]);
		if (count($images)) {
			$imgPath = 'staticPages/images/' . $images[0]->id . '__page_description.' . $images[0]->extension;
			if (file_exists($imgPath)) {
				$projectForView['img'] = '/' . $imgPath;
			} else {
				$projectForView['img'] = '/img/no_foto.png';
			}
		} else {
			$projectForView['img'] = '/img/no_foto.png';
		}
		$projectForView['full_content'] = $page->full_content;

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
			if ($areThereChildrenCats)
			{
				$mainCategoriesForView[$i]['path'] = '/catalog/show/' . $mainCategories[$i]->seo_name . '/';

			} else {

				$mainCategoriesForView[$i]['path'] = '/products/list/' . $mainCategories[$i]->seo_name . '/';
			}
		}

		$this->view->sidebar_categories = $mainCategoriesForView;
		$this->view->project = $projectForView;

		echo $this->view->render('projects/description');
	}

	public function notFoundAction()
	{
		$this->response->setStatusCode(404, 'Not Found');

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
			if ($areThereChildrenCats)
			{
				$mainCategoriesForView[$i]['path'] = '/catalog/show/' . $mainCategories[$i]->seo_name . '/';

			} else {

				$mainCategoriesForView[$i]['path'] = '/products/list/' . $mainCategories[$i]->seo_name . '/';
			}

			$catImage = Models\CategoryImage::findFirst([
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

		$this->view->sidebar_categories = $mainCategoriesForView;

		echo $this->view->render('projects/notfound');
	}
}