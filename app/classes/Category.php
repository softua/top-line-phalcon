<?php
/**
 * Created by Ruslan Koloskov
 * Date: 30.06.14
 * Time: 23:35
 */

namespace App;


class Category
{
	protected $_di;
	protected $_url;

	public $id;
	public $name;
	public $parent_id;
	public $seo_name;
	public $sort;
	public $link;
	public $edit_link;
	public $active = false;
	public $images = [];
	public $mainImage;
	private $parentsArray = [];

	public function setDi($di)
	{
		$this->_di = $di;
		$this->_url = $this->_di->get('url');
	}

	public function setLink($customLink = null)
	{
		if ($customLink) {
			$this->link = $customLink;
			return;
		}
		if (!$this->link) {
			if ($this->seo_name === 'servisnyie_uslugi_montaj') {
				$this->link = $this->_url->get('service');
			} elseif ($this->areThereChildrenCategories()) {
				$this->link = $this->_url->get('catalog/show/') . $this->seo_name . '/';
			} else {
				$this->link = $this->_url->get('products/list/') . $this->seo_name . '/';
			}
		}
	}

	private function areThereChildrenCategories()
	{
		$children = Models\CategoryModel::find([
			'parent_id = ?1',
			'bind' => [1 => $this->id]
		]);
		if (count($children))
			return true;
		else
			return false;
	}

	public function setImages()
	{
		$images = Models\CategoryImageModel::find([
			'category_id = ' . $this->id
		]);
		if (count($images)) {
			$i = 0;
			foreach ($images as $image) {
				if (file_exists($image->pathname)) {
					$this->images[] = $this->_url->getStatic() . $image->pathname;
					if ($i === 0)
						$this->mainImage = $this->_url->getStatic() . $image->pathname;
				} else {
					$this->images[] = $this->_url->getStatic('img/no_foto_110x110.png');
					if ($i === 0)
						$this->mainImage = $this->_url->getStatic('img/no_foto_110x110.png');
				}
				$i++;
			}
		} else {
			$this->mainImage = $this->_url->getStatic('img/no_foto_110x110.png');
		}
	}

	private function setParentsCategories()
	{
		$parentId = $this->parent_id;
		while($parentId) {
			$dbCat = Models\CategoryModel::findFirst($parentId);
			$parentId = $dbCat->parent_id;
			$tempCat = new self();
			$tempCat->id = $dbCat->id;
			$tempCat->name = $dbCat->name;
			$tempCat->parent_id = $dbCat->parent_id;
			$tempCat->seo_name = $dbCat->seo_name;
			$tempCat->sort = $dbCat->sort;
			$tempCat->setLink();
			$tempCat->edit_link = $this->_url->get('admin/editcategory/') . $dbCat->id;
			$this->parentsArray[] = $tempCat;
		}
		array_reverse($this->parentsArray);
	}

	public function getParentsCategories()
	{
		if (!count($this->parentsArray))
			$this->setParentsCategories();
		//TODO: возврат результата, если есть
	}

	public static function getMainCategories($di, $images = false)
	{
		$dbCats = Models\CategoryModel::getMainCategories();
		if ($dbCats) {
			$categoriesArray = [];
			foreach ($dbCats as $dbCat) {
				$newCat = new self();
				$newCat->setDi($di);
				$newCat->id = $dbCat->id;
				$newCat->name = $dbCat->name;
				$newCat->parent_id = $dbCat->parent_id;
				$newCat->seo_name = $dbCat->seo_name;
				$newCat->sort = $dbCat->sort;
				$newCat->edit_link = $di->get('url')->get('admin/editcategory/') . $dbCat->id;
				$newCat->setLink();
				if ($images) {
					$newCat->setImages();
				}
				$categoriesArray[] = $newCat;
			}
			return $categoriesArray;
		} else {
			return null;
		}
	}
}