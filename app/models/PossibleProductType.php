<?php
/**
 * Created by Ruslan Koloskov
 * Date: 07.05.14
 * Time: 13:40
 */

namespace App\Models;

class PossibleProductType extends \Phalcon\Mvc\Model
{
	protected $_di;
	protected $_url;

	//DB properties
	public $id;
	public $name;

	public $dbFields = ['id', 'name'];

	public function getSource()
	{
		return 'possible_product_types';
	}

	public function initialize()
	{
		$this->useDynamicUpdate(true);
		$this->setDI();
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
		$types = self::find();

		if(count($types) > 0) return $types;
		else return null;
	}

	public static  function getAllTypesAsString()
	{
		$types = self::getAllTypes();

		$result = [];

		if($types !== null) {
			foreach($types as $type) {
				$result[] = $type->name;
			}

			return json_encode($result);
		} else return null;
	}

	public static function addType($type)
	{
		if ($type)
		{
			$types = self::query()
				->where('name = :name:')
				->bind(['name' => $type])
				->execute();

			if (count($types) < 1)
			{
				$newType = new self();
				$newType->name = $type;
				$newType->save();
			}
		}
	}
} 