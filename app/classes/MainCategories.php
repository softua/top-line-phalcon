<?php
/**
 * Created by Ruslan Koloskov
 * Date: 30.06.14
 * Time: 23:50
 */

namespace App;

use App\Models;

class MainCategories
{
	private $categoriesArray;
	protected $_di;

	public function __construct($di)
	{
		$this->_di = $di;
	}

	public function setMainCategories()
	{
		$dbCats = Models\CategoryModel::getMainCategories();
		if ($dbCats) {
			foreach ($dbCats as $dbCat) {
				$newCat = new Category();
				$newCat->setId($dbCat->id);
				$newCat->name = $dbCat->name;
				$newCat->parent_id = $dbCat->parent_id;
				$newCat->seo_name = $dbCat->seo_name;
				$newCat->sort = $dbCat->sort;
				$newCat->edit_link = $this->_di->getUrl()->get('admin/editcategory/') . $dbCat->id;
				$this->categoriesArray[] = $newCat;
			}
		}
	}

	public function getMainCategories()
	{
		return $this->categoriesArray;
	}
} 