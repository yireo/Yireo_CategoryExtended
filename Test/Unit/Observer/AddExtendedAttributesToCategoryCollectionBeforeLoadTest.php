<?php declare(strict_types=1);

namespace Yireo\CategoryExtended\Test\Unit\Observer;

use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use PHPUnit\Framework\TestCase;
use Yireo\CategoryExtended\Observer\AddExtendedAttributesToCategoryCollectionBeforeLoad;

class AddExtendedAttributesToCategoryCollectionBeforeLoadTest extends TestCase
{
    public function testAddsExtendedAttributesToSelect(): void
    {
        $collection = $this->createMock(Collection::class);
        $collection->expects($this->once())
            ->method('addAttributeToSelect')
            ->with(['display_mode', 'cms_page', 'custom_url'])
            ->willReturnSelf();

        (new AddExtendedAttributesToCategoryCollectionBeforeLoad())->execute(
            new Observer(['event' => new Event(['category_collection' => $collection])])
        );
    }
}
