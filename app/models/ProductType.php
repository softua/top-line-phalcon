<?php
/**
 * Created by Ruslan Koloskov
 * Date: 07.05.14
 * Time: 13:40
 */

namespace App\Models;

class ProductType extends \Phalcon\Mvc\Collection
{
	public function getSource()
	{
		return 'product_types';
	}

	public static function getAllTypes()
	{
		$types = ProductType::find();

		if(count($types) > 0) return $types;
		else return null;
	}

	public static  function getAllTypesAsString()
	{
		$types = self::getAllTypes();

		$result = [];

		if($types !== null) {
			foreach($types as $type) {
				$result[] = $type->name;
			}

			return json_encode($result);
		} else return null;
	}

	public static function addType($type)
	{
		if ($type)
		{
			$types = ProductType::find([
				'conditions' => ['name' => $type]
			]);

			if (count($types) < 1)
			{
				$newType = new ProductType();
				$newType->name = $type;
				$newType->save();
			}
		}
	}
} 