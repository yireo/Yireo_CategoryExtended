<?php declare(strict_types=1);

namespace Yireo\CategoryExtended\Test\Integration\Plugin;

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
}
