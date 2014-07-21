<?
namespace App\Models;

class Role extends \Phalcon\Mvc\Model
{
	protected $_di;
	protected $_url;

	//DB properties
	public $id;
	public $name;
	public $description;

	public $dbFields = ['id', 'name', 'description'];

	public function getSource()
	{
		return 'roles';
	}

	public function initialize()
	{
		$this->hasMany('id', '\App\Models\UserModel', 'role_id', [
			'alias' => 'users'
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
}