<?php
/**
 * Created by Ruslan Koloskov
 * Date: 02.07.14
 * Time: 1:31
 */

namespace App;


class Product
{
	protected $_di;
	protected $_url;

	public $id;
	public $name;
	public $type;
	public $articul;
	public $model;
	public $country_id;
	public $brand;
	public $main_curancy;
	public $price_eur;
	public $price_usd;
	public $price_uah;
	public $price_alternative;
	public $short_description;
	public $full_description;
	public $seo_name;
	public $meta_keywords;
	public $meta_description;
	public $public;
	public $top;
	public $_path;
	public $_editPath;
	private $categories = [];
	private $mainCategory;
	private $files = [];
	private $_sales = [];

	private function setCategories()
	{
		$dbcats = Models\ProductCategoryModel::query()
			->where('product_id = ?1')->bind([1 => $this->id])
			->execute();
		if (!count($dbcats)) {
			$this->categories = false;
			$this->mainCategory = false;
		} else {
			foreach ($dbcats as $productCategory) {
				$this->categories[] = Category::getCategoryById($this->_di, $productCategory->category_id, false, true);
			}
			$this->mainCategory = $this->categories[0];
		}
	}

	private function setFiles()
	{
		//TODO: Реализовать получение файлов из БД.
	}

	private function _setPath()
	{
		$this->_path = $this->_url->get('products/show/' . $this->seo_name);
		$this->_editPath = $this->_url->get('admin/editproduct/' . $this->id . '/');
	}

	private function _setSales()
	{
		$prodsSales = Models\ProductSaleModel::query()
			->where('product_id = ?1')->bind([1 => $this->id])
			->execute();

		if (!count($prodsSales)) {$this->_sales = false; return;}
		$pagesIds = [];
		foreach ($prodsSales as $prodSale) {
			$pagesIds[] = $prodSale->page_id;
		}

		$sales = Models\PageModel::query()
			->inWhere('id', $pagesIds)
			->andWhere('public = 1')
			->andWhere('expiration > NOW()')
			->execute();
	}

	public function setDi($di)
	{
		$this->_di = $di;
		$this->_url = $this->_di->get('url');
	}

	public function setCategoriesIfNone()
	{
		if (is_array($this->categories) && empty($this->categories)) {
			$this->setCategories();
		}
	}

	public function setPath()
	{
		if (!$this->_path) {
			$this->_setPath();
		}
	}

	public function setFilesIfNone()
	{
		if (is_array($this->files) && empty($this->files)) {
			$this->setFiles();
		}
	}

	public function getCategories()
	{
		if (is_array($this->categories) && empty($this->categories)) {
			$this->setCategories();
			if ($this->categories === false) {
				return null;
			} else {
				return $this->categories;
			}
		} elseif ($this->categories === false) {
			return null;
		} else {
			return $this->categories;
		}
	}

	public function getFiles()
	{
		if (is_array($this->files) && empty($this->files)) {
			$this->setFiles();
		} elseif ($this->files === false) {
			return null;
		} else {
			return $this->files;
		}
	}

	public function getMainCategory()
	{
		if($this->mainCategory === null) {
			$this->setCategories();
			if ($this->mainCategory === false) {
				return null;
			} else {
				return $this->mainCategory;
			}
		} elseif ($this->mainCategory === false) {
			return null;
		} else {
			return $this->mainCategory;
		}
	}

	public static function getProductBySeoName($di, $seoName, $withCategories = false)
	{
		$dbProduct = Models\ProductModel::query()
			->where('seo_name = ?1')->bind([1 => $seoName])
			->andWhere('public = 1')
			->execute()
			->getFirst();
		if (!$dbProduct) {
			return null;
		}
		$product = new self();
		$product->setDi($di);
		$product->id = $dbProduct->id;
		$product->name = $dbProduct->name;
		$product->type = $dbProduct->type;
		$product->articul = $dbProduct->articul;
		$product->model = $dbProduct->model;
		$product->country_id = $dbProduct->country_id;
		$product->brand = $dbProduct->brand;
		$product->main_curancy = $dbProduct->main_curancy;
		$product->price_eur = $dbProduct->price_eur;
		$product->price_usd = $dbProduct->price_usd;
		$product->price_uah = $dbProduct->price_uah;
		$product->price_alternative = $dbProduct->price_alternative;
		$product->short_description = $dbProduct->short_description;
		$product->full_description = $dbProduct->full_description;
		$product->seo_name = $dbProduct->seo_name;
		$product->meta_keywords = $dbProduct->meta_keywords;
		$product->meta_description = $dbProduct->meta_description;
		$product->public = $dbProduct->public;
		$product->top = $dbProduct->top;
		$product->setPath();
		if ($withCategories) {
			$product->setCategoriesIfNone();
		}
		return $product;
	}

	public static function getProductById($di, $id, $withCategories = false)
	{
		if (!$id || !preg_match('/\d+/', $id)) {
			return null;
		}

		$dbProduct = Models\ProductModel::query()
			->where('id = ?1')->bind([1 => $id])
			->andWhere('public = 1')
			->execute()
			->getFirst();

		if (!$dbProduct) {
			return null;
		}

		$product = new self();
		$product->setDi($di);
		$product->id = $dbProduct->id;
		$product->name = $dbProduct->name;
		$product->type = $dbProduct->type;
		$product->articul = $dbProduct->articul;
		$product->model = $dbProduct->model;
		$product->country_id = $dbProduct->country_id;
		$product->brand = $dbProduct->brand;
		$product->main_curancy = $dbProduct->main_curancy;
		$product->price_eur = $dbProduct->price_eur;
		$product->price_usd = $dbProduct->price_usd;
		$product->price_uah = $dbProduct->price_uah;
		$product->price_alternative = $dbProduct->price_alternative;
		$product->short_description = $dbProduct->short_description;
		$product->full_description = $dbProduct->full_description;
		$product->seo_name = $dbProduct->seo_name;
		$product->meta_keywords = $dbProduct->meta_keywords;
		$product->meta_description = $dbProduct->meta_description;
		$product->public = $dbProduct->public;
		$product->top = $dbProduct->top;
		$product->setPath();
		if ($withCategories) {
			$product->setCategoriesIfNone();
		}

		return $product;
	}

	public static function getProductsByIds($di, $ids, $withCategories = false)
	{
		$dbProducts = Models\ProductModel::query()
			->inWhere('id', $ids)
			->andWhere('public = 1')
			->execute();

		if (!count($dbProducts)) {
			return null;
		}

		$products = [];
		foreach ($dbProducts as $dbProduct) {
			$product = new self();
			$product->setDi($di);
			$product->id = $dbProduct->id;
			$product->name = $dbProduct->name;
			$product->type = $dbProduct->type;
			$product->articul = $dbProduct->articul;
			$product->model = $dbProduct->model;
			$product->country_id = $dbProduct->country_id;
			$product->brand = $dbProduct->brand;
			$product->main_curancy = $dbProduct->main_curancy;
			$product->price_eur = $dbProduct->price_eur;
			$product->price_usd = $dbProduct->price_usd;
			$product->price_uah = $dbProduct->price_uah;
			$product->price_alternative = $dbProduct->price_alternative;
			$product->short_description = $dbProduct->short_description;
			$product->full_description = $dbProduct->full_description;
			$product->seo_name = $dbProduct->seo_name;
			$product->meta_keywords = $dbProduct->meta_keywords;
			$product->meta_description = $dbProduct->meta_description;
			$product->public = $dbProduct->public;
			$product->top = $dbProduct->top;
			$product->setPath();
			if ($withCategories) {
				$product->setCategoriesIfNone();
			}
			$products[] = $product;
		}

		return $products;
	}
} 