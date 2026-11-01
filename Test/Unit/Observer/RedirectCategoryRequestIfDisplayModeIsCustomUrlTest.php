<?php declare(strict_types=1);

namespace Yireo\CategoryExtended\Test\Unit\Observer;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Response\Http;
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
        $action = $this->createNonRedirectingAction();

        $this->createObserverInstance()
            ->execute($this->createObserver(['display_mode' => 'PRODUCTS', 'custom_url' => 'https://example.com/'], $action));
    }

    public function testEmptyCustomUrlDoesNotRedirect(): void
    {
        $action = $this->createNonRedirectingAction();
        $observer = $this->createObserverInstance();

        foreach ([null, '', '  '] as $customUrl) {
            $observer->execute($this->createObserver([
                'display_mode' => CategoryExtended::DM_CUSTOM_URL,
                'custom_url' => $customUrl,
            ], $action));
        }
    }

    public function testAbsoluteCustomUrlRedirects(): void
    {
        $this->createObserverInstance()->execute($this->createObserver([
            'display_mode' => CategoryExtended::DM_CUSTOM_URL,
            'custom_url' => ' https://example.com/foo ',
        ], $this->createRedirectingAction('https://example.com/foo')));
    }

    public function testRelativeCustomUrlRedirectsToNormalizedUrl(): void
    {
        $this->createObserverInstance()->execute($this->createObserver([
            'display_mode' => CategoryExtended::DM_CUSTOM_URL,
            'custom_url' => 'customer/account/login',
        ], $this->createRedirectingAction('https://shop.test/customer/account/login')));
    }

    private function createNonRedirectingAction(): Action
    {
        $action = $this->createMock(Action::class);
        $action->expects($this->never())->method('getResponse');

        return $action;
    }

    private function createRedirectingAction(string $expectedUrl): Action
    {
        $response = $this->createMock(Http::class);
        $response->expects($this->once())->method('setRedirect')->with($expectedUrl)->willReturnSelf();
        $response->expects($this->never())->method('sendResponse');

        $action = $this->createMock(Action::class);
        $action->method('getResponse')->willReturn($response);

        return $action;
    }

    private function createObserverInstance(): RedirectCategoryRequestIfDisplayModeIsCustomUrl
    {
        $urlModel = $this->createMock(UrlInterface::class);
        $urlModel->method('getUrl')->willReturnCallback(fn (string $path) => 'https://shop.test/' . $path);

        return new RedirectCategoryRequestIfDisplayModeIsCustomUrl(new NormalizeUrl($urlModel));
    }

    private function createObserver(array $categoryData, Action $action): Observer
    {
        return new Observer(['event' => new Event([
            'category' => new DataObject($categoryData),
            'controller_action' => $action,
        ])]);
    }
}
