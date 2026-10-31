<?php declare(strict_types=1);

namespace Yireo\CategoryExtended\Test\Unit\Observer;

use Magento\Framework\App\Response\Http;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use Magento\Framework\UrlInterface;
use PHPUnit\Framework\TestCase;
use Yireo\CategoryExtended\Model\CategoryExtended;
use Yireo\CategoryExtended\Observer\RedirectCategoryRequestIfDisplayModeIsCustomUrl;
use Yireo\CategoryExtended\Util\NormalizeUrl;

class RedirectCategoryRequestIfDisplayModeIsCustomUrlTest extends TestCase
{
    public function testOtherDisplayModeDoesNotRedirect(): void
    {
        $responseFactory = $this->createMock(ResponseFactory::class);
        $responseFactory->expects($this->never())->method('create');

        $this->createObserverInstance($responseFactory)
            ->execute($this->createObserver(['display_mode' => 'PRODUCTS', 'custom_url' => 'https://example.com/']));
    }

    public function testEmptyCustomUrlDoesNotRedirect(): void
    {
        $responseFactory = $this->createMock(ResponseFactory::class);
        $responseFactory->expects($this->never())->method('create');
        $observer = $this->createObserverInstance($responseFactory);

        foreach ([null, '', '  '] as $customUrl) {
            $observer->execute($this->createObserver([
                'display_mode' => CategoryExtended::DM_CUSTOM_URL,
                'custom_url' => $customUrl,
            ]));
        }
    }

    public function testAbsoluteCustomUrlRedirects(): void
    {
        $responseFactory = $this->createResponseFactory('https://example.com/foo');

        $this->createObserverInstance($responseFactory)->execute($this->createObserver([
            'display_mode' => CategoryExtended::DM_CUSTOM_URL,
            'custom_url' => ' https://example.com/foo ',
        ]));
    }

    public function testRelativeCustomUrlRedirectsToNormalizedUrl(): void
    {
        $responseFactory = $this->createResponseFactory('https://shop.test/customer/account/login');

        $this->createObserverInstance($responseFactory)->execute($this->createObserver([
            'display_mode' => CategoryExtended::DM_CUSTOM_URL,
            'custom_url' => 'customer/account/login',
        ]));
    }

    private function createResponseFactory(string $expectedUrl): ResponseFactory
    {
        $response = $this->createMock(Http::class);
        $response->expects($this->once())->method('setRedirect')->with($expectedUrl)->willReturnSelf();
        $response->expects($this->once())->method('sendResponse');

        $responseFactory = $this->createMock(ResponseFactory::class);
        $responseFactory->expects($this->once())->method('create')->willReturn($response);

        return $responseFactory;
    }

    private function createObserverInstance(ResponseFactory $responseFactory): RedirectCategoryRequestIfDisplayModeIsCustomUrl
    {
        $urlModel = $this->createMock(UrlInterface::class);
        $urlModel->method('getUrl')->willReturnCallback(fn (string $path) => 'https://shop.test/' . $path);

        return new RedirectCategoryRequestIfDisplayModeIsCustomUrl($responseFactory, new NormalizeUrl($urlModel));
    }

    private function createObserver(array $categoryData): Observer
    {
        return new Observer(['event' => new Event(['category' => new DataObject($categoryData)])]);
    }
}
