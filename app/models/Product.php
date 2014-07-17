<?php
/**
 * Created by Ruslan Koloskov
 * Date: 15.05.14
 * Time: 13:32
 */

namespace App\Models;

/**
 * Class Product
 * @package App\Models
 * @property int $id
 * @property string $name
 * @property string $type
 * @property string $articul
 * @property string $model
 * @property int $country_id
 * @property string $brand
 * @property string $main_curancy
 * @property float $price_eur
 * @property float $price_usd
 * @property float $price_uah
 * @property string $price_alternative
 * @property string $short_description
 * @property string $full_description
 * @property string $seo_name
 * @property string $meta_keywords
 * @property string $meta_description
 * @property int $public
 * @property int $top
 * @property int $novelty
 * @property string[] $dbFields
 * @property string $path
 * @property string $editPath
 * @property Category[] | false $_categories
 * @property File[] | false $_files
 * @property Sale[] | false $_sales
 * @property ImageProduct[] | false $_images
 * @property ProductVideo[] | false $_videos
 */
class Product extends \Phalcon\Mvc\Model
{
	protected $_di;
	protected $_url;

	//DB properties
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
	public $novelty;

	public $dbFields = [
		'id', 'name', 'type', 'articul', 'model', 'country_id', 'brand', 'main_curancy', 'price_eur', 'price_usd', 'price_uah', 'price_alternative', 'short_description', 'full_description', 'seo_name', 'meta_keywords', 'meta_description', 'public', 'top', 'novelty'
	];
	public $path;
	public $editPath;
	private $_categories;
	private $_files;
	private $_sales;
	private $_images;
	private $_videos;

	public function getSource()
	{
		return 'products';
	}

	public function initialize()
	{
		$this->hasManyToMany('id', '\App\Models\ProductSale', 'product_id', 'page_id', '\App\Models\Sale', 'id', [
			'alias' => 'sales'
		]);
		$this->hasManyToMany('id', '\App\Models\ProductCategory', 'product_id', 'category_id', '\App\Models\Category', 'id', [
			'alias' => 'categories'
		]);
		$this->hasMany('id', '\App\Models\File', 'product_id', [
			'alias' => 'files'
		]);
		$this->hasMany('id', '\App\Models\ImageProduct', 'product_id', [
			'alias' => 'images'
		]);
		$this->hasMany('id', '\App\Models\ProductParam', 'product_id', [
			'alias' => 'params'
		]);
		$this->hasMany('id', '\App\Models\ProductVideo', 'product_id', [
			'alias' => 'videos'
		]);
		$this->hasMany('id', '\App\Models\ProductSale', 'product_id', [
			'alias' => 'productSales'
		]);

		$this->setDI();
		$this->useDynamicUpdate(true);

	}

	public function setDi($di = null)
	{
		if ($this->_di === null) $this->_di = \Phalcon\DI::getDefault();
		if ($this->_url === null) $this->_url = $this->_di->get('url');
	}

	public function dbSave($data = null)
	{
		return $this->save(null, $this->dbFields);
	}

	public function setCategories()
	{
		if ($this->_categories === null) {
			/** @var ProductCategory[] $dbcats */
			$dbcats = ProductCategory::query()
				->where('product_id = ?1')->bind([1 => $this->id])
				->execute();

			if (!count($dbcats)) {
				$this->_categories = false;
			} else {
				$this->_categories = [];
				foreach($dbcats as $dbCat) {
					$this->_categories[] = Category::getCategoryById($dbCat->category_id, false, true);
				}
			}
		}
	}

	public function setFiles()
	{
		if ($this->_files === null) {
			$files = File::getFilesByProduct($this);
			if (!$files || !count($files)) $this->_files = false;
			else $this->_files = $files;
		}
	}

	public function setImages()
	{
		if ($this->_images === null) {
			$this->_images = ImageProduct::query()
				->where('belongs = \'product\'')
				->andWhere('belongs_id = ?1', [1 => $this->id])
				->orderBy('sort')
				->execute()->filter(function(ImageProduct $item) {
					$item->setPaths();
					return $item;
				});

			if (!$this->_images || !count($this->_images)) $this->_images = false;
		}
	}

	public function setPath()
	{
		if ($this->path === null) {
			$this->path = $this->_url->get('products/show/' . $this->seo_name);
		}

		if ($this->editPath === null) {
			$this->editPath = $this->_url->get('admin/editproduct/' . $this->id . '/');
		}
	}

	public function setSales()
	{
		if ($this->_sales !== null) {
			return;
		}

		$prodsSalesIds = ProductSale::query()
			->where('product_id = ?1')->bind([1 => $this->id])
			->execute()->filter(function($dbSale) {
				return $dbSale->page_id;
			});

		if (!count($prodsSalesIds)) {
			$this->_sales = false;
			return;
		}

		$sales = Sale::query()
			->inWhere('id', $prodsSalesIds)
			->andWhere('public = 1')
			->andWhere('expiration > NOW()')
			->execute()
			->filter(function($sale) {
				$sale->setPath();
			});

		foreach ($sales as $page) {
			$sale = new Sale();
			$sale->setDi($this->_di);
			$sale->id = $page->id;
			$sale->name = $page->name;
			$sale->short_content = $page->short_content;
			$sale->full_content = $page->full_content;
			$sale->seo_name = $page->seo_name;
			$sale->type_id = $page->type_id;
			$sale->meta_keywords = $page->meta_keywords;
			$sale->meta_description = $page->meta_description;
			$sale->public = $page->public;
			$sale->path = $this->_url->get('sales/show/' . $sale->seo_name);
			$sale->time = strtotime($page->time);
			$sale->expiration = strtotime($page->expiration);
			$this->_sales[] = $sale;
		}
	}

	public function setVideos()
	{
		if ($this->_videos === null) {
			$videos = ProductVideo::getVideosByProduct($this);
			if ($videos && count($videos)) {
				$this->_videos = $videos;
			} else {
				$this->_videos = false;
			}
		}
	}

	public function getCategories()
	{
		if ($this->_categories === null) {
			$this->setCategories();
			if ($this->_categories) return $this->_categories;
			else return null;
		}
		elseif ($this->_categories === false) return null;
		else return $this->_categories;
	}

	public function getFiles()
	{
		if ($this->_files === null) {
			$this->setFiles();
			if ($this->_files) return $this->_files;
			else return null;
		}
		elseif ($this->_files === false) return null;
		else return $this->_files;
	}

	public function getMainCategory()
	{
		$this->setCategories();
		if ($this->_categories) return $this->_categories[0];
		else return null;
	}

	public function getImages()
	{
		$this->setImages();
		if ($this->_images) return $this->_images;
		else return null;
	}

	public function getMainImage()
	{
		$this->setImages();
		if ($this->_images) return $this->_images[0];
		else return null;
	}

	public function getSales()
	{
		$this->setSales();
		if ($this->_sales) return $this->_sales;
		else return null;

	}

	public function getVideos()
	{
		$this->setVideos();
		if ($this->_videos) return $this->_videos;
		else return null;
	}

	public function hasSales()
	{
		$this->setSales();
		if ($this->_sales) return true;
		else return false;

	}

	public function hasImages()
	{
		$this->setImages();
		if ($this->_images) return true;
		else return false;
	}

	public function hasVideos()
	{
		$this->setVideos();
		if ($this->_videos) return true;
		else return false;
	}

	public function hasFiles()
	{
		$this->setFiles();
		if ($this->_files) return true;
		else return false;
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public static function isUniqueSeoName($name)
	{
		if (!$name)
			return false;

		$productsCount = self::query()
			->where('seo_name = ?1')->bind([1 => $name])
			->execute()
			->count();

		if ($productsCount) return false;
		else return true;
	}

	/**
	 * @param Product $product
	 * @return string
	 */
	public static function generateSeoName($product)
	{
		$seoNameArray[] = $product->type;

		if ($product->brand)
			$seoNameArray[] = $product->brand;
		else
		{
			$country = Country::findFirst($product->country_id);
			$seoNameArray[] = $country->name;
		}

		if ($product->articul != $product->model)
			$seoNameArray[] = $product->model;

		$seoNameArray[] = $product->articul;

		$seoNameString = implode(' ', $seoNameArray);

		return \App\Translit::get_seo_keyword($seoNameString, true);
	}

	/**
	 * Получение товара по ID
	 * @param int $id
	 * @return Product | false Если товар найден, он возвращается. Иначе - false
	 */
	public static function getProductById($id)
	{
		if ($id && preg_match('/\d+/', $id)) {
			$product = self::findFirst($id);

			if (count($product))
				return $product;
			else
				return false;
		}
	}

	/**
	 * @param int | null $categoryId
	 * @return Product[] | null Возвращает товары
	 */
	public static function getProducts($categoryId = null)
	{
		if ($categoryId)
		{
			$productIds = ProductCategory::find([
				'category_id = ?1',
				'bind' => [1 => $categoryId]
			]);

			if (count($productIds))
			{
				$products = [];
				foreach ($productIds as $productId)
				{
					$tempProduct = self::findFirst($productId->product_id);

					if ($tempProduct)
						$products[] = $tempProduct;
				}

				return $products;
			}
			else
				return null;
		} else
		{
			$tempProducts = self::find();

			if (count($tempProducts))
			{
				$products = [];
				foreach ($tempProducts as $product)
				{
					$products[] = $product;
				}

				return $products;
			}
			else
				return null;
		}
	}

	/**
	 * @param $seoName
	 * @param bool $withCategories
	 * @param bool $withImages
	 * @return Product | null
	 */
	public static function getProductBySeoName($seoName, $withCategories = false, $withImages = true)
	{
		/** @var self $product */
		$product = self::query()
			->where('seo_name = ?1')->bind([1 => $seoName])
			->andWhere('public = 1')
			->execute()
			->getFirst();
		if (!$product) {
			return null;
		}

		$product->setPath();
		if ($withCategories) {
			$product->setCategories();
		}
		if ($withImages) {
			$product->setImages();
		}
		return $product;
	}

	/**
	 * @param int[] $ids
	 * @param bool $withCategories
	 * @param bool $withImages
	 * @return self[] | null
	 */
	public static function getProductsByIds($ids, $withCategories = false, $withImages = true)
	{
		/** @var self[] $products */
		$products = self::query()
			->inWhere('id', $ids)
			->andWhere('public = 1')
			->execute()->filter(function(self $product) {
				$product->setPath();
			});

		if (count($products)) return $products;
		else return null;
	}

	/**
	 * @param Category[] $categories
	 * @param bool $withCategories
	 * @param bool $withImages
	 * @param bool $withSales
	 * @param string $sort
	 * @return Category | Category[] | null
	 */
	public static function getProductsByCategories($categories, $withCategories = false, $withImages = true, $withSales = false, $sort = 'DESC')
	{
		if (!count($categories)) return null;
		elseif(count($categories) == 1) {
			$prodsCats = ProductCategory::query()
				->where('category_id = ?1')->bind([1 => $categories[0]->id])
				->execute();
		}
		else {
			$ids = [];
			foreach ($categories as $cat) {
				$ids[] = $cat->id;
			}

			$prodsCats = ProductCategory::query()
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
		/** @var self[] $dbProducts */
		$dbProducts = self::query()
			->inWhere('id', $ids)
			->andWhere('public = 1')
			->orderBy('price_uah ' . $sort . ', name')
			->execute();

		$products = [];
		foreach ($dbProducts as $dbProduct) {

			$dbProduct->setPath();
			if ($withCategories) {
				$dbProduct->setCategories();
			}
			if ($withImages) {
				$dbProduct->setImages();
			}
			if ($withSales) {
				$dbProduct->setSales();
			}
			$products[] = $dbProduct;
		}

		return $products;
	}

	/**
	 * @param bool $withCategories
	 * @param bool $withImages
	 * @return self[] | null
	 */
	public static function getNovelty($withCategories = false, $withImages = true)
	{
		/** @var self[] $prods */
		$prods = Product::query()
			->where('novelty = \'1\'')
			->andWhere('public = \'1\'')
			->orderBy('price_uah DESC')
			->execute()->filter(function(self $prod) {
				$prod->setPath();
				return $prod;
			});

		if (count($prods)) return $prods;
		else return null;
	}
}