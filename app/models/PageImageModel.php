<?php
/**
 * Created by Ruslan Koloskov
 * Date: 09.06.14
 * Time: 10:15
 */

namespace App\Models;


class PageImageModel extends \Phalcon\Mvc\Model
{
	public function getSource()
	{
		return 'pages_images';

	}

	public function initialize()
	{
		$this->belongsTo('page_id', '\App\Models\PageModel', 'id', [
			'alias' => 'page'
		]);
	}

	public $id;
	public $extension;
	public $page_id;
	public $sort;

	public static function deleteFiles($fileName)
	{
		$flag = true;

		while ($flag)
		{
			if (file_exists($fileName)) {
				try {
					unlink($fileName);
					$flag = false;
				}
				catch (\Exception $e) {
					self::deleteFiles($fileName);
				}
			} else {
				$flag = false;
			}
		}
		return true;
	}
}