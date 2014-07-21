<?php
/**
 * Created by Ruslan Koloskov
 * Date: 01.06.14
 * Time: 1:03
 */

namespace App\Models;


class ProductParam extends \Phalcon\Mvc\Model
{
	protected $_di;
	protected $_url;

	//DB properties
	public $id;
	public $name;
	public $value;
	public $product_id;
	public $sort;

	public $dbFields = ['id', 'name', 'value', 'product_id', 'sort'];

	public function getSource()
	{
		return 'products_params';
	}

	public function initialize()
	{
		$this->belongsTo('product_id', '\App\Models\ProductModel', 'id', [
			'alias' => 'product'
		]);

		$this->setDI();
		$this->useDynamicUpdate(true);
	}

	public function setDI($di = null)
	{
		if (!$this->_di) $this->_di = \Phalcon\DI::getDefault();
		if (!$this->_url) $this->_url = $this->_di->get('url');
	}

	public function dbSave($data = null)
	{
		return $this->save(null, $this->dbFields);
	}

	/**
	 * @param int $prodId
	 * @return self[] | null
	 */
	public static function getParamsByProductId($prodId)
	{
		if ($prodId && preg_match('/\d+/', $prodId)) {
			$items = self::query()
				->where('product_id = ?1')->bind([1 => $prodId])
				->orderBy('sort')
				->execute()->filter(function($item) {
					return $item;
				});
			if (count($items)) return $items;
			else return null;
		}
		else {
			return null;
		}
	}
} 