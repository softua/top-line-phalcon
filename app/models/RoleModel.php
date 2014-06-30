<?
namespace App\Models;

class RoleModel extends \Phalcon\Mvc\Model
{

	public function getSource()
	{
		return 'roles';
	}

	public function initialize()
	{
		$this->hasMany('id', '\App\Models\UserModel', 'role_id', [
			'alias' => 'users'
		]);
	}

	public $id;
	public $name;
	public $description;
}