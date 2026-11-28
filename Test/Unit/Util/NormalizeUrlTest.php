<?php declare(strict_types=1);

namespace Yireo\CategoryExtended\Test\Unit\Util;

use Magento\Framework\UrlInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Yireo\CategoryExtended\Exception\InvalidCustomUrl;
use Yireo\CategoryExtended\Util\CustomUrlValidator;
use Yireo\CategoryExtended\Util\NormalizeUrl;

class NormalizeUrlTest extends TestCase
{
    public function testAbsoluteUrlIsReturnedUnchanged(): void
    {
        $urlModel = $this->createMock(UrlInterface::class);
        $urlModel->expects($this->never())->method('getUrl');
        $normalizeUrl = new NormalizeUrl($urlModel, new CustomUrlValidator());

        foreach (['http://example.com/', 'https://example.com/category-extended', 'HTTPS://example.com/upper'] as $url) {
            $this->assertSame($url, $normalizeUrl->normalize($url));
        }
    }

    /**
     * @dataProvider getRelativeUrls
     */
    #[DataProvider('getRelativeUrls')]
    public function testRelativeUrlIsMadeAbsoluteAsDirectPath(string $url, string $expectedDirectPath): void
    {
        $urlModel = $this->createMock(UrlInterface::class);
        $urlModel->expects($this->once())
            ->method('getUrl')
            ->with(null, ['_direct' => $expectedDirectPath])
            ->willReturn('https://shop.test/' . $expectedDirectPath);

        $normalizeUrl = new NormalizeUrl($urlModel, new CustomUrlValidator());

        $this->assertSame('https://shop.test/' . $expectedDirectPath, $normalizeUrl->normalize($url));
    }

    public static function getRelativeUrls(): array
    {
        return [
            ['customer/account/login', 'customer/account/login'],
            ['about-us', 'about-us'],
            ['/about-us', 'about-us'],
            ['about-us.html?foo=bar', 'about-us.html?foo=bar'],
            ['about-us.html?foo=bar#top', 'about-us.html?foo=bar#top'],
            ['page#anchor', 'page#anchor'],
            ['company/about/team/history', 'company/about/team/history'],
            ['ftp-page/http://', 'ftp-page/http://'],
        ];
    }

    /**
     * @dataProvider getUnsafeUrls
     */
    #[DataProvider('getUnsafeUrls')]
    public function testUnsafeUrlThrowsException(string $url): void
    {
        $urlModel = $this->createMock(UrlInterface::class);
        $urlModel->expects($this->never())->method('getUrl');

        $this->expectException(InvalidCustomUrl::class);
        (new NormalizeUrl($urlModel, new CustomUrlValidator()))->normalize($url);
    }

    public static function getUnsafeUrls(): array
    {
        return [
            ['javascript://example.com/%0aalert(document.domain)'],
            ['https://evil.example/" onmouseover="alert(1)'],
            ['foo" onmouseover="alert(1)'],
            ['mailto:a@b.c'],
            ['//evil.example/x'],
        ];
    }
}
