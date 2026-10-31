<?php declare(strict_types=1);

namespace Yireo\CategoryExtended\Test\Unit\Model\Category\Attribute\Source;

use Magento\Cms\Helper\Page as PageHelper;
use Magento\Cms\Model\Page;
use Magento\Cms\Model\PageFactory;
use Magento\Framework\DataObject;
use PHPUnit\Framework\TestCase;
use Yireo\CategoryExtended\Model\Category\Attribute\Source\CmsPage;

class CmsPageTest extends TestCase
{
    public function testGetAllOptionsStartsWithEmptyOptionFollowedByPages(): void
    {
        $page = $this->createMock(Page::class);
        $page->method('getCollection')->willReturn([
            new DataObject(['id' => 1, 'title' => 'Home page']),
            new DataObject(['id' => 2, 'title' => 'About us']),
        ]);

        $pageFactory = $this->createMock(PageFactory::class);
        $pageFactory->method('create')->willReturn($page);

        $options = (new CmsPage($pageFactory, $this->createMock(PageHelper::class)))->getAllOptions();

        $this->assertCount(3, $options);
        $this->assertSame('', $options[0]['value']);
        $this->assertSame('Please select a static page.', (string)$options[0]['label']);
        $this->assertSame(['value' => 1, 'label' => 'Home page'], $options[1]);
        $this->assertSame(['value' => 2, 'label' => 'About us'], $options[2]);
    }

    public function testGetAllOptionsWithoutPagesOnlyReturnsEmptyOption(): void
    {
        $page = $this->createMock(Page::class);
        $page->method('getCollection')->willReturn([]);

        $pageFactory = $this->createMock(PageFactory::class);
        $pageFactory->method('create')->willReturn($page);

        $options = (new CmsPage($pageFactory, $this->createMock(PageHelper::class)))->getAllOptions();

        $this->assertCount(1, $options);
        $this->assertSame('', $options[0]['value']);
    }
}
