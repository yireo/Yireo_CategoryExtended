<?php declare(strict_types=1);

namespace Yireo\CategoryExtended\Test\Unit\Observer;

use Magento\Framework\App\Response\Http;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use PHPUnit\Framework\TestCase;
use Yireo\CategoryExtended\Exception\InvalidCmsPageId;
use Yireo\CategoryExtended\Model\CategoryExtended;
use Yireo\CategoryExtended\Observer\RedirectCategoryRequestIfDisplayModeIsCmsPage;
use Yireo\CategoryExtended\Util\GetUrlByCmsPageId;

class RedirectCategoryRequestIfDisplayModeIsCmsPageTest extends TestCase
{
    public function testOtherDisplayModeDoesNotRedirect(): void
    {
        $responseFactory = $this->createMock(ResponseFactory::class);
        $responseFactory->expects($this->never())->method('create');
        $getUrlByCmsPageId = $this->createMock(GetUrlByCmsPageId::class);
        $getUrlByCmsPageId->expects($this->never())->method('get');

        (new RedirectCategoryRequestIfDisplayModeIsCmsPage($responseFactory, $getUrlByCmsPageId))
            ->execute($this->createObserver(['display_mode' => 'PRODUCTS', 'cms_page' => 42]));
    }

    public function testInvalidCmsPageDoesNotRedirect(): void
    {
        $responseFactory = $this->createMock(ResponseFactory::class);
        $responseFactory->expects($this->never())->method('create');
        $getUrlByCmsPageId = $this->createMock(GetUrlByCmsPageId::class);
        $getUrlByCmsPageId->method('get')->with(42)->willThrowException(new InvalidCmsPageId('CMS Page not found'));

        (new RedirectCategoryRequestIfDisplayModeIsCmsPage($responseFactory, $getUrlByCmsPageId))
            ->execute($this->createObserver(['display_mode' => CategoryExtended::DM_CMS_PAGE, 'cms_page' => '42']));
    }

    public function testValidCmsPageRedirects(): void
    {
        $response = $this->createMock(Http::class);
        $response->expects($this->once())->method('setRedirect')->with('https://shop.test/about-us')->willReturnSelf();
        $response->expects($this->once())->method('sendResponse');

        $responseFactory = $this->createMock(ResponseFactory::class);
        $responseFactory->expects($this->once())->method('create')->willReturn($response);
        $getUrlByCmsPageId = $this->createMock(GetUrlByCmsPageId::class);
        $getUrlByCmsPageId->method('get')->with(42)->willReturn('https://shop.test/about-us');

        (new RedirectCategoryRequestIfDisplayModeIsCmsPage($responseFactory, $getUrlByCmsPageId))
            ->execute($this->createObserver(['display_mode' => CategoryExtended::DM_CMS_PAGE, 'cms_page' => '42']));
    }

    private function createObserver(array $categoryData): Observer
    {
        return new Observer(['event' => new Event(['category' => new DataObject($categoryData)])]);
    }
}
