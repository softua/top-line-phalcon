<?php
/**
 * Created by Ruslan Koloskov
 * Date: 10.06.14
 * Time: 10:00
 */

namespace App\Models;


class CategoryImageModel extends \Phalcon\Mvc\Model
{
	public function getSource()
	{
		return 'categories_images';
	}

	public $id;
	public $pathname;
	public $category_id;

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