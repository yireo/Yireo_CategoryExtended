<?php declare(strict_types=1);

namespace Yireo\CategoryExtended\Test\Unit\Setup\Patch\Data;

use Magento\Catalog\Model\Category;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use PHPUnit\Framework\TestCase;
use Yireo\CategoryExtended\Model\Category\Attribute\Backend\CustomUrl;
use Yireo\CategoryExtended\Setup\Patch\Data\AddBackendModelToCustomUrlAttribute;
use Yireo\CategoryExtended\Setup\Patch\Data\AddCustomUrlAttributeToCategory;

class AddBackendModelToCustomUrlAttributeTest extends TestCase
{
    public function testApplySetsBackendModelOfCustomUrl(): void
    {
        $eavSetup = $this->createMock(EavSetup::class);
        $eavSetup->expects($this->once())
            ->method('updateAttribute')
            ->with(Category::ENTITY, 'custom_url', 'backend_model', CustomUrl::class)
            ->willReturnSelf();

        $eavSetupFactory = $this->createMock(EavSetupFactory::class);
        $eavSetupFactory->method('create')->willReturn($eavSetup);

        $patch = new AddBackendModelToCustomUrlAttribute($eavSetupFactory);

        $this->assertSame($patch, $patch->apply());
    }

    public function testPatchDependsOnCustomUrlAttribute(): void
    {
        $patch = new AddBackendModelToCustomUrlAttribute($this->createMock(EavSetupFactory::class));

        $this->assertSame([AddCustomUrlAttributeToCategory::class], AddBackendModelToCustomUrlAttribute::getDependencies());
        $this->assertSame([], $patch->getAliases());
    }
}
