<?php declare(strict_types=1);

namespace Yireo\CategoryExtended\Test\Unit\Plugin;

use Magento\Catalog\Helper\Category as CategoryHelper;
use Magento\Framework\DataObject;
use Magento\Framework\UrlInterface;
use PHPUnit\Framework\MockObject\Rule\InvocationOrder;
use PHPUnit\Framework\TestCase;
use Yireo\CategoryExtended\Model\CategoryExtended;
use Yireo\CategoryExtended\Plugin\ModifyCategoryUrlIfDisplayModeIsCustomUrl;
use Yireo\CategoryExtended\Util\NormalizeUrl;

class ModifyCategoryUrlIfDisplayModeIsCustomUrlTest extends TestCase
{
    private const ORIGINAL_URL = 'https://shop.test/gear.html';

    public function testOtherDisplayModeReturnsOriginalUrl(): void
    {
        $category = new DataObject(['display_mode' => 'PRODUCTS', 'custom_url' => 'https://example.com/']);

        $this->assertSame(self::ORIGINAL_URL, $this->getUrl($category, $this->never()));
    }

    public function testEmptyCustomUrlReturnsOriginalUrl(): void
    {
        foreach ([null, '', '   '] as $customUrl) {
            $category = new DataObject(['display_mode' => CategoryExtended::DM_CUSTOM_URL, 'custom_url' => $customUrl]);

            $this->assertSame(self::ORIGINAL_URL, $this->getUrl($category, $this->never()));
        }
    }

    public function testAbsoluteCustomUrlIsReturnedTrimmed(): void
    {
        $category = new DataObject([
            'display_mode' => CategoryExtended::DM_CUSTOM_URL,
            'custom_url' => ' https://example.com/foo ',
        ]);

        $this->assertSame('https://example.com/foo', $this->getUrl($category, $this->never()));
    }

    public function testRelativeCustomUrlIsNormalized(): void
    {
        $category = new DataObject([
            'display_mode' => CategoryExtended::DM_CUSTOM_URL,
            'custom_url' => 'customer/account/login',
        ]);

        $this->assertSame('https://shop.test/customer/account/login', $this->getUrl($category, $this->once()));
    }

    private function getUrl(DataObject $category, InvocationOrder $getUrlExpectation): string
    {
        $urlModel = $this->createMock(UrlInterface::class);
        $urlModel->expects($getUrlExpectation)
            ->method('getUrl')
            ->willReturnCallback(fn (string $path) => 'https://shop.test/' . $path);

        $plugin = new ModifyCategoryUrlIfDisplayModeIsCustomUrl(new NormalizeUrl($urlModel));

        return $plugin->afterGetCategoryUrl($this->createMock(CategoryHelper::class), self::ORIGINAL_URL, $category);
    }
}
