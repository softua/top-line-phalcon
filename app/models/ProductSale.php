<?php
/**
 * Created by Ruslan Koloskov
 * Date: 25.06.14
 * Time: 20:18
 */

namespace App\Models;


class ProductSale extends \Phalcon\Mvc\Model
{
	public function getSource()
	{
		return 'products_sales';
	}

	public function initialize()
	{
		$this->belongsTo('product_id', '\App\Models\Product', 'id', [
			'alias' => 'product'
		]);
		$this->belongsTo('page_id', '\App\Models\Page', 'id', [
			'alias' => 'sale'
		]);
	}
	public $id;
	public $product_id;
	public $page_id;
}