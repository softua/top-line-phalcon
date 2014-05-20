<?php
/**
 * Created by Ruslan Koloskov
 * Date: 15.05.14
 * Time: 13:32
 */

namespace App\Models;


class Product extends \Phalcon\Mvc\Collection
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

		if (count($products) > 1)
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
			$seoNameArray[] = $product->country;

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
		if ($id && strlen($id) == 24)
		{
			$product = Product::findById($id);

			if (count($product) > 0)
				return $product;
			else
				return false;
		}
	}

	/**
	 * @param string|null $categoryId
	 * @return [Product]|null Возвращает товары
	 */
	public static function getProducts($categoryId = null)
	{
		if ($categoryId)
		{
			$products = Product::find([
				'conditions' => ['categories' => $categoryId]
			]);

			if (count($products) > 0)
				return $products;
			else
				return null;
		} else
		{
			$products = Product::find();

			if (count($products) > 0)
				return $products;
			else
				return null;
		}
	}
} 