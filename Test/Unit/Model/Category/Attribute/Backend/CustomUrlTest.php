<?php declare(strict_types=1);

namespace Yireo\CategoryExtended\Test\Unit\Model\Category\Attribute\Backend;

use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Eav\Model\Entity\Attribute\Exception as AttributeException;
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

    public function testExceptionContainsAttributeCode(): void
    {
        try {
            $this->createBackend()->beforeSave(new DataObject(['custom_url' => 'foo" onmouseover="alert(1)']));
            $this->fail('Expected an attribute exception');
        } catch (AttributeException $exception) {
            $this->assertSame('custom_url', $exception->getAttributeCode());
        }
    }

    public function testValueIsReadFromAttributeCodeOfAttribute(): void
    {
        $backend = $this->createBackend('other_url');
        $category = new DataObject(['other_url' => ' about-us ', 'custom_url' => 'javascript:alert(1)']);

        $this->assertTrue($backend->validate($category));
        $this->assertSame('about-us', $category->getData('other_url'));
        $this->assertSame('javascript:alert(1)', $category->getData('custom_url'));

        $this->expectException(AttributeException::class);
        $backend->validate(new DataObject(['other_url' => 'javascript:alert(1)']));
    }

    private function createBackend(string $attributeCode = 'custom_url'): CustomUrl
    {
        $attribute = $this->createMock(AbstractAttribute::class);
        $attribute->method('getAttributeCode')->willReturn($attributeCode);
        $attribute->method('getIsRequired')->willReturn(false);
        $attribute->method('getIsUnique')->willReturn(false);

        $backend = new CustomUrl(new CustomUrlValidator());
        $backend->setAttribute($attribute);

        return $backend;
    }
}
