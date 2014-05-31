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

	/***
	 * Проверка является ли Seo-название уникальным
	 * @param string $name seo-название, которое проверяется
	 * @return bool Возвращает true, если имя уникально. Иначе false
	 */
	public static function isUniqueSeoName($name)
	{
		if (!$name)
			return false;

		$products = Product::find([
			'conditions' => ['seo_name' => $name]
		]);

		$products = Product::query()
			->where('seo_name = :name:')
			->bind(['name' => $name])
			->execute();

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