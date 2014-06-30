<?php
/**
 * Created by Ruslan Koloskov
 * Date: 30.06.14
 * Time: 23:35
 */

namespace App;


class Category
{
	private $_id;
	public $name;
	public $parent_id;
	public $seo_name;
	public $sort;
	public $link;
	public $edit_link;
	public $active = false;

	public function setId($id)
	{
		if ($id && preg_match('/\d+/', $id)) {
			$this->_id = $id;
		}
	}
}