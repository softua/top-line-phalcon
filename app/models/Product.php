<?php
/**
 * Created by Ruslan Koloskov
 * Date: 15.05.14
 * Time: 13:32
 */

namespace App\Models;

class Product extends \Phalcon\Mvc\Model
{
	public function getSource()
	{
		return 'products';
	}

	public function initialize()
	{
		$this->hasManyToMany('id', '\App\Models\ProductSale', 'product_id', 'page_id', '\App\Models\Page', 'id', [
			'alias' => 'sales'
		]);
		$this->hasManyToMany('id', '\App\Models\ProductCategory', 'product_id', 'category_id', '\App\Models\Category', 'id', [
			'alias' => 'categories'
		]);
		$this->hasMany('id', '\App\Models\ProductFile', 'product_id', [
			'alias' => 'files'
		]);
		$this->hasMany('id', '\App\Models\ProductImage', 'product_id', [
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

	}

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

	/***
	 * Проверка является ли Seo-название уникальным
	 * @param string $name seo-название, которое проверяется
	 * @return bool Возвращает true, если имя уникально. Иначе false
	 */
	public static function isUniqueSeoName($name)
	{
		if (!$name)
			return false;;

		$products = Product::find([
			'seo_name = :name:',
			'bind' => ['name' => $name]
		]);

		if ($products->count() > 1)
			return false;
		else
			return true;
	}

	/***
	 * Формирование SEO-названия для товара
	 * @param Product $product объект, из свойств которого формируется срока
	 * @return string Возвращает SEO-название
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
	 * @param string $id
	 * @return bool|Product Если товар найден, он возвращается. Иначе - false
	 */
	public static function getProductById($id)
	{
		if ($id)
		{
			$product = Product::findFirst($id);

			if (count($product) > 0)
				return $product;
			else
				return false;
		}
	}

	/**
	 * @param int|null $categoryId
	 * @return Product[]|null Возвращает товары
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
					$tempProduct = Product::findFirst($productId->product_id);

					if ($tempProduct)
						$products[] = $tempProduct;
				}

				return $products;
			}
			else
				return null;
		} else
		{
			$tempProducts = Product::find();

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
}