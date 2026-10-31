<?php declare(strict_types=1);

namespace Yireo\CategoryExtended\Test\Integration\Observer;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Yireo\CategoryExtended\Model\CategoryExtended;
use Yireo\CategoryExtended\Test\Integration\Fixture\CreateCategoryExtendedFixtures;

/**
 * @magentoAppArea frontend
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 */
class CategoryCollectionTest extends TestCase
{
    use CreateCategoryExtendedFixtures;

    public function testCollectionLoadsExtendedAttributes(): void
    {
        $page = $this->createCmsPage('ce-collection-page');
        $cmsPageCategory = $this->createCategory('CE Collection CMS', CategoryExtended::DM_CMS_PAGE, [
            'cms_page' => $page->getId(),
        ]);
        $customUrlCategory = $this->createCategory('CE Collection URL', CategoryExtended::DM_CUSTOM_URL, [
            'custom_url' => 'https://example.com/collection',
        ]);

        $collection = Bootstrap::getObjectManager()->get(CollectionFactory::class)->create();
        $collection->addIdFilter([$cmsPageCategory->getId(), $customUrlCategory->getId()]);
        $collection->load();

        $loadedCmsPageCategory = $collection->getItemById($cmsPageCategory->getId());
        $this->assertNotNull($loadedCmsPageCategory);
        $this->assertSame(CategoryExtended::DM_CMS_PAGE, $loadedCmsPageCategory->getData('display_mode'));
        $this->assertSame((int)$page->getId(), (int)$loadedCmsPageCategory->getData('cms_page'));

        $loadedCustomUrlCategory = $collection->getItemById($customUrlCategory->getId());
        $this->assertNotNull($loadedCustomUrlCategory);
        $this->assertSame(CategoryExtended::DM_CUSTOM_URL, $loadedCustomUrlCategory->getData('display_mode'));
        $this->assertSame('https://example.com/collection', $loadedCustomUrlCategory->getData('custom_url'));
    }
}
