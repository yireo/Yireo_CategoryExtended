<?php declare(strict_types=1);

namespace Yireo\CategoryExtended\Test\Unit\Observer;

use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use PHPUnit\Framework\TestCase;
use Yireo\CategoryExtended\Observer\AddCustomUrlAttributeToCategoryCollectionBeforeLoad;

class AddCustomUrlAttributeToCategoryCollectionBeforeLoadTest extends TestCase
{
    public function testAddsDisplayModeAndCustomUrlAttributesToSelect(): void
    {
        $attributes = [];
        $collection = $this->createMock(Collection::class);
        $collection->expects($this->exactly(2))
            ->method('addAttributeToSelect')
            ->willReturnCallback(function (string $attribute) use (&$attributes, $collection) {
                $attributes[] = $attribute;
                return $collection;
            });

        (new AddCustomUrlAttributeToCategoryCollectionBeforeLoad())
            ->execute(new Observer(['event' => new Event(['category_collection' => $collection])]));

        $this->assertSame(['display_mode', 'custom_url'], $attributes);
    }
}
