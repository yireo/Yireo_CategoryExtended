<?php declare(strict_types=1);

namespace Yireo\CategoryExtended\Test\Unit\Setup\Patch\Data;

use Magento\Catalog\Model\Category;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use PHPUnit\Framework\TestCase;
use Yireo\CategoryExtended\Setup\Patch\Data\AddCustomUrlAttributeToCategory;

class AddCustomUrlAttributeToCategoryTest extends TestCase
{
    public function testApplyAddsStoreScopedCustomUrlText(): void
    {
        $eavSetup = $this->createMock(EavSetup::class);
        $eavSetup->expects($this->once())
            ->method('addAttribute')
            ->with(Category::ENTITY, 'custom_url', $this->callback(function (array $attribute): bool {
                $this->assertSame('varchar', $attribute['type']);
                $this->assertSame('text', $attribute['input']);
                $this->assertFalse($attribute['required']);
                $this->assertSame(ScopedAttributeInterface::SCOPE_STORE, $attribute['global']);
                $this->assertSame('Search Engine Optimization', $attribute['group']);

                return true;
            }))
            ->willReturnSelf();

        $eavSetupFactory = $this->createMock(EavSetupFactory::class);
        $eavSetupFactory->method('create')->willReturn($eavSetup);

        $patch = new AddCustomUrlAttributeToCategory($eavSetupFactory);

        $this->assertSame($patch, $patch->apply());
    }

    public function testPatchHasNoDependenciesOrAliases(): void
    {
        $patch = new AddCustomUrlAttributeToCategory($this->createMock(EavSetupFactory::class));

        $this->assertSame([], AddCustomUrlAttributeToCategory::getDependencies());
        $this->assertSame([], $patch->getAliases());
    }
}
