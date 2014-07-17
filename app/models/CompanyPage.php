<?php
/**
 * Created by Ruslan Koloskov
 * Date: 03.07.14
 * Time: 15:50
 */

namespace App\Models;


class CompanyPage extends Page
{
	public $path;

	public static function getCompanyPages()
	{
		/** @var self[] $pages */
		$pages = self::query()
			->where('type_id = \'1\'')
			->andWhere('public = \'1\'')
			->orderBy('sort, name')
			->execute()->filter(function(self $page) {
				$page->time = strtotime($page->time);
				$page->path = \Phalcon\DI::getDefault()['url']->get('page/show/' . $page->seo_name);
				return $page;
			});

		if (count($pages)) return $pages;
		else return null;
	}

	public static function getPageBySeoName($seoName)
	{
		/** @var self $page */
		$page = self::query()
			->where('seo_name = ?1')->bind([1 => $seoName])
			->execute()
			->getFirst();

		if (!$page) return null;

		$page->time = strtotime($page->time);
		$page->path = \Phalcon\DI::getDefault()['url']->get('page/show/' . $page->seo_name);

		return $page;
	}
}