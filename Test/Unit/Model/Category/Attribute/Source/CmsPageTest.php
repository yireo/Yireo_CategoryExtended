<?php declare(strict_types=1);

namespace Yireo\CategoryExtended\Test\Unit\Model\Category\Attribute\Source;

use ArrayIterator;
use Magento\Cms\Model\ResourceModel\Page\Collection;
use Magento\Cms\Model\ResourceModel\Page\CollectionFactory;
use Magento\Framework\DataObject;
use PHPUnit\Framework\TestCase;
use Yireo\CategoryExtended\Model\Category\Attribute\Source\CmsPage;

class CmsPageTest extends TestCase
{
    public function testGetAllOptionsStartsWithEmptyOptionFollowedByPages(): void
    {
        $options = $this->createSource([
            new DataObject(['id' => 1, 'title' => 'Home page']),
            new DataObject(['id' => 2, 'title' => 'About us']),
        ])->getAllOptions();

        $this->assertCount(3, $options);
        $this->assertSame('', $options[0]['value']);
        $this->assertSame('Please select a static page.', (string)$options[0]['label']);
        $this->assertSame(['value' => 1, 'label' => 'Home page'], $options[1]);
        $this->assertSame(['value' => 2, 'label' => 'About us'], $options[2]);
    }

    public function testGetAllOptionsWithoutEmptyOption(): void
    {
        $options = $this->createSource([new DataObject(['id' => 1, 'title' => 'Home page'])])->getAllOptions(false);

        $this->assertSame([['value' => 1, 'label' => 'Home page']], $options);
    }

    public function testGetAllOptionsWithoutPagesOnlyReturnsEmptyOption(): void
    {
        $options = $this->createSource([])->getAllOptions();

        $this->assertCount(1, $options);
        $this->assertSame('', $options[0]['value']);
    }

    private function createSource(array $pages): CmsPage
    {
        $collection = $this->createMock(Collection::class);
        $collection->expects($this->once())->method('addFieldToSelect')->with(['page_id', 'title'])->willReturnSelf();
        $collection->method('getIterator')->willReturn(new ArrayIterator($pages));

        $collectionFactory = $this->createMock(CollectionFactory::class);
        $collectionFactory->method('create')->willReturn($collection);

        return new CmsPage($collectionFactory);
    }
}
