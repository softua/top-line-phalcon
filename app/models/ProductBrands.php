<?php
/**
 * Created by Ruslan Koloskov
 * Date: 07.05.14
 * Time: 13:40
 */

namespace App\Models;

class ProductBrands extends \Phalcon\Mvc\Collection
{
	public function getSource()
	{
		return 'product_brands';
	}

	public static function getAllTypes()
	{
		$brands = ProductBrands::find();

		if(count($brands) > 0) return $brands;
		else return null;
	}

	public static function getAllTypesAsString()
	{
		$brands = self::getAllTypes();

		$result = [];

		if($brands !== null) {
			foreach($brands as $brand) {
				$result[] = $brand->name;
			}

			return json_encode($result);
		} else
			return null;
	}

	public static function addBrand($brand)
	{
		if ($brand)
		{
			$brands = ProductBrands::find([
				'conditions' => ['name' => $brand]
			]);

			if (count($brands) < 1)
			{
				$newBrand = new ProductBrands();
				$newBrand->name = $brand;
				$newBrand->save();
			}
		}
	}
} 