<?php
/**
 * Created by Ruslan Koloskov
 * Date: 13.06.14
 * Time: 9:48
 */

namespace App\Controllers;
use App\Models;

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
	$sidebarCategories = Models\Category::find([
		'parent_id = 0',
		'order' => 'sort'
	]);

	if (count($sidebarCategories) > 0)
	{
		$sidebarCategoriesForView = [];
		foreach ($sidebarCategories as $category)
		{
			$tempCategory = [];
			$categoryChildren = Models\Category::findFirst([
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
	echo $this->view->render('products/notfound');
}

	public function listAction()
	{
		$catSeoName = $this->dispatcher->getParams()[0];

		$currentCategory = Models\Category::findFirst([
			'seo_name = :seoName:',
			'bind' => ['seoName' => $catSeoName]
		]);

		if (!$currentCategory)
		{
			return $this->response->redirect('catalog');
		}

		// Формируем хлебные крошки
		$categoriesArray = [];
		$currPosition = $currentCategory->id;
		while ($currPosition)
		{
			$tempCat = Models\Category::findFirst($currPosition);

			if (!$tempCat)
			{
				$currPosition = 0;
			}

			$cat['name'] = $tempCat->name;

			$childrenCats = Models\Category::findFirst([
				'parent_id = :id:',
				'bind' => ['id' => $tempCat->id]
			]);

			if ($childrenCats)
			{
				$cat['path'] = '/catalog/show/' . $tempCat->seo_name . '/';

			} else {

				$cat['path'] = '/products/list/' . $tempCat->seo_name . '/';
			}

			array_unshift($categoriesArray, $cat);
			$currPosition = $tempCat->parent_id;
		}

		// Категории для сайдбара
		$sidebarCats = Models\Category::find([
			'parent_id = 0',
			'order' => 'sort,name'
		]);
		$currentCategoryParentId = $currentCategory->id;
		$currentPosition = $currentCategory->parent_id;
		while ($currentPosition)
		{
			if ($currentCategory->parent_id)
			{
				$parentCat = Models\Category::findFirst($currentPosition);
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
			$sidebarCatChildren = Models\Category::findFirst([
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

		// Список товаров
		$prodCats = Models\ProductCategory::find([
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

			$productList = Models\Product::find([
				$queryString,
				'order' => 'price_uah DESC'
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

				$prodImages = Models\ProductImage::find([
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

		$this->view->breadcrumbs = $categoriesArray;
		$this->view->name = $categoriesArray[count($categoriesArray) - 1]['name'];
		$this->view->sidebar_categories = $sidebarCatsForView;
		$this->view->products = $productsForView;

		echo $this->view->render('products/list');
	}

	public function showAction()
	{
		$productSeoName = trim(strip_tags($this->dispatcher->getParams()[0]));
		if (!$productSeoName)
		{
			return $this->dispatcher->forward([
				'controller' => 'products',
				'action' => 'notfound'
			]);
		}
		$currentProduct = Models\Product::findFirst([
			'seo_name = :productSeoName: AND public = 1',
			'bind' => ['productSeoName' => $productSeoName]
		]);
		if (!$currentProduct)
		{
			return $this->dispatcher->forward([
				'controller' => 'products',
				'action' => 'notfound'
			]);
		}

		//Главная категория

		$prodCatMain = Models\ProductCategory::findFirst([
			'product_id = :productId:',
			'bind' => ['productId' => $currentProduct->id]
		]);
		$currentCategory = Models\Category::findFirst($prodCatMain->category_id);

		// Формируем хлебные крошки

		$categoriesArray = [];
		$currPosition = $currentCategory->id;
		while ($currPosition)
		{
			$tempCat = Models\Category::findFirst($currPosition);

			if (!$tempCat)
			{
				$currPosition = 0;
			}

			$cat['name'] = $tempCat->name;

			$childrenCats = Models\Category::findFirst([
				'parent_id = :id:',
				'bind' => ['id' => $tempCat->id]
			]);

			if ($childrenCats)
			{
				$cat['path'] = '/catalog/show/' . $tempCat->seo_name . '/';

			} else {

				$cat['path'] = '/products/list/' . $tempCat->seo_name . '/';
			}

			array_unshift($categoriesArray, $cat);
			$currPosition = $tempCat->parent_id;
		}
		$categoriesArray[] = ['name' => $currentProduct->name];

		//Формируем данные товара для представления

		$currentProductForView = [];
		$currentProductForView['name'] = $currentProduct->name;
		$currentProductForView['articul'] = $currentProduct->articul;
		$currentProductForView['main_curancy'] = $currentProduct->main_curancy;
		$priceName = 'price_' . $currentProduct->main_curancy;
		if ($currentProduct->$priceName == 0)
		{
			$currentProductForView['alt_price'] = $currentProduct->price_alternative;
		} else {
			$currentProductForView['price'] = number_format($currentProduct->$priceName, 2, '.', ' ');
		}
		$currentProductForView['country'] = Models\Country::findFirst([$currentProduct->country_id])->name;
		if ($currentProduct->brand)
		{
			$currentProductForView['brand'] = $currentProduct->brand;
		}
		if ($currentProduct->short_description)
		{
			$currentProductForView['short_desc'] = $currentProduct->short_description;
		}
		$prodParams = Models\ProductParam::getParamsByProductId($currentProduct->id);
		if ($prodParams)
		{
			foreach ($prodParams as $param)
			{
				$currentProductForView['parameters'][$param->name] =  $param->value;
			}
		}
		if ($currentProduct->full_description)
		{
			$currentProductForView['full_desc'] = $currentProduct->full_description;
		}
		$prodImages = Models\ProductImage::find([
			'product_id = :prodId:',
			'bind' => ['prodId' => $currentProduct->id],
			'order' => 'sort'
		]);
		if (count($prodImages) > 0)
		{
			foreach ($prodImages as $image)
			{
				if (file_exists('products/' . $image->product_id . '/images/' . $image->id . '__product_description.' . $image->extension))
				{
					$tempImage['desc'] = '/products/' . $image->product_id . '/images/' . $image->id . '__product_description.' . $image->extension;
				} else {
					$tempImage['desc'] = '/img/no_foto.png';
				}
				if (file_exists('products/' . $image->product_id . '/images/' . $image->id . '__product_thumb.' . $image->extension))
				{
					$tempImage['thumb'] = '/products/' . $image->product_id . '/images/' . $image->id . '__product_thumb.' . $image->extension;
				} else {
					$tempImage['thumb'] = '/img/no_foto.png';
				}
				$currentProductForView['images'][] = $tempImage;
			}
		}
		// Видео
		$prodVideos = Models\ProductVideo::find([
			'product_id = ?1',
			'bind' => [1 => $currentProduct->id],
			'order' => 'sort'
		]);
		if (count($prodVideos))
		{
			foreach ($prodVideos as $video)
			{
				$tempVideo = [];
				$tempVideo['id'] = $video->id;
				$tempVideo['name'] = ($video->name) ? $video->name : $video->href;
				$tempVideo['href'] = $video->href;
				$tempVideo['sort'] = $video->sort;
				$tempVideo['product_id'] = $video->product_id;
				$currentProductForView['video'][] = $tempVideo;
			}
		}
		// Файлы
		$prodFiles = Models\ProductFile::find([
			'product_id = ?1',
			'bind' => [1 => $currentProduct->id]
		]);
		if (count($prodFiles))
		{
			foreach ($prodFiles as $file)
			{
				$tempFile = [];
				$tempFile['name'] = $file->name;
				$tempFile['path'] = '/' . $file->pathname;
				$currentProductForView['files'][] = $tempFile;
			}
		}

		// Формируем категории для сайдбара

		$sidebarCatsForView = [];
		$prodCats = Models\ProductCategory::find([
			'product_id = :prodId:',
			'bind' => ['prodId' => $currentProduct->id]
		]);
		$activeCats = [];
		foreach ($prodCats as $prodCat)
		{
			$activeCats[] = Models\Category::getRootCategoryByChildId($prodCat->category_id);
		}
		$mainCats = Models\Category::getMainCategories();
		foreach ($mainCats as $mainCat)
		{
			$tempSidebarCat['name'] = $mainCat->name;
			$mainCatChildren = Models\Category::findFirst([
				'parent_id = :id:',
				'bind' => ['id' => $mainCat->id]
			]);
			if ($mainCatChildren)
			{
				$tempSidebarCat['path'] = '/catalog/show/' . $mainCat->seo_name . '/';
			} else {
				$tempSidebarCat['path'] = '/products/list/' . $mainCat->seo_name . '/';
			}
			$tempSidebarCat['active'] = false;
			foreach ($activeCats as $activeCat)
			{
				if ($activeCat->id == $mainCat->id)
				{
					$tempSidebarCat['active'] = true;
					break;
				}
			}
			$sidebarCatsForView[] = $tempSidebarCat;
		}

		//

		$this->view->breadcrumbs = $categoriesArray;
		$this->view->product = $currentProductForView;
		$this->view->sidebar_categories = $sidebarCatsForView;

		echo $this->view->render('products/product');
	}
}