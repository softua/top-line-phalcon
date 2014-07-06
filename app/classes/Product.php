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
	public $path;
	public $editPath;
	public $novelty;

	private $categories = [];
	private $mainCategory;
	private $_files = [];
	private $_sales = [];
	private $_images = [];
	private $_videos = [];


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

	private function _setFiles()
	{
		if (is_array($this->_files) && empty($this->_files)) {
			$files = File::getFilesByProduct($this->_di, $this);
			if (!$files || !count($files)) $this->_files = false;
			else $this->_files = $files;
		}
	}

	private function _setPath()
	{
		$this->path = $this->_url->get('products/show/' . $this->seo_name);
		$this->editPath = $this->_url->get('admin/editproduct/' . $this->id . '/');
	}

	private function _setSales()
	{
		$prodsSales = Models\ProductSaleModel::query()
			->where('product_id = ?1')->bind([1 => $this->id])
			->execute();

		if (!count($prodsSales)) {
			$this->_sales = false;
			return;
		}
		$pagesIds = [];
		foreach ($prodsSales as $prodSale) {
			$pagesIds[] = $prodSale->page_id;
		}

		$sales = Models\PageModel::query()
			->inWhere('id', $pagesIds)
			->andWhere('public = 1')
			->andWhere('expiration > NOW()')
			->execute();

		foreach ($sales as $page) {
			$sale = new Sale();
			$sale->setDi($this->_di);
			$sale->id = $page->id;
			$sale->name = $page->name;
			$sale->shortContent = $page->short_content;
			$sale->fullContent = $page->full_content;
			$sale->seoName = $page->seo_name;
			$sale->typeId = $page->type_id;
			$sale->metaKeywords = $page->meta_keywords;
			$sale->metaDescription = $page->meta_description;
			$sale->public = $page->public;
			$sale->path = $this->_url->get('sales/show/' . $sale->seoName);
			$sale->time = strtotime($page->time);
			$sale->expiration = strtotime($page->expiration);
			$this->_sales[] = $sale;
		}
	}

	private function _setImages()
	{
		$imgs = Models\ProductImageModel::query()
			->where('product_id = ?1')->bind([1 => $this->id])
			->orderBy('sort')
			->execute();

		if (!count($imgs)) { $this->_images = false; return; }

		foreach ($imgs as $img) {
			$newImage = new ProductImage();
			$newImage->setDi($this->_di);
			$newImage->id = $img->id;
			$newImage->extension = $img->extension;
			$newImage->productId = $img->product_id;
			$newImage->sort = $img->sort;
			$newImage->setPath();
			$this->_images[] = $newImage;
		}
	}

	private function _setVideos()
	{
		$videos = Video::getVideosByProduct($this->_di, $this);
		if ($videos && count($videos)) {
			$this->_videos = $videos;
		} else {
			$this->_videos = false;
		}
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
		if (!$this->path) {
			$this->_setPath();
		}
	}

	public function setSales()
	{
		if (is_array($this->_sales) && empty($this->_sales)) {
			$this->_setSales();
		}
	}

	public function setFilesIfNone()
	{
		if (is_array($this->_files) && empty($this->_files)) $this->setFiles();
	}

	public function setImages()
	{
		if (is_array($this->_images) && empty($this->_images)) {
			$this->_setImages();
		}
	}

	public function setVideos()
	{
		if (is_array($this->_videos) && empty($this->_videos)) {
			$this->_setVideos();
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
		if (is_array($this->_files) && empty($this->_files)) {
			$this->_setFiles();
			if ($this->_files === false) return null;
			else return $this->_files;
		}
		elseif ($this->_files === false) return null;
		else return $this->_files;
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

	public function getImages()
	{
		if ($this->hasImages()) return $this->_images;
		else return null;
	}

	public function getMainImageForList()
	{
		if ($this->hasImages()) {
			return $this->_images[0]->productListPath;
		} else return null;
	}

	public function getMainImageForDescription()
	{
		if ($this->hasImages()) {
			return $this->_images[0]->productDescriptionPath;
		} else return null;
	}

	public function getSales()
	{
		if ($this->hasSales()) return $this->_sales;
		else return null;
	}

	public function getVideos()
	{
		if ($this->hasVideos()) return $this->_videos;
		else return null;
	}

	public function hasSales()
	{
		if (is_array($this->_sales) && empty($this->_sales)) {
			$this->_setSales();
			if ($this->_sales === false) return false;
			else {return true;}
		} elseif ($this->_sales === false) return false;
		else return true;
	}

	public function hasImages()
	{
		if (is_array($this->_images) && empty($this->_images)) {
			$this->_setImages();
			if ($this->_images === false) return false;
			else return true;
		} elseif ($this->_images === false) return false;
		else return true;
	}

	public function hasVideos()
	{
		if (is_array($this->_videos) && empty($this->_videos)) $this->_setVideos();

		if ($this->_videos === false) return false;
		else return true;

	}

	public function hasFiles()
	{
		if (is_array($this->_files) && empty($this->_files)) {
			$this->_setFiles();

			if ($this->_files === false) return false;
			else return true;
		}
		elseif ($this->_files === false) return false;
		else return true;
	}

	public static function getProductBySeoName($di, $seoName, $withCategories = false, $withImages = true)
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
		$product->novelty = $dbProduct->novelty;
		$product->setPath();
		if ($withCategories) {
			$product->setCategoriesIfNone();
		}
		if ($withImages) {
			$product->setImages();
		}
		return $product;
	}

	public static function getProductById($di, $id, $withCategories = false, $withImages = true)
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
		$product->novelty = $dbProduct->novelty;
		$product->setPath();
		if ($withCategories) {
			$product->setCategoriesIfNone();
		}
		if ($withImages) {
			$product->setImages();
		}

		return $product;
	}

	public static function getProductsByIds($di, $ids, $withCategories = false, $withImages = true)
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
			$product->novelty = $dbProduct->novelty;
			$product->setPath();
			if ($withCategories) {
				$product->setCategoriesIfNone();
			}
			if ($withImages) {
				$product->setImages();
			}
			$products[] = $product;
		}

		return $products;
	}

	public static function getProductsByCategories($di, $categories, $withCategories = false, $withImages = true, $withSales = false, $sort = 'DESC')
	{
		if (!count($categories)) return null;

		if(count($categories) == 1) {
			$prodsCats = Models\ProductCategoryModel::query()
				->where('category_id = ?1')->bind([1 => $categories[0]->id])
				->execute();
		} else {
			$ids = [];
			foreach ($categories as $cat) {
				$ids[] = $cat->id;
			}

			$prodsCats = Models\ProductModel::query()
				->inWhere('category_id', $ids)
				->execute();
		}
		if (!count($prodsCats)) return null;

		$ids = [];
		foreach ($prodsCats as $item) {
			if (!in_array($item->product_id, $ids)) {
				$ids[] = $item->product_id;
			}
		}

		if (!$sort) $sort = 'DESC';
		$dbProducts = Models\ProductModel::query()
			->inWhere('id', $ids)
			->andWhere('public = 1')
			->orderBy('price_uah ' . $sort . ', name')
			->execute();

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
			$product->novelty = $dbProduct->novelty;
			$product->setPath();
			if ($withCategories) {
				$product->setCategoriesIfNone();
			}
			if ($withImages) {
				$product->setImages();
			}
			if ($withSales) {
				$product->setSales();
			}
			$products[] = $product;
		}

		return $products;
	}

	public static function getNovelty($di, $withCategories = false, $withImages = true)
	{
		$prods = Models\ProductModel::query()
			->where('novelty = 1')
			->orderBy('price_uah DESC')
			->execute();
		if (!$prods || !count($prods)) return null;

		$products = [];
		foreach ($prods as $dbProduct) {
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
			$product->novelty = $dbProduct->novelty;
			$product->setPath();
			if ($withCategories) {
				$product->setCategoriesIfNone();
			}
			if ($withImages) {
				$product->setImages();
			}
			$products[] = $product;
		}

		return $products;
	}
}