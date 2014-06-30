<?
/**
 * Created by Ruslan Koloskov
 * Date: 11.04.14
 * Time: 13:17
 */

namespace App\Controllers;
use App\Models;
class BaseFrontController extends \Phalcon\Mvc\Controller
{
	public function initialize()
	{
		$this->tag->setTitle($this->di->get('config')->name);
		$this->tag->setTitleSeparator(' :: ');

		$this->view->top_products = $this->getTopProducts();
		$this->view->static_pages = $this->getSubmenuWithCompanyPages();
	}

	public function getTopProducts()
	{
		$topProducts = Models\ProductModel::find([
			'top = 1',
			'limit' => 5
		]);
		if (count($topProducts))
		{
			$topProductsForView = [];
			foreach ($topProducts as $topProd)
			{
				$tempTopProd = [];
				$tempTopProd['name'] = $topProd->name;
				$tempTopProd['href'] = '/products/show/' . $topProd->seo_name;
				$prodTopImage = Models\ProductImageModel::find([
					'product_id = ?1',
					'bind' => [1 => $topProd->id],
					'order' => 'sort'
				]);
				if (count($prodTopImage))
				{
					$tempTopProd['img'] = '/products/' . $prodTopImage[0]->product_id . '/images/' . $prodTopImage[0]->id . '__product_top.' . $prodTopImage[0]->extension;
				} else {
					$tempTopProd['img'] = '/img/no_foto.png';
				}
				$topProductsForView[] = $tempTopProd;
			}
			return $topProductsForView;
		}
		return false;
	}

	public function getSubmenuWithCompanyPages()
	{
		$staticCompanyPages = Models\PageModel::find([
			'type_id = 1 AND public = 1',
			'order' => 'name'
		]);
		if (count($staticCompanyPages)) {
			$pages = [];
			foreach ($staticCompanyPages as $page) {
				$tempPage = [];
				$tempPage['name'] = $page->name;
				$tempPage['href'] = '/company/show/' . $page->seo_name;
				$pages[] = $tempPage;
			}
			return $pages;
		} else {
			return null;
		}
	}
}