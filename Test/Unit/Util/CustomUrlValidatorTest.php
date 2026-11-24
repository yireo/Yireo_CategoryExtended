<?php declare(strict_types=1);

namespace Yireo\CategoryExtended\Test\Unit\Util;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Yireo\CategoryExtended\Util\CustomUrlValidator;

class CustomUrlValidatorTest extends TestCase
{
    /**
     * @dataProvider getValidUrls
     */
    #[DataProvider('getValidUrls')]
    public function testValidUrlIsAccepted(string $url): void
    {
        $this->assertTrue((new CustomUrlValidator())->isValid($url));
    }

    /**
     * @dataProvider getInvalidUrls
     */
    #[DataProvider('getInvalidUrls')]
    public function testInvalidUrlIsRejected(string $url): void
    {
        $this->assertFalse((new CustomUrlValidator())->isValid($url));
    }

    public static function getValidUrls(): array
    {
        return [
            ['http://example.com/'],
            ['https://example.com/category-extended'],
            ['HTTPS://example.com/upper'],
            ['https://example.com:8443/path?query=1&b=2#anchor'],
            ['https://example.com/%0a-encoded'],
            ['https://user@example.com/'],
            ['https://[2001:db8::1]/path'],
            ['https://[2001:db8::1]:8443/'],
            ['https://example.com/caf%C3%A9'],
            ['café'],
            ['customer/account/login'],
            ['about-us'],
            ['/about-us'],
            ['about-us.html?foo=bar'],
            ['ftp-page/http://'],
        ];
    }

    public static function getInvalidUrls(): array
    {
        return [
            [''],
            ['javascript:alert(1)'],
            ['JaVaScRiPt:alert(document.domain)//'],
            ['javascript://example.com/%0aalert(document.domain)'],
            ['data:text/html,alert(1)'],
            ['data://x.y/text/html'],
            ['vbscript://x.y/a'],
            ['mailto:a@b.c'],
            ['tel:+31612345678'],
            ['ftp://example.com/'],
            ['https:example.com'],
            ['https:/example.com'],
            ['https:///example.com'],
            ['https://:8443/path'],
            ['https://user@/path'],
            ['https://example.com:123456/'],
            ['https://[evil.example/'],
            ["https://example.com/\u{00A0}x"],
            ["about\u{2028}us"],
            ["about\u{200B}us"],
            ["about-us\xC2"],
            ['//evil.example/x'],
            ['\\\\evil.example'],
            ['/\\evil.example'],
            ['https://evil.example/" onmouseover="alert(1)'],
            ["http://x.y/'><img src=x onerror=alert(1)>"],
            ['https://evil.example/<script>alert(1)</script>'],
            ['foo" onmouseover="alert(1)'],
            ['foo`bar'],
            ['about us'],
            ["https://x.example/\r\nSet-Cookie: a=b"],
            ["customer/account\nlogin"],
            ["about-us\x00"],
            ["about-us\x7F"],
        ];
    }
}
