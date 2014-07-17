<?php
/**
 * Created by Ruslan Koloskov
 * Date: 07.05.14
 * Time: 13:40
 */

namespace App\Models;

class PossibleBrand extends \Phalcon\Mvc\Model
{
	protected $_di;
	protected $_url;

	//DB properties
	public $id;
	public $name;

	public $dbFields = ['id', 'name'];

	public function getSource()
	{
		return 'possible_brands';
	}

	public function initialize()
	{
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

	public static function getAllTypes()
	{
		$brands = self::query()->execute();

		if(count($brands)) return $brands;
		else return null;
	}

	public static function getAllTypesAsString()
	{
		$brands = self::getAllTypes();

		$result = [];

		if($brands !== null) {
			foreach($brands as $brand) {
				$result[] = $brand->name;
			}
			return json_encode($result);
		} else
			return null;
	}

	public static function addBrand($brand)
	{
		if ($brand) {
			$brands = self::find([
				'name = :name:',
				'bind' => ['name' => $brand]
			]);

			if (count($brands) < 1) {
				$newBrand = new self();
				$newBrand->name = $brand;
				$newBrand->save();
			}
		}
	}
}