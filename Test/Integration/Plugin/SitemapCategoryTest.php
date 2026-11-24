<?php declare(strict_types=1);

namespace Yireo\CategoryExtended\Test\Integration\Plugin;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ResourceModel\Category as CategoryResource;
use Magento\Sitemap\Model\ResourceModel\Catalog\Category as SitemapCategory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Yireo\CategoryExtended\Model\CategoryExtended;
use Yireo\CategoryExtended\Test\Integration\Fixture\CreateCategoryExtendedFixtures;

/**
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 */
class SitemapCategoryTest extends TestCase
{
    use CreateCategoryExtendedFixtures;

    public function testSitemapExcludesCategoriesWithExtendedDisplayMode(): void
    {
        $page = $this->createCmsPage('ce-sitemap-page');
        $cmsPageCategory = $this->createCategory('CE Sitemap CMS', CategoryExtended::DM_CMS_PAGE, [
            'cms_page' => $page->getId(),
        ]);
        $customUrlCategory = $this->createCategory('CE Sitemap URL', CategoryExtended::DM_CUSTOM_URL, [
            'custom_url' => 'https://example.com/sitemap',
        ]);
        $normalCategory = $this->createCategory('CE Sitemap Normal');

        $objectManager = Bootstrap::getObjectManager();
        $storeId = (int)$objectManager->get(StoreManagerInterface::class)->getDefaultStoreView()->getId();
        $categories = $objectManager->create(SitemapCategory::class)->getCollection($storeId);

        $this->assertIsArray($categories);
        $this->assertArrayHasKey($normalCategory->getId(), $categories);
        $this->assertArrayNotHasKey($cmsPageCategory->getId(), $categories);
        $this->assertArrayNotHasKey($customUrlCategory->getId(), $categories);
    }

    public function testSitemapRespectsDisplayModeOfStoreView(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $storeId = (int)$objectManager->get(StoreManagerInterface::class)->getDefaultStoreView()->getId();

        $customUrlInStoreView = $this->createCategory('CE Sitemap Store URL');
        $this->saveDisplayModeForStore($customUrlInStoreView, $storeId, CategoryExtended::DM_CUSTOM_URL, [
            'custom_url' => 'https://example.com/store-view',
        ]);

        $productsInStoreView = $this->createCategory('CE Sitemap Store Products', CategoryExtended::DM_CUSTOM_URL, [
            'custom_url' => 'https://example.com/global',
        ]);
        $this->saveDisplayModeForStore($productsInStoreView, $storeId, Category::DM_PRODUCT);

        $categories = $objectManager->create(SitemapCategory::class)->getCollection($storeId);

        $this->assertIsArray($categories);
        $this->assertArrayNotHasKey($customUrlInStoreView->getId(), $categories);
        $this->assertArrayHasKey($productsInStoreView->getId(), $categories);
    }

    private function saveDisplayModeForStore(Category $category, int $storeId, string $displayMode, array $data = []): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $storeCategory = $objectManager->get(CategoryFactory::class)->create();
        $storeCategory->setStoreId($storeId);
        $objectManager->get(CategoryResource::class)->load($storeCategory, (int)$category->getId());
        $storeCategory->addData(array_merge(['display_mode' => $displayMode], $data));
        $objectManager->get(CategoryResource::class)->save($storeCategory);
    }
}
