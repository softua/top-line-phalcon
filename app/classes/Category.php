<?php
/**
 * Created by Ruslan Koloskov
 * Date: 30.06.14
 * Time: 23:35
 */

namespace App;


use App\Models\CategoryModel;

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
		$this->edit_link = $this->_url->get('admin/editcategory/') . $this->id;
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

	private function setParentsCategories($di, $includeCurrent = true)
	{
		if ($includeCurrent) {
			$parentId = $this->id;
		} else {
			$parentId = $this->parent_id;
		}
		while($parentId) {
			$dbCat = Models\CategoryModel::findFirst($parentId);
			$parentId = $dbCat->parent_id;
			$tempCat = new self();
			$tempCat->setDi($di);
			$tempCat->id = $dbCat->id;
			$tempCat->name = $dbCat->name;
			$tempCat->parent_id = $dbCat->parent_id;
			$tempCat->seo_name = $dbCat->seo_name;
			$tempCat->sort = $dbCat->sort;
			$tempCat->setLink();
			array_unshift($this->parentsArray, $tempCat);
		}
		if (!count($this->parentsArray)) {
			$this->parentsArray = null;
		}
	}

	public function getParentsCategories()
	{
		if ($this->parentsArray === null) {
			return null;
		}
		if (!count($this->parentsArray)) {
			$this->setParentsCategories($this->_di);
			return $this->parentsArray;
		} else {
			return $this->parentsArray;
		}
	}

	public static function getMainCategories($di, $images = false, $activeCats = null)
	{
		$mainCats = Models\CategoryModel::getMainCategories();
		if ($mainCats) {
			$categoriesArray = [];
			foreach ($mainCats as $dbCat) {
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
			if ($activeCats && is_array($activeCats)) {
				if (count($activeCats) === 1) {
					if (is_string($activeCats[0])) {
						$cats = CategoryModel::query()
							->where('seo_name = ?1')->bind([1 => $activeCats[0]])
							->execute();
					} else {
						$cats = CategoryModel::query()
							->where('seo_name = ?1')->bind([1 => $activeCats[0]->seo_name])
							->execute();
					}
				} else {
					$queryArray = [];
					foreach ($activeCats as $item) {
						if (is_string($item)) {
							$queryArray[] = $item;
						} else {
							$queryArray[] = $item->seo_name;
						}
					}
					$cats = CategoryModel::query()
						->inWhere('seo_name', $queryArray)
						->execute();
				}
				if (count($cats)) {
					foreach ($cats as $cat) {
						$newCat = new self();
						$newCat->setDi($di);
						$newCat->id = $cat->id;
						foreach ($categoriesArray as $item) {
							if ($item->seo_name === $newCat->getParentsCategories()[0]->seo_name) {
								$item->active = true;
							}
						}
					}
				}
			}
			return $categoriesArray;
		} else {
			return null;
		}
	}

	public static function getChildrenCategoriesByParentSeoName($di, $seoName)
	{
		$dbCat = Models\CategoryModel::findFirst([
			'seo_name = ?1',
			'bind' => [1 => $seoName]
		]);
		if (!$dbCat) {
			return null;
		}
		$children = Models\CategoryModel::find([
			'parent_id = :id:',
			'bind' => ['id' => $dbCat->id],
			'order' => 'sort, name'
		]);
		if (!count($children)) {
			return null;
		}
		$cats = [];
		foreach ($children as $cat) {
			$newCat = new self();
			$newCat->setDi($di);
			$newCat->id = $cat->id;
			$newCat->name = $cat->name;
			$newCat->parent_id = $cat->parent_id;
			$newCat->seo_name = $cat->seo_name;
			$newCat->sort = $cat->sort;
			$newCat->setLink();
			$newCat->setImages();
			$cats[] = $newCat;
		}
		return $cats;
	}

	public static function getCategoryBySeoName($di, $seoName, $parents = false)
	{
		$dbCat = CategoryModel::findFirst([
			'seo_name = ?1',
			'bind' => [1 => $seoName]
		]);
		if (!$dbCat) {
			return null;
		}
		$cat = new self();
		$cat->setDi($di);
		$cat->id = $dbCat->id;
		$cat->name = $dbCat->name;
		$cat->parent_id = $dbCat->parent_id;
		$cat->seo_name = $dbCat->seo_name;
		$cat->sort = $dbCat->sort;
		$cat->setLink();
		if ($parents) {
			$cat->setParentsCategories($di);
		}
		return $cat;
	}

	public static function getCategoryById($di, $id, $images = false, $parents = false)
	{
		$category = new self();
		$category->setDi($di);

		if ($id && preg_match('/\d+/', $id)) {
			$cat = Models\CategoryModel::query()
				->where('id = ?1')->bind([1 => $id])
				->execute()
				->getFirst();
			if ($cat) {
				$category->id = $cat->id;
				$category->name = $cat->name;
				$category->parent_id = $cat->parent_id;
				$category->seo_name = $cat->seo_name;
				$category->setLink();
				if ($images) {
					$category->setImages();
				}
				if ($parents) {
					$category->setParentsCategories($di);
				}
			}
		}
		return $category;
	}
}