<?php
/**
 * Created by Ruslan Koloskov
 * Date: 28.04.14
 * Time: 16:19
 */

namespace App\Models;

class Category extends \Phalcon\Mvc\Collection
{
	public function getSource()
	{
		return 'categories';
	}

	public static  function getMainCategories()
	{
		$mainCats = Category::find([
			'conditions' => ['parent' => '0'],
			'sort' => ['sort' => 1, 'name' => 1]
		]);

		if(count($mainCats) > 0)
			return $mainCats;
		else
			return null;
	}

	public static function getAllCategories()
	{
		$cats = Category::find([
			'sort' => ['sort' => 1, 'name' => 1]
		]);

		if (count($cats) > 0)
			return $cats;
		else
			return null;
	}

	public static function getCategories($parentId)
	{
		if($parentId == 0)
			return null;

		$result = Category::find([
			'conditions' => ['parent' => $parentId],
			'sort' => ['sort' => 1, 'name' => 1]
		]);

		if(count($result) > 0)
			return $result;
		else
			return null;
	}

	public static function getCategory($id)
	{
		if($id != 0) {
			$category = Category::findById($id);
			if(count($category) > 0)
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
			$cat = Category::findById($id);
			array_unshift($array, $cat->name);
			if($cat->parent != 0)
				recursiveGetParent($cat->parent, $array);
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
		if ($id)
		{
			$flag = true;
			$resultArray = [];
			$result[] = Category::getCategory($id);

			while($flag)
			{
				if (!$result[count($result)-1]->parent || $result[count($result)-1]->parent == '0')
				{
					$flag = false;
					break;
				} else
				{
					$result[] = Category::getCategory($result[count($result)-1]->parent);
				}
			}

			if (count($result) > 1)
				$result = array_reverse($result);

			for ($i = 0; $i < count($result); $i++)
			{
				if ($i == count($result) - 1)
				{
					$resultArray['id'] = (string)$result[$i]->_id;
					$resultArray['parent'] = $result[$i]->parent;
					$resultArray['seo'] = $result[$i]->seo;
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
}