<?php declare(strict_types=1);

namespace Yireo\CategoryExtended\Test\Unit\Util;

use Magento\Framework\UrlInterface;
use PHPUnit\Framework\TestCase;
use Yireo\CategoryExtended\Util\NormalizeUrl;

class NormalizeUrlTest extends TestCase
{
    public function testAbsoluteUrlIsReturnedUnchanged(): void
    {
        $urlModel = $this->createMock(UrlInterface::class);
        $urlModel->expects($this->never())->method('getUrl');
        $normalizeUrl = new NormalizeUrl($urlModel);

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
        $normalizeUrl = new NormalizeUrl($urlModel);

        foreach ($urls as $url) {
            $this->assertSame('https://shop.test/' . $url, $normalizeUrl->normalize($url));
        }
    }
}
