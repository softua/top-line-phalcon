<?php
/**
 * Created by Ruslan Koloskov
 * Date: 01.06.14
 * Time: 1:03
 */

namespace App\Models;


class ProductParam extends \Phalcon\Mvc\Model
{
	public function getSource()
	{
		return 'products_params';
	}

	public $id;
	public $name;
	public $value;
	public $product_id;
	public $sort;

	public static function getParamsByProductId($prodId)
	{
		if ($prodId && preg_match('/\d+/', $prodId))
		{
			$temp = ProductParam::find([
				'product_id = :id:',
				'bind' => ['id' => $prodId],
				'order' => 'sort'
			]);

			if ($temp->count() > 0) return $temp;
			else return null;

		} else
		{
			return null;
		}
	}
} 