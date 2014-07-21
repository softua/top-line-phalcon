<?php
/**
 * Created by Ruslan Koloskov
 * Date: 16.06.14
 * Time: 23:26
 */

namespace App\Models;


class ProductVideo extends \Phalcon\Mvc\Model
{
	protected $_di;
	protected $_url;

	public $id;
	public $name;
	public $href;
	public $sort;
	public $product_id;

	public $dbFields = ['id', 'name', 'href', 'sort', 'product_id'];

	public function getSource()
	{
		return 'products_videos';
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
		if ($this->_di === null) $this->_di = \Phalcon\DI::getDefault();
		if ($this->_url === null) $this->_url = $this->_di->get('url');
	}

	/**
	 * @param array | null $data
	 * @return bool
	 */
	public function dbSave($data = null)
	{
		return $this->save(null, $this->dbFields);
	}

	/**
	 * @param Product $prod
	 * @return self[] | null
	 */
	public static function getVideosByProduct(Product $prod)
	{
		$videos = self::query()
			->where('product_id = ?1')->bind([1 => $prod->id])
			->orderBy('sort')
			->execute();

		if (!count($videos)) return null;
		else {
			$videoArray = $videos->filter(function($item) {
				return $item;
			});
			return $videoArray;
		}
	}
}