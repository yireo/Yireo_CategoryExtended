<?php declare(strict_types=1);

namespace Yireo\CategoryExtended\Test\Unit\Model\Category\Attribute\Backend;

use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use PHPUnit\Framework\TestCase;
use Yireo\CategoryExtended\Model\Category\Attribute\Backend\CustomUrl;
use Yireo\CategoryExtended\Util\CustomUrlValidator;

class CustomUrlTest extends TestCase
{
    public function testEmptyAndValidValuesAreAccepted(): void
    {
        $backend = $this->createBackend();

        foreach ([null, '', '  ', 'https://example.com/foo', 'customer/account/login', ' about-us '] as $value) {
            $category = new DataObject(['custom_url' => $value]);
            $this->assertTrue($backend->validate($category));
            $this->assertSame($backend, $backend->beforeSave($category));
        }
    }

    public function testValueIsTrimmedBeforeSave(): void
    {
        $category = new DataObject(['custom_url' => " https://example.com/foo\n"]);
        $this->createBackend()->beforeSave($category);

        $this->assertSame('https://example.com/foo', $category->getData('custom_url'));
    }

    public function testInvalidValueFailsValidation(): void
    {
        $this->expectException(LocalizedException::class);
        $this->createBackend()->validate(new DataObject(['custom_url' => 'javascript://x.y/%0aalert(1)']));
    }

    public function testInvalidValueFailsSave(): void
    {
        $this->expectException(LocalizedException::class);
        $this->createBackend()->beforeSave(new DataObject(['custom_url' => "https://x.example/\r\nSet-Cookie: a=b"]));
    }

    private function createBackend(): CustomUrl
    {
        $attribute = $this->createMock(AbstractAttribute::class);
        $attribute->method('getAttributeCode')->willReturn('custom_url');
        $attribute->method('getIsRequired')->willReturn(false);
        $attribute->method('getIsUnique')->willReturn(false);

        $backend = new CustomUrl(new CustomUrlValidator());
        $backend->setAttribute($attribute);

        return $backend;
    }
}
