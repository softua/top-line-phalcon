<?php
/**
 * Created by Ruslan Koloskov
 * Date: 07.05.14
 * Time: 13:40
 */

namespace App\Models;

class PossibleBrands extends \Phalcon\Mvc\Model
{
	public function getSource()
	{
		return 'possible_brands';
	}

	public static function getAllTypes()
	{
		$brands = PossibleBrands::find();

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

			$brands = PossibleBrands::find([
				'name = :name:',
				'bind' => ['name' => $brand]
			]);

			if (count($brands) < 1)
			{
				$newBrand = new PossibleBrands();
				$newBrand->name = $brand;
				$newBrand->save();
			}
		}
	}
}