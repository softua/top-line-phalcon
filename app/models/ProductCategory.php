<?php
/**
 * Created by Ruslan Koloskov
 * Date: 31.05.14
 * Time: 18:14
 */

namespace App\Models;


class ProductCategory extends \Phalcon\Mvc\Model
{
	protected $_di;
	protected $_url;

	//DB properties
	public $id;
	public $product_id;
	public $category_id;

	public $dbFields = [
		'id', 'product_id', 'category_id'
	];

	public function setDI($di = null)
	{
		$this->_di = \Phalcon\DI::getDefault();
		$this->_url = $this->_di->get('url');
	}

	public function getSource()
	{
		return 'products_categories';
	}

	public function initialize()
	{
		$this->belongsTo('product_id', '\App\Models\ProductModel', 'id');
		$this->belongsTo('category_id', '\App\Models\CategoryModel', 'id');

		$this->setDI();
	}
}