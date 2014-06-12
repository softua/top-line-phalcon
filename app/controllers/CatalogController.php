<?php
/**
 * Created by Ruslan Koloskov
 * Date: 11.06.14
 * Time: 14:50
 */

namespace App\Controllers;
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
		$mainCategories = Models\Category::find([
			'parent_id = 0',
			'order' => 'sort'
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

				$mainCategoriesForView[$i]['path'] = '/product/' . $mainCategories[$i]->seo_name . '/';
			}

			$catImage = Models\CategoryImage::findFirst([
				'category_id = :id:',
				'bind' => ['id' => $mainCategories[$i]->id]
			]);

			$mainCategoriesForView[$i]['img'] = ($catImage) ? '/' . $catImage->pathname : null;
		}
		$this->view->categories = $this->view->sidebar_categories = $mainCategoriesForView;

		echo $this->view->render('products/catalog');
	}

	public function showAction()
	{
		$categorySeoName = trim(strip_tags($this->dispatcher->getParams()[0]));

		if (!$categorySeoName)
		{
			return $this->response->redirect('catalog');
		}

		$currentCategory = Models\Category::findFirst([
			'seo_name = :seoName:',
			'bind' => ['seoName' => $categorySeoName]
		]);

		if (!$currentCategory)
		{
			return $this->response->redirect('catalog');
		}

		$tempChildren = Models\Category::find([
			'parent_id = :id:',
			'bind' => ['id' => $currentCategory->id],
			'order' => 'sort'
		]);

		$categoriesForView = [];
		$i = 0;
		foreach ($tempChildren as $childCat)
		{
			$categoriesForView[$i]['name'] = $childCat->name;

			$areThereChildrenCats = Models\Category::findFirst([
				'parent_id = :id:',
				'bind' => ['id' => $childCat->id]
			]);

			if ($areThereChildrenCats) // Если в этой категории есть дочерние, формируем ссылку для категории
			{
				$categoriesForView[$i]['path'] = '/catalog/show/' . $childCat->seo_name . '/';

			} else { // иначе ссылка для перечня товаров

				$categoriesForView[$i]['path'] = '/product/' . $childCat->seo_name . '/';
			}

			$img = Models\CategoryImage::findFirst([
				'category_id = :id:',
				'bind' => ['id' => $childCat->id]
			]);
			$categoriesForView[$i]['img'] = ($img) ? '/' . $img->pathname : null;

			$i++;
		}

		$tempSidebarCats = Models\Category::find([
			'parent_id = :id:',
			'bind' => ['id' => $currentCategory->parent_id],
			'order' => 'sort'
		]);

		$sidebarCatsForView = [];
		$i = 0;
		foreach ($tempSidebarCats as $sidebarCat)
		{
			$sidebarCatsForView[$i]['name'] = $sidebarCat->name;

			$sidebarCatChildren = Models\Category::findFirst([
				'parent_id = :id:',
				'bind' => ['id' => $sidebarCat->id]
			]);

			if ($sidebarCatChildren) // Если в этой категории есть дочерние, формируем ссылку для категории
			{
				$sidebarCatsForView[$i]['path'] = '/catalog/show/' . $sidebarCat->seo_name . '/';

			} else { // иначе ссылка для перечня товаров

				$sidebarCatsForView[$i]['path'] = '/product/' . $sidebarCat->seo_name . '/';
			}

			if ($sidebarCat->id == $currentCategory->id)
			{
				$sidebarCatsForView[$i]['active'] = true;
			}
			$i++;
		}

		// Получаем массив категорий для крошек
		$categoriesArray = [];

		$currPosition = $currentCategory->id;
		while ($currPosition)
		{
			$tempCat = Models\Category::findFirst([
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