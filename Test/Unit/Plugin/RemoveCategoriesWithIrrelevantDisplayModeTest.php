<?php declare(strict_types=1);

namespace Yireo\CategoryExtended\Test\Unit\Plugin;

use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\DataObject;
use Magento\Sitemap\Model\ResourceModel\Catalog\Category as SitemapCategory;
use PHPUnit\Framework\TestCase;
use Yireo\CategoryExtended\Model\CategoryExtended;
use Yireo\CategoryExtended\Plugin\RemoveCategoriesWithIrrelevantDisplayMode;

class RemoveCategoriesWithIrrelevantDisplayModeTest extends TestCase
{
    public function testRemovesCategoriesWithExtendedDisplayMode(): void
    {
        $collection = $this->createMock(Collection::class);
        $collection->expects($this->once())
            ->method('addAttributeToFilter')
            ->with('display_mode', ['in' => [CategoryExtended::DM_CMS_PAGE, CategoryExtended::DM_CUSTOM_URL]])
            ->willReturnSelf();
        $collection->method('getAllIds')->willReturn(['4', '6']);

        $collectionFactory = $this->createMock(CollectionFactory::class);
        $collectionFactory->expects($this->once())->method('create')->willReturn($collection);

        $categories = [
            3 => new DataObject(['id' => 3]),
            4 => new DataObject(['id' => 4]),
            5 => new DataObject(['id' => 5]),
            6 => new DataObject(['id' => 6]),
        ];

        $plugin = new RemoveCategoriesWithIrrelevantDisplayMode($collectionFactory, new CategoryExtended());
        $result = $plugin->afterGetCollection($this->createMock(SitemapCategory::class), $categories, 1);

        $this->assertSame([3 => $categories[3], 5 => $categories[5]], $result);
    }

    public function testKeepsAllCategoriesWithoutExtendedDisplayMode(): void
    {
        $collection = $this->createMock(Collection::class);
        $collection->method('addAttributeToFilter')->willReturnSelf();
        $collection->method('getAllIds')->willReturn([]);

        $collectionFactory = $this->createMock(CollectionFactory::class);
        $collectionFactory->method('create')->willReturn($collection);

        $categories = [3 => new DataObject(['id' => 3]), 5 => new DataObject(['id' => 5])];

        $plugin = new RemoveCategoriesWithIrrelevantDisplayMode($collectionFactory, new CategoryExtended());

        $this->assertSame($categories, $plugin->afterGetCollection($this->createMock(SitemapCategory::class), $categories, 1));
    }
}
