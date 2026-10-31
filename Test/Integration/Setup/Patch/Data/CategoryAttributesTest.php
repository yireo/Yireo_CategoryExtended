<?php declare(strict_types=1);

namespace Yireo\CategoryExtended\Test\Integration\Setup\Patch\Data;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Yireo\CategoryExtended\Model\Category\Attribute\Source\CmsPage;

class CategoryAttributesTest extends TestCase
{
    public function testCmsPageAttributeExists(): void
    {
        $attribute = $this->getAttribute('cms_page');

        $this->assertSame('int', $attribute->getBackendType());
        $this->assertSame('select', $attribute->getFrontendInput());
        $this->assertSame(CmsPage::class, $attribute->getSourceModel());
        $this->assertSame(ScopedAttributeInterface::SCOPE_STORE, (int)$attribute->getIsGlobal());
        $this->assertFalse((bool)$attribute->getIsRequired());
    }

    public function testCustomUrlAttributeExists(): void
    {
        $attribute = $this->getAttribute('custom_url');

        $this->assertSame('varchar', $attribute->getBackendType());
        $this->assertSame('text', $attribute->getFrontendInput());
        $this->assertSame(ScopedAttributeInterface::SCOPE_STORE, (int)$attribute->getIsGlobal());
        $this->assertFalse((bool)$attribute->getIsRequired());
    }

    private function getAttribute(string $attributeCode): Attribute
    {
        $eavConfig = Bootstrap::getObjectManager()->get(EavConfig::class);
        $attribute = $eavConfig->getAttribute(Category::ENTITY, $attributeCode);
        $this->assertInstanceOf(Attribute::class, $attribute);
        $this->assertNotEmpty($attribute->getId(), 'Attribute ' . $attributeCode . ' does not exist');

        return $attribute;
    }
}
