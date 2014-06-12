<?php
/**
 * Created by Ruslan Koloskov
 * Date: 09.06.14
 * Time: 10:15
 */

namespace App\Models;


class ProductImage extends \Phalcon\Mvc\Model
{
	public function getSource()
	{
		return 'products_images';

	}

	public $id;
	public $extension;
	public $product_id;
	public $sort;

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