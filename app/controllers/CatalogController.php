<?php
/**
 * Created by Ruslan Koloskov
 * Date: 11.06.14
 * Time: 14:50
 */

namespace App\Controllers;
use App;
use App\Models;

class CatalogController extends BaseFrontController
{
	public function initialize()
	{
		parent::initialize();

		$this->tag->appendTitle('Каталог');
		$this->view->active_link = 'catalog';
	}

	public function indexAction()
	{
		$this->view->categories = App\Category::getMainCategories($this->di, true);

		echo $this->view->render('products/catalog');
	}

	public function showAction()
	{
		$categorySeoName = trim(strip_tags($this->dispatcher->getParams()[0]));

		if (!$categorySeoName)
		{
			return $this->response->redirect('catalog');
		}

		$currentCategory = Models\CategoryModel::findFirst([
			'seo_name = :seoName:',
			'bind' => ['seoName' => $categorySeoName]
		]);

		if (!$currentCategory)
		{
			return $this->response->redirect('catalog');
		}

		$tempChildren = Models\CategoryModel::find([
			'parent_id = :id:',
			'bind' => ['id' => $currentCategory->id],
			'order' => 'sort, name'
		]);

		$categoriesForView = [];
		$i = 0;
		foreach ($tempChildren as $childCat)
		{
			$categoriesForView[$i]['name'] = $childCat->name;

			$areThereChildrenCats = Models\CategoryModel::findFirst([
				'parent_id = :id:',
				'bind' => ['id' => $childCat->id]
			]);

			if ($areThereChildrenCats) // Если в этой категории есть дочерние, формируем ссылку для категории
			{
				$categoriesForView[$i]['path'] = '/catalog/show/' . $childCat->seo_name . '/';

			} else { // иначе ссылка для перечня товаров

				$categoriesForView[$i]['path'] = '/products/list/' . $childCat->seo_name . '/';
			}

			$img = Models\CategoryImageModel::findFirst([
				'category_id = :id:',
				'bind' => ['id' => $childCat->id]
			]);
			if ($img && file_exists($img->pathname))
			{
				$categoriesForView[$i]['img'] = '/' . $img->pathname;
			} else {
				$categoriesForView[$i]['img'] = '/img/no_foto_110x110.png';
			}


			$i++;
		}

		// Категории для сайдбара
		$sidebarCats = Models\CategoryModel::find([
			'parent_id = 0',
			'order' => 'sort, name'
		]);
		$currentCategoryParentId = $currentCategory->id;
		$currentPosition = $currentCategory->parent_id;
		while ($currentPosition)
		{
			if ($currentCategory->parent_id)
			{
				$parentCat = Models\CategoryModel::findFirst($currentPosition);
				$currentCategoryParentId = $parentCat->id;
				$currentPosition = $parentCat->parent_id;
			} else {
				$currentPosition = 0;
			}

		}
		$sidebarCatsForView = [];
		foreach ($sidebarCats as $sidebarCat)
		{
			$tempSidebarCat['name'] = $sidebarCat->name;
			$sidebarCatChildren = Models\CategoryModel::findFirst([
				'parent_id = :id:',
				'bind' => ['id' => $sidebarCat->id]
			]);
			if ($sidebarCatChildren)
			{
				$tempSidebarCat['path'] = '/catalog/show/' . $sidebarCat->seo_name . '/';
			} else {
				$tempSidebarCat['path'] = '/products/list/' . $sidebarCat->seo_name . '/';
			}
			if ($sidebarCat->id == $currentCategoryParentId)
			{
				$tempSidebarCat['active'] = true;
			} else {
				$tempSidebarCat['active'] = false;
			}
			$sidebarCatsForView[] = $tempSidebarCat;
		}

		// Получаем массив категорий для крошек
		$categoriesArray = [];

		$currPosition = $currentCategory->id;
		while ($currPosition)
		{
			$tempCat = Models\CategoryModel::findFirst([
				'id = :id:',
				'bind' => ['id' => $currPosition]
			]);

			if ($tempCat)
			{
				$cat['name'] = $tempCat->name;
				$cat['path'] = '/catalog/show/' . $tempCat->seo_name . '/';

				array_unshift($categoriesArray, $cat);
				$currPosition = $tempCat->parent_id;

			} else {

				$currPosition = 0;
			}
		}

		$this->view->breadcrumbs = $categoriesArray;
		$this->view->categories = $categoriesForView;
		$this->view->sidebar_categories = $sidebarCatsForView;

		echo $this->view->render('products/catalog');
	}
}