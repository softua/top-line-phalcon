<?php
/**
 * Created by Ruslan Koloskov
 * Date: 09.06.14
 * Time: 17:12
 */

namespace App\Models;


class ProductFile extends \Phalcon\Mvc\Model
{
	public function getSource()
	{
		return 'products_files';
	}

	public function initialize()
	{
		$this->belongsTo('product_id', '\App\Models\Product', 'id', [
			'alias' => 'product'
		]);
	}

	public $id;
	public $name;
	public $pathname;
	public $product_id;

	public static function deleteFiles($fileName)
	{
		$flag = true;

		while ($flag)
		{
			try {
				unlink($fileName);
				$flag = false;
			}
			catch (\Exception $e) {
				self::deleteFiles($fileName);
			}
		}

		return true;
	}
}