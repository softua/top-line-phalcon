<?php
/**
 * Created by Ruslan Koloskov
 * Date: 16.06.14
 * Time: 23:26
 */

namespace App\Models;


class ProductVideoModel extends \Phalcon\Mvc\Model
{
	public function getSource()
	{
		return 'products_videos';
	}

	public function initialize()
	{
		$this->belongsTo('product_id', '\App\Models\ProductModel', 'id', [
			'alias' => 'product'
		]);
	}

	public $id;
	public $name;
	public $href;
	public $sort;
	public $product_id;


}