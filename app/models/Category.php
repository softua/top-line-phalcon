<?php
/**
 * Created by Ruslan Koloskov
 * Date: 30.06.14
 * Time: 23:35
 */

namespace App\Models;

class Category extends \Phalcon\Mvc\Model
{
	public function getSource()
	{
		return 'categories';
	}

	public function initialize()
	{
		$this->hasManyToMany('id', '\App\Models\Product', 'category_id', 'product_id', '\App\Models\Product', 'id', [
			'alias' => 'products'
		]);
		$this->hasMany('id', '\App\Models\CategoryImage', 'category_id', [
			'alias' => 'images'
		]);

		$this->useDynamicUpdate(true);
		$this->setDI();

	}

	protected $_di;
	protected $_url;

	//DB properties
	public $id;
	public $name;
	public $parent_id;
	public $seo_name;
	public $sort;

	public $dbFields = [
		'id', 'name', 'parent_id', 'seo_name', 'sort'
	];

	public $link;
	public $edit_link;
	public $active = false;
	public $images;
	public $mainImage;
	private $_parentsCats;
	private $_childrenCats;

	/**
	 * Если дочерние категории не инициализированы, вызывает метод для получения категорий из БД.<br>
	 * Потом еще раз проверяет уже проинициализированые дочерние категории.<br>
	 * Если дочерние категории проинициализированы, сразу проверяет есть категории или нет.
	 * @return bool Возвращает true, если дочерние категории есть, иначе - false.
	 */
	private function areThereChildrenCategories()
	{
		if ($this->_childrenCats === null) {
			$this->setChildrenCats();
			if ($this->_childrenCats === false) {
				return false;
			} else {
				return true;
			}
		} elseif ($this->_childrenCats === false) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Вызывается только из areThereChildrenCategories()
	 */
	private function setChildrenCats()
	{
		$children = self::query()
			->where('parent_id = ?1')->bind([1 => $this->id])
			->orderBy('sort, name')
			->execute();

		if (count($children)) $this->_childrenCats = $children;
		else $this->_childrenCats = false;
	}

	private function setParentsCategories($includeCurrent = true)
	{
		if ($includeCurrent) {
			$parentId = $this->id;
		}
		else {
			$parentId = $this->parent_id;
		}
		while($parentId) {
			/** @var self | null $dbCat */
			$dbCat = self::findFirst($parentId);
			$parentId = $dbCat->parent_id;
			$dbCat->setLink();
			if ($this->_parentsCats === null) $this->_parentsCats = [];
			array_unshift($this->_parentsCats, $dbCat);
		}
		if (!count($this->_parentsCats)) {
			$this->_parentsCats = false;
		}
	}

	public function setDI($di = null)
	{
		$this->_di = \Phalcon\DI::getDefault();
		$this->_url = $this->_di['url'];
	}

	/**
	 * Устанавливает ссылки категории (обычная + редактирование)
	 * @param null | string $customLink Если передается кастомная ссылка, она будет установлена.
	 */
	public function setLink($customLink = null)
	{
		$this->edit_link = $this->_di['url']->get('admin/editcategory/') . $this->id;
		if ($customLink) {
			$this->link = $customLink;
			return;
		}
		if (!$this->link) {
			if ($this->seo_name === 'servisnyie_uslugi_montaj') {
				$this->link = $this->_di['url']->get('service');
			} elseif ($this->areThereChildrenCategories()) {
				$this->link = $this->_di['url']->get('catalog/show/') . $this->seo_name . '/';
			} else {
				$this->link = $this->_di['url']->get('products/list/') . $this->seo_name . '/';
			}
		}
	}

	public function setImages()
	{
		/** @var ImageCategory[] $images */
		$images = ImageCategory::query()
			->where('belongs = \'category\'')
			->andWhere('belongs_id = ?1', [1 => $this->id])
			->orderBy('sort')
			->execute()->filter(function(ImageCategory $image) {
				$image->setPaths();
				return $image;
			});


		if (count($images)) {
			$this->images = $images;
			$this->mainImage = $images[0];
		}
		else {
			$this->images = false;
			$this->mainImage = false;
		}
	}

	/**
	 * @return Category[] | null
	 */
	public function getParentsCategories()
	{
		if ($this->_parentsCats === null) {
			$this->setParentsCategories();
			if (!$this->_parentsCats) return null;
			else return $this->_parentsCats;
		}
		elseif (!$this->_parentsCats) return null;
		else return $this->_parentsCats;
	}

	public function dbSave($data = null)
	{
		if ($this->save($data, $this->dbFields))
			return true;
		else
			return false;
	}

	/**
	 * @param bool $images
	 * @param null | string[] $activeCats
	 * @return self[] | null
	 */
	public static function getMainCategories($images = false, $activeCats = null)
	{
		/** @var Category[] $mainCats */
		$mainCats = self::query()
			->where('parent_id = \'0\'')
			->orderBy('sort, name')
			->execute();
		if ($mainCats) {
			$categoriesArray = [];
			foreach ($mainCats as $dbCat) {
				$dbCat->setLink();
				if ($images) {
					$dbCat->setImages();
				}
				$categoriesArray[] = $dbCat;
			}
			if ($activeCats && is_array($activeCats)) {
				if (count($activeCats) === 1) {
					if (is_string($activeCats[0])) {
						$cats = self::query()
							->where('seo_name = ?1')->bind([1 => $activeCats[0]])
							->execute();
					} else {
						$cats = self::query()
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
					/** @var Category[] $cats */
					$cats = self::query()
						->inWhere('seo_name', $queryArray)
						->execute();
				}
				if (count($cats)) {
					foreach ($cats as $cat) {
						foreach ($categoriesArray as $item) {
							if ($item->seo_name === $cat->getParentsCategories()[0]->seo_name) {
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

	public static function getChildrenCategoriesByParentSeoName($seoName)
	{
		/** @var Category $mainCat */
		$mainCat = self::query()
			->where('seo_name = ?1')->bind([1 => $seoName])
			->execute()
			->getFirst();

		if (!$mainCat) return null;
		else {
			/** @var Category[] $children */
			$children = self::find([
				'parent_id = :id:',
				'bind' => ['id' => $mainCat->id],
				'order' => 'sort, name'
			]);

			$children = self::query()
				->where('parent_id = ?1')->bind([1 => $mainCat->id])
				->orderBy('sort, name')
				->execute()->filter(function(Category $item) {
					$item->setLink();
					$item->setImages();
					return $item;
				});
			if (count($children)) return $children;
			else return null;
		}
	}

	/**
	 * @param string $seoName
	 * @param bool $parents
	 * @return self | null
	 */
	public static function getCategoryBySeoName($seoName, $parents = false)
	{
		/** @var self | null $cat */
		$cat = self::query()
			->where('seo_name = ?1')->bind([1 => $seoName])
			->execute()
			->getFirst();

		if (!$cat) return null;
		else {
			$cat->setLink();
			if ($parents) {
				$cat->setParentsCategories();
			}
			return $cat;
		}
	}

	public static function getCategoryById($id, $images = false, $parents = false)
	{
		if ($id && preg_match('/\d+/', $id)) {
			/** @var Category $cat */
			$cat = self::query()
				->where('id = ?1')->bind([1 => $id])
				->execute()
				->getFirst();
			if ($cat) {
				$cat->setLink();
				if ($images) {
					$cat->setImages();
				}
				if ($parents) {
					$cat->setParentsCategories();
				}
				return $cat;
			}
			else return null;
		}
	}

	public static function getAllCategories()
	{
		/** @var Category[] $cats */
		$cats = self::query()
			->order('sort, name')
			->execute();

		if (count($cats))
			return $cats;
		else
			return null;
	}

	public static function getCategories($parentId)
	{
		if($parentId == 0) return null;

		$result = self::query()
			->where('parent_id = :parentId:')->bind(['parentId' => $parentId])
			->order('sort, name')
			->execute();

		$arrayResult = [];
		if(count($result)) {
			foreach ($result as $cat)
				$arrayResult[] = $cat;

			return $arrayResult;
		}
		else
			return null;
	}

	public static function getCategory($id)
	{
		if($id && preg_match('/\d+/', $id)) {
			$category = self::findFirst($id);
			if($category) return $category;
			else return null;
		} else
			return null;
	}

	public static function getFullCategoryName($parentId)
	{
		$fullCategoryName = [];
		$resultString = '';
		$index = 0;

		function recursiveGetParent($id, &$array) {
			$cat = Category::find($id);
			array_unshift($array, $cat->name);
			if($cat->parent_id != 0)
				recursiveGetParent($cat->parent_id, $array);
		}

		if($parentId == 0) {
			$fullCategoryName[] = 'Корневая категория';
		} else {
			recursiveGetParent($parentId, $fullCategoryName);
		}

		foreach($fullCategoryName as $str) {
			if($index < count($fullCategoryName) - 1) {
				$resultString .= $str . ' => ';
			} else {
				$resultString .= $str;
			}
			$index++;
		}

		return $resultString;

	}

	public static function getCategoryWithFullName($id)
	{
		if ($id && preg_match('/\d+/', $id))
		{
			$flag = true;
			$resultArray = [];
			$result[] = self::getCategory($id);

			while($flag)
			{
				if (!$result[count($result)-1]->parent_id || $result[count($result)-1]->parent_id == '0') {
					$flag = false;
					break;
				} else {
					$result[] = self::getCategory($result[count($result)-1]->parent_id);
				}
			}

			if (count($result) > 1)
				$result = array_reverse($result);

			for ($i = 0; $i < count($result); $i++) {
				if ($i == count($result) - 1) {
					$resultArray['id'] = $result[$i]->id;
					$resultArray['parent'] = $result[$i]->parent_id;
					$resultArray['seo'] = $result[$i]->seo_name;
					$resultArray['sort'] = $result[$i]->sort;
					$resultArray['name'] = $result[$i]->name;
					$resultArray['full_name'] .= $result[$i]->name;
				} else {
					$resultArray['full_name'] .= $result[$i]->name . ' >> ';
				}
			}

			return $resultArray;

		} else
			return null;
	}

	public static function getRootCategoryByChildId($id)
	{
		if (!$id || !preg_match('/\d+/', $id)) return false;

		/** @var Category $currentCategory */
		$currentCategory = self::findFirst($id);
		if (!$currentCategory) return false;

		while (true)
		{
			if ($currentCategory->parent_id) {
				/** @var Category|null $currentCategory */
				$currentCategory = self::findFirst($currentCategory->parent_id);
			}
			else {
				return $currentCategory;
			}
		}
	}
}