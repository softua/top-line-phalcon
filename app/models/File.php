<?php
/**
 * Created by Ruslan Koloskov
 * Date: 03.07.14
 * Time: 15:29
 */

namespace App\Models;


class File extends \Phalcon\Mvc\Model
{
	protected $_di;
	protected $_url;

	//DB properties
	public $id;
	public $name;
	public $pathname;
	public $product_id;

	public $dbFields = [
		'id', 'name', 'pathname', 'product_id'
	];

	public function getSource()
	{
		return 'products_files';
	}

	public function initialize()
	{
		$this->belongsTo('product_id', '\App\Models\Product', 'id', [
			'alias' => 'product'
		]);

		$this->setDI();
	}

	public function setDI($di = null)
	{
		$this->_di = \Phalcon\DI::getDefault();
		$this->_url = $this->_di->get('url');
	}

	public function dbSave($data = null)
	{
		if ($this->save($data, $this->dbFields)) return true;
		else return false;
	}

	/**
	 * @param Product $prod
	 * @return self[] | null
	 */
	public static function getFilesByProduct(Product $prod)
	{
		$files = self::query()
			->where('product_id = ?1')->bind([1 => $prod->id])
			->orderBy('name')
			->execute();

		if (count($files)) return (array)$files;
		else return null;

	}

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