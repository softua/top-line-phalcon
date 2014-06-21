<?php
/**
 * Created by Ruslan Koloskov
 * Date: 18.06.14
 * Time: 21:57
 */

namespace App\Controllers;
use App\Models;

class ServiceController extends BaseFrontController
{
	public function initialize()
	{
		parent::initialize();

		$this->tag->appendTitle('Сервис');
		$this->view->active_link = 'service';
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

		$this->view->sidebar_categories = $mainCategoriesForView;

		echo $this->view->render('service');
	}
} 