<?php declare(strict_types=1);

namespace Yireo\CategoryExtended\Test\Unit\Util;

use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Cms\Model\Page;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\TestCase;
use Yireo\CategoryExtended\Exception\InvalidCmsPageId;
use Yireo\CategoryExtended\Util\GetUrlByCmsPageId;

class GetUrlByCmsPageIdTest extends TestCase
{
    public function testZeroIdThrowsException(): void
    {
        $repository = $this->createMock(PageRepositoryInterface::class);
        $repository->expects($this->never())->method('getById');

        $this->expectException(InvalidCmsPageId::class);
        $this->create($this->createMock(UrlInterface::class), $repository)->get(0);
    }

    public function testNegativeIdThrowsException(): void
    {
        $repository = $this->createMock(PageRepositoryInterface::class);
        $repository->expects($this->never())->method('getById');

        $this->expectException(InvalidCmsPageId::class);
        $this->create($this->createMock(UrlInterface::class), $repository)->get(-1);
    }

    public function testMissingPageThrowsException(): void
    {
        $repository = $this->createMock(PageRepositoryInterface::class);
        $repository->method('getById')->with(42)->willThrowException(new NoSuchEntityException());

        $this->expectException(InvalidCmsPageId::class);
        $this->create($this->createMock(UrlInterface::class), $repository)->get(42);
    }

    public function testInactivePageThrowsException(): void
    {
        $urlModel = $this->createMock(UrlInterface::class);
        $urlModel->expects($this->never())->method('getUrl');

        $this->expectException(InvalidCmsPageId::class);
        $this->create($urlModel, $this->createRepository(false, [0]))->get(42);
    }

    public function testPageOfOtherStoreThrowsException(): void
    {
        $urlModel = $this->createMock(UrlInterface::class);
        $urlModel->expects($this->never())->method('getUrl');

        $this->expectException(InvalidCmsPageId::class);
        $this->create($urlModel, $this->createRepository(true, ['2', '3']), 1)->get(42);
    }

    public function testActivePageOfAllStoreViewsReturnsUrlOfIdentifier(): void
    {
        $this->assertSame(
            'https://shop.test/about-us',
            $this->create($this->createUrlModel(), $this->createRepository(true, ['0']), 1)->get(42)
        );
    }

    public function testActivePageOfCurrentStoreReturnsUrlOfIdentifier(): void
    {
        $this->assertSame(
            'https://shop.test/about-us',
            $this->create($this->createUrlModel(), $this->createRepository(true, ['2', '1']), 1)->get(42)
        );
    }

    private function createUrlModel(): UrlInterface
    {
        $urlModel = $this->createMock(UrlInterface::class);
        $urlModel->expects($this->once())
            ->method('getUrl')
            ->with('about-us')
            ->willReturn('https://shop.test/about-us');

        return $urlModel;
    }

    private function createRepository(bool $active, array $storeIds): PageRepositoryInterface
    {
        $page = $this->createMock(Page::class);
        $page->method('isActive')->willReturn($active);
        $page->method('getStores')->willReturn($storeIds);
        $page->method('getIdentifier')->willReturn('about-us');

        $repository = $this->createMock(PageRepositoryInterface::class);
        $repository->method('getById')->with(42)->willReturn($page);

        return $repository;
    }

    private function create(
        UrlInterface $urlModel,
        PageRepositoryInterface $repository,
        int $currentStoreId = 1
    ): GetUrlByCmsPageId {
        $store = $this->createMock(StoreInterface::class);
        $store->method('getId')->willReturn($currentStoreId);
        $storeManager = $this->createMock(StoreManagerInterface::class);
        $storeManager->method('getStore')->willReturn($store);

        return new GetUrlByCmsPageId($urlModel, $repository, $storeManager);
    }
}
