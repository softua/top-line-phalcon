<?php
/**
 * Created by Ruslan Koloskov
 * Date: 07.05.14
 * Time: 13:40
 */

namespace App\Models;

class ProductProperty extends \Phalcon\Mvc\Collection
{
	public function getSource()
	{
		return 'properties';
	}

	public static function getAllProperties()
	{
		$props = ProductProperty::find([
			'sort' => ['name' => 1]
		]);

		if(count($props) > 0) return $props;
		else return null;
	}


} 