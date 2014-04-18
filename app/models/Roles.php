<?
namespace App\Models;

class Roles extends \Phalcon\Mvc\Collection
{

	public function getSource()
	{
		return 'roles';
	}
}