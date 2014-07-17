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
		$this->view->categories = Models\Category::getMainCategories(true);
		$this->view->sidebar_categories = Models\Category::getMainCategories();

		echo $this->view->render('products/catalog');
	}

	public function showAction()
	{
		$categorySeoName = trim(strip_tags($this->dispatcher->getParams()[0]));

		if (!$categorySeoName) {
			return $this->response->redirect('catalog');
		}
		$category = Models\Category::getCategoryBySeoName($categorySeoName, true);

		if ($category) {
			$this->view->breadcrumbs = $category->getParentsCategories();
		}


		$this->view->categories = Models\Category::getChildrenCategoriesByParentSeoName($categorySeoName);
		$this->view->sidebar_categories = Models\Category::getMainCategories(false, [$category->seo_name]);

		echo $this->view->render('products/catalog');
	}
}