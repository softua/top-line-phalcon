<?php
/**
 * Created by Ruslan Koloskov
 * Date: 26.05.14
 * Time: 10:37
 */

namespace App\Models;


class PossibleParameter extends \Phalcon\Mvc\Model
{
	protected $_di;
	protected $_url;

	//DB properties
	public $id;
	public $name;

	public $dbFields = ['id', 'name'];

	public function getSource()
	{
		return 'possible_params';
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

	/**
	 * Получение всех параметров
	 * @param bool $needJson определяет нужно ли отдавать JSON
	 * @return array|null|string Возвращает массив параметров, если они есть, иначе null. Если $needJson = true, вернет JSON
	 */
	public static function getAllParameters($needJson = false)
	{
		$paramsObjs = self::query()
			->order('name')
			->execute();

		if ($needJson)
		{
			$names = [];

			foreach ($paramsObjs as $obj)
			{
				$names[] = $obj->name;
			}

			if(count($names) > 0) echo json_encode($names);
			else echo json_encode(null);

		} else {

			if(count($paramsObjs) > 0) return $paramsObjs;
			else return null;
		}
	}

	public static function addParameter($paramName)
	{
		if ($paramName)
		{
			$params = self::findFirst([
				'name = :name:',
				'bind' => ['name' => $paramName]
			]);

			if (!$params)
			{
				$parameter = new self();
				$parameter->name = $paramName;
				$parameter->save();
			}
		}
	}
}