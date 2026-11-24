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

    public function testRelativeUrlIsMadeAbsolute(): void
    {
        $urls = ['customer/account/login', 'about-us', 'ftp-page/http://'];
        $urlModel = $this->createMock(UrlInterface::class);
        $urlModel->expects($this->exactly(count($urls)))
            ->method('getUrl')
            ->willReturnCallback(fn (string $path) => 'https://shop.test/' . $path);
        $normalizeUrl = new NormalizeUrl($urlModel, new CustomUrlValidator());

        foreach ($urls as $url) {
            $this->assertSame('https://shop.test/' . $url, $normalizeUrl->normalize($url));
        }
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
