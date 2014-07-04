<?php
/**
 * Created by Ruslan Koloskov
 * Date: 03.07.14
 * Time: 16:09
 */

namespace App;
use App\Models;

class Sale extends Page
{
	public $shortContent;
	public $fullContent;
	public $path;
	public $expiration;
	private $_products = [];

	private function _setProducts()
	{
		$prodsSales = Models\ProductSaleModel::query()
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

	public function hasProducts()
	{
		if (is_array($this->_products) && empty($this->_products)) {
			$this->_setProducts();
			if ($this->_products === false) {return false;}
			else {return true;}
		} elseif ($this->_products === false) {return false;}
		else {return true;}
	}

	public static function getSales($di, $withImages = false)
	{
		$sales = Models\PageModel::query()
			->where('type_id = 5')
			->andWhere('public = 1')
			->orderBy('time DESC, name')
			->execute();
		if (count($sales)) {
			$salesArray = [];
			foreach ($sales as $sale) {
				$newSale = new self();
				$newSale->setDi($di);
				$newSale->id = $sale->id;
				$newSale->name = $sale->name;
				$newSale->shortContent = $sale->short_content;
				$newSale->fullContent = $sale->full_content;
				$newSale->seoName = $sale->seo_name;
				$newSale->typeId = $sale->type_id;
				$newSale->metaKeywords = $sale->meta_keywords;
				$newSale->metaDescription = $sale->meta_description;
				$newSale->public = $sale->public;
				$newSale->time = $sale->time;
				$newSale->expiration = $sale->expiration;
				$newSale->path = $di->get('url')->get('sales/show/') . $newSale->seoName;
				if ($withImages) {
					$newSale->setImages();
				}
				$salesArray[] = $newSale;
			}
			return $salesArray;
		} else {
			return null;
		}
	}

	public static function getSaleBySeoName($di, $seoName, $withImages = false)
	{
		$page = parent::getPageBySeoName($seoName);

		if ($page) {
			$sale = new self();
			$sale->setDi($di);
			$sale->id = $page->id;
			$sale->name = $page->name;
			$sale->seoName = $page->seo_name;
			$sale->shortContent = $page->short_content;
			$sale->fullContent = $page->full_content;
			$sale->path = $di->get('url')->get('sales/show/' . $page->seo_name);
			$sale->typeId = 5;
			$sale->metaKeywords = $page->meta_keywords;
			$sale->metaDescription = $page->meta_description;
			$sale->public = $page->public;
			$sale->time = strtotime($page->time);
			if ($page->expiration) {
				$sale->expiration = strtotime($page->expiration);
			}
			if ($withImages) {
				$sale->setImages();
			}
			return $sale;
		} else {
			return null;
		}

	}
}