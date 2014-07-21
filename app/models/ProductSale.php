<?php
/**
 * Created by Ruslan Koloskov
 * Date: 25.06.14
 * Time: 20:18
 */

namespace App\Models;


class ProductSale extends \Phalcon\Mvc\Model
{
	protected $_di;
	protected $_url;

	//DB properties
	public $id;
	public $product_id;
	public $page_id;

	public $dbFields = ['id', 'product_id', 'page_id'];

	public function setDI($di = null)
	{
		if (!$this->_di) {
			$this->_di = \Phalcon\DI::getDefault();
			$this->_url = $this->_di->get('url');
		}
	}

	public function onConstruct()
	{
		$this->setDI();
	}

	public function getSource()
	{
		return 'products_sales';
	}

	public function initialize()
	{
		$this->belongsTo('product_id', '\App\Models\ProductModel', 'id', [
			'alias' => 'product'
		]);
		$this->belongsTo('page_id', '\App\Models\PageModel', 'id', [
			'alias' => 'sale'
		]);

		$this->useDynamicUpdate(true);
	}

	public function dbSave($data = null)
	{
		return $this->save(null, $this->dbFields);
	}
}