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
	public $categories = [];
	public $mainCategory;

	public function setDi($di)
	{
		$this->_di = $di;
		$this->_url = $this->_di->get('url');
	}

	public static function getProductBySeoName($di, $seoName, $categories = false)
	{
		$dbProduct = Models\ProductModel::query()
			->where('seo_name = ?1')
			->andWhere('public = 1')
			->bind([1 => $seoName])
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
		if ($categories) {
			$dbcats = Models\ProductCategoryModel::query()
				->where('product_id = ?1')->bind([1 => $product->id])
				->execute();
			if (!count($dbcats)) {
				$product->categories = null;
			} else {
				foreach ($dbcats as $dbRow) {
					$product->categories[] = Category::getCategoryById($di, $dbRow->category_id, false, true);
				}
				$product->mainCategory = $product->categories[0];
			}
		}
		return $product;
	}
} 