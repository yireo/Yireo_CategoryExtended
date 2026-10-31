<?php declare(strict_types=1);

namespace Yireo\CategoryExtended\Test\Unit\Util;

use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
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
        (new GetUrlByCmsPageId($this->createMock(UrlInterface::class), $repository))->get(0);
    }

    public function testNegativeIdThrowsException(): void
    {
        $repository = $this->createMock(PageRepositoryInterface::class);
        $repository->expects($this->never())->method('getById');

        $this->expectException(InvalidCmsPageId::class);
        (new GetUrlByCmsPageId($this->createMock(UrlInterface::class), $repository))->get(-1);
    }

    public function testMissingPageThrowsException(): void
    {
        $repository = $this->createMock(PageRepositoryInterface::class);
        $repository->method('getById')->with(42)->willThrowException(new NoSuchEntityException());

        $this->expectException(InvalidCmsPageId::class);
        (new GetUrlByCmsPageId($this->createMock(UrlInterface::class), $repository))->get(42);
    }

    public function testInactivePageThrowsException(): void
    {
        $page = $this->createMock(PageInterface::class);
        $page->method('isActive')->willReturn(false);
        $repository = $this->createMock(PageRepositoryInterface::class);
        $repository->method('getById')->with(42)->willReturn($page);

        $urlModel = $this->createMock(UrlInterface::class);
        $urlModel->expects($this->never())->method('getUrl');

        $this->expectException(InvalidCmsPageId::class);
        (new GetUrlByCmsPageId($urlModel, $repository))->get(42);
    }

    public function testActivePageReturnsUrlOfIdentifier(): void
    {
        $page = $this->createMock(PageInterface::class);
        $page->method('isActive')->willReturn(true);
        $page->method('getIdentifier')->willReturn('about-us');
        $repository = $this->createMock(PageRepositoryInterface::class);
        $repository->method('getById')->with(42)->willReturn($page);

        $urlModel = $this->createMock(UrlInterface::class);
        $urlModel->expects($this->once())
            ->method('getUrl')
            ->with('about-us')
            ->willReturn('https://shop.test/about-us');

        $this->assertSame('https://shop.test/about-us', (new GetUrlByCmsPageId($urlModel, $repository))->get(42));
    }
}
