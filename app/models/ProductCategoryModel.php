<?php
/**
 * Created by Ruslan Koloskov
 * Date: 31.05.14
 * Time: 18:14
 */

namespace App\Models;


class ProductCategoryModel extends \Phalcon\Mvc\Model
{
	public function getSource()
	{
		return 'products_categories';
	}

	public function initialize()
	{
		$this->belongsTo('product_id', '\App\Models\ProductModel', 'id');
		$this->belongsTo('category_id', '\App\Models\CategoryModel', 'id');
	}

	public $id;
	public $product_id;
	public $category_id;
}