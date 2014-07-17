<?php
/**
 * Created by Ruslan Koloskov
 * Date: 10.07.14
 * Time: 1:30
 */

namespace App\Models;


class Image extends \Phalcon\Mvc\Model
{
	protected $_di;
	protected $_url;

	//DB properties
	public $id;
	public $extension;
	public $belongs;
	public $belongs_id;
	public $sort;

	public $dbFields = [
		'id', 'extension', 'belongs', 'belongs_id', 'sort'
	];

	public function getSource()
	{
		return 'images';
	}

	public function initialize()
	{
		$this->useDynamicUpdate(true);
		$this->setDI();
	}

	public function setDI($di = null)
	{
		if (!$this->_di) {
			$this->_di = \Phalcon\DI::getDefault();
			$this->_url = $this->_di->get('url');
		}
	}

	public function dbSave($data = null)
	{
		if ($this->save($data, $this->dbFields)) return true;
		else return false;
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