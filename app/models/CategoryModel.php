<?php
/**
 * Created by Ruslan Koloskov
 * Date: 28.04.14
 * Time: 16:19
 */

namespace App\Models;

class CategoryModel extends \Phalcon\Mvc\Model
{
	public function getSource()
	{
		return 'categories';
	}

	public function initialize()
	{
		$this->hasManyToMany('id', '\App\Models\ProductCategoryModel', 'category_id', 'product_id', '\App\Models\ProductModel', 'id', [
			'alias' => 'products'
		]);
	}

	public static  function getMainCategories()
	{
		$mainCats = Category::query()
			->where('parent_id = 0')
			->orderBy('sort, name')
			->execute();

		if(count($mainCats) > 0)
			return $mainCats;
		else
			return null;
	}

	public static function getAllCategories()
	{
		$cats = Category::query()
			->order('sort, name')
			->execute();

		if (count($cats) > 0)
			return $cats;
		else
			return null;
	}

	public static function getCategories($parentId)
	{
		if($parentId == 0)
			return null;

		$result = Category::query()
			->where('parent_id = :parentId:')
			->bind(['parentId' => $parentId])
			->order('sort, name')
			->execute();

		$arrayResult = [];
		if(count($result) > 0)
		{
			foreach ($result as $cat)
			{
				$arrayResult[] = $cat;
			}

			return $arrayResult;
		}
		else
			return null;
	}

	public static function getCategory($id)
	{
		if($id) {
			$category = Category::findFirst($id);
			if($category)
				return $category;
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
			$result[] = Category::getCategory($id);

			while($flag)
			{
				if (!$result[count($result)-1]->parent_id || $result[count($result)-1]->parent_id == '0')
				{
					$flag = false;
					break;
				} else
				{
					$result[] = Category::getCategory($result[count($result)-1]->parent_id);
				}
			}

			if (count($result) > 1)
				$result = array_reverse($result);

			for ($i = 0; $i < count($result); $i++)
			{
				if ($i == count($result) - 1)
				{
					$resultArray['id'] = $result[$i]->id;
					$resultArray['parent'] = $result[$i]->parent_id;
					$resultArray['seo'] = $result[$i]->seo_name;
					$resultArray['sort'] = $result[$i]->sort;
					$resultArray['name'] = $result[$i]->name;
					$resultArray['full_name'] .= $result[$i]->name;
				} else
				{
					$resultArray['full_name'] .= $result[$i]->name . ' >> ';
				}
			}

			return $resultArray;

		} else
			return null;
	}

	/**
	 * @param int $id
	 * @return bool|Category
	 */
	public static function getRootCategoryByChildId($id)
	{
		if (!$id || !preg_match('/\d+/', $id))
			return false;
		$currentCategory = Category::findFirst($id);
		if (!$currentCategory)
			return false;
		while (true)
		{
			if ($currentCategory->parent_id)
			{
				$currentCategory = Category::findFirst($currentCategory->parent_id);
			}
			else {
				return $currentCategory;
			}
		}
	}
}