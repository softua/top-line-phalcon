<?php
/**
 * Created by Ruslan Koloskov
 * Date: 16.06.14
 * Time: 23:26
 */

namespace App\Models;


class ProductVideo extends \Phalcon\Mvc\Model
{
	public function getSource()
	{
		return 'products_videos';
	}

	public $id;
	public $name;
	public $href;
	public $sort;
	public $product_id;


}