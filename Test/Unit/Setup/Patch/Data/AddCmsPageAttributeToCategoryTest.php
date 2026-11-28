<?php declare(strict_types=1);

namespace Yireo\CategoryExtended\Test\Unit\Setup\Patch\Data;

use Magento\Catalog\Model\Category;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use PHPUnit\Framework\TestCase;
use Yireo\CategoryExtended\Model\Category\Attribute\Source\CmsPage;
use Yireo\CategoryExtended\Setup\Patch\Data\AddCmsPageAttributeToCategory;

class AddCmsPageAttributeToCategoryTest extends TestCase
{
    public function testApplyAddsStoreScopedCmsPageSelect(): void
    {
        $eavSetup = $this->createMock(EavSetup::class);
        $eavSetup->expects($this->once())
            ->method('addAttribute')
            ->with(Category::ENTITY, 'cms_page', $this->callback(function (array $attribute): bool {
                $this->assertSame('int', $attribute['type']);
                $this->assertSame('select', $attribute['input']);
                $this->assertSame(CmsPage::class, $attribute['source']);
                $this->assertFalse($attribute['required']);
                $this->assertSame(ScopedAttributeInterface::SCOPE_STORE, $attribute['global']);
                $this->assertSame('Display Settings', $attribute['group']);

                return true;
            }))
            ->willReturnSelf();

        $eavSetupFactory = $this->createMock(EavSetupFactory::class);
        $eavSetupFactory->method('create')->willReturn($eavSetup);

        $patch = new AddCmsPageAttributeToCategory($eavSetupFactory);

        $this->assertSame($patch, $patch->apply());
    }

    public function testPatchHasNoDependenciesOrAliases(): void
    {
        $patch = new AddCmsPageAttributeToCategory($this->createMock(EavSetupFactory::class));

        $this->assertSame([], AddCmsPageAttributeToCategory::getDependencies());
        $this->assertSame([], $patch->getAliases());
    }
}
