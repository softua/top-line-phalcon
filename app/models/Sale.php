<?php
/**
 * Created by Ruslan Koloskov
 * Date: 03.07.14
 * Time: 16:09
 */

namespace App\Models;

class Sale extends Page
{
	public $path;

	private $_products = [];

	private function _setProducts()
	{
		/** @var ProductSale[] $prodsSales */
		$prodsSales = ProductSale::query()
			->where('page_id = ?1')->bind([1 => $this->id])
			->execute();

		if (!count($prodsSales)) {
			$this->_products = false;
			return;
		}

		$prodsIds = [];
		foreach ($prodsSales as $prodSale) {
			$prodsIds[] = $prodSale->product_id;
		}

		if (count($prodsIds) == 1) {
			$products = Product::getProductById($this->_di, $prodsIds[0]);
		} else {
			$products = Product::getProductsByIds($this->_di, $prodsIds);
		}

		if ($products === null) {
			$this->_products = false;
		} elseif (is_array($products)) {
			$this->_products = $products;
		} else {
			$this->_products[] = $products;
		}
	}

	public function setImages()
	{
		if ($this->_images === null) {
			$this->_images = ImageSale::query()
				->where('belongs = \'sale\'')
				->andWhere('belongs_id = ?1', [1 => $this->id])
				->orderBy('sort')
				->execute()->filter(function(ImageSale $item) {
					$item->setPaths();
					return $item;
				});

			if (!$this->_images || !count($this->_images)) {
				$this->_images = false;
				$this->_mainImage = false;
			}
			else {
				$this->_mainImage = $this->_images[0];
			}
		}
	}

	public function setProducts()
	{
		if (is_array($this->_products) && empty($this->_products)) {
			$this->_setProducts();
		}
	}

	public function getProducts()
	{
		if (is_array($this->_products) && empty($this->_products)) {
			$this->_setProducts();
			if (!$this->_products) {
				return null;
			} else {
				return $this->_products;
			}
		} elseif ($this->_products === false) {
			return null;
		} else {
			return $this->_products;
		}
	}

	public function setPath()
	{
		if (!$this->path) {
			$this->path = $this->_url->get('sales/show/' . $this->seo_name);
		}
	}

	/**
	 * @return ImageSale[] | null
	 */
	public function getImages()
	{
		if ($this->_images === null) {
			$this->setImages();
			$this->getImages();
		}
		elseif ($this->_images === false) return null;
		else return $this->_images;
	}

	/**
	 * @return ImageSale | null
	 */
	public function getMainImage()
	{
		if ($this->_mainImage === null) {
			$this->setImages();
			$this->getMainImage();
		}
		elseif ($this->_mainImage === false) return null;
		else return $this->_mainImage;
	}

	public function hasProducts()
	{
		if (is_array($this->_products) && empty($this->_products)) {
			$this->_setProducts();
			if ($this->_products === false) {return false;}
			else {return true;}
		} elseif ($this->_products === false) {return false;}
		else {return true;}
	}

	public static function getSales($withImages = false)
	{
		/** @var Sale[] $sales */
		$sales = self::query()
			->where('type_id = 5')
			->andWhere('public = 1')
			->orderBy('time DESC, name')
			->execute()->filter(function(Sale $item) {
				$item->time = strtotime($item->time);
				$item->setPath();
				return $item;
			});
		if (count($sales) && $withImages) {
			foreach ($sales as $sale) {
				$sale->setImages();
			}
		}
		if (count($sales)) return $sales;
		else return null;
	}

	public static function getSaleBySeoName($seoName, $withImages = false)
	{//TODO: Реализовать $withImages
		/** @var Page $page */
		$page = parent::getPageBySeoName($seoName);

		if ($page) {
			$sale = new self();
			$sale->id = $page->id;
			$sale->name = $page->name;
			$sale->seo_name = $page->seo_name;
			$sale->short_content = $page->short_content;
			$sale->full_content = $page->full_content;
			$sale->setPath();
			$sale->type_id = 5;
			$sale->meta_keywords = $page->meta_keywords;
			$sale->meta_description = $page->meta_description;
			$sale->public = $page->public;
			$sale->time = $page->time;
			if ($page->expiration) {
				$sale->expiration = strtotime($page->expiration);
			}
			return $sale;
		}
		else return null;
	}
}