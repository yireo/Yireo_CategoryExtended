<?php declare(strict_types=1);

namespace Yireo\CategoryExtended\Test\Unit\Plugin;

use Magento\Catalog\Helper\Category as CategoryHelper;
use Magento\Framework\DataObject;
use PHPUnit\Framework\TestCase;
use Yireo\CategoryExtended\Exception\InvalidCmsPageId;
use Yireo\CategoryExtended\Model\CategoryExtended;
use Yireo\CategoryExtended\Plugin\ModifyCategoryUrlIfDisplayModeIsCmsPage;
use Yireo\CategoryExtended\Util\GetUrlByCmsPageId;

class ModifyCategoryUrlIfDisplayModeIsCmsPageTest extends TestCase
{
    private const ORIGINAL_URL = 'https://shop.test/gear.html';

    public function testOtherDisplayModeReturnsOriginalUrl(): void
    {
        $getUrlByCmsPageId = $this->createMock(GetUrlByCmsPageId::class);
        $getUrlByCmsPageId->expects($this->never())->method('get');

        $category = new DataObject(['display_mode' => 'PRODUCTS', 'cms_page' => 42]);

        $this->assertSame(self::ORIGINAL_URL, $this->getUrl($getUrlByCmsPageId, $category));
    }

    public function testInvalidCmsPageReturnsOriginalUrl(): void
    {
        $getUrlByCmsPageId = $this->createMock(GetUrlByCmsPageId::class);
        $getUrlByCmsPageId->method('get')->with(42)->willThrowException(new InvalidCmsPageId('CMS Page is inactive'));

        $category = new DataObject(['display_mode' => CategoryExtended::DM_CMS_PAGE, 'cms_page' => '42']);

        $this->assertSame(self::ORIGINAL_URL, $this->getUrl($getUrlByCmsPageId, $category));
    }

    public function testMissingCmsPageIdIsPassedAsZero(): void
    {
        $getUrlByCmsPageId = $this->createMock(GetUrlByCmsPageId::class);
        $getUrlByCmsPageId->expects($this->once())
            ->method('get')
            ->with(0)
            ->willThrowException(new InvalidCmsPageId('Empty CMS Page ID'));

        $category = new DataObject(['display_mode' => CategoryExtended::DM_CMS_PAGE]);

        $this->assertSame(self::ORIGINAL_URL, $this->getUrl($getUrlByCmsPageId, $category));
    }

    public function testValidCmsPageReturnsPageUrl(): void
    {
        $getUrlByCmsPageId = $this->createMock(GetUrlByCmsPageId::class);
        $getUrlByCmsPageId->method('get')->with(42)->willReturn('https://shop.test/about-us');

        $category = new DataObject(['display_mode' => CategoryExtended::DM_CMS_PAGE, 'cms_page' => '42']);

        $this->assertSame('https://shop.test/about-us', $this->getUrl($getUrlByCmsPageId, $category));
    }

    private function getUrl(GetUrlByCmsPageId $getUrlByCmsPageId, DataObject $category): string
    {
        $plugin = new ModifyCategoryUrlIfDisplayModeIsCmsPage($getUrlByCmsPageId);

        return $plugin->afterGetCategoryUrl($this->createMock(CategoryHelper::class), self::ORIGINAL_URL, $category);
    }
}
