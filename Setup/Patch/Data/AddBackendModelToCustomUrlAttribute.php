<?php declare(strict_types=1);

namespace Yireo\CategoryExtended\Setup\Patch\Data;

use Magento\Catalog\Model\Category;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Yireo\CategoryExtended\Model\Category\Attribute\Backend\CustomUrl;

class AddBackendModelToCustomUrlAttribute implements DataPatchInterface
{
    public function __construct(
        private readonly EavSetupFactory $eavSetupFactory
    ) {
    }

    public static function getDependencies()
    {
        return [AddCustomUrlAttributeToCategory::class];
    }

    public function getAliases()
    {
        return [];
    }

    public function apply()
    {
        $eavSetup = $this->eavSetupFactory->create();
        $eavSetup->updateAttribute(Category::ENTITY, 'custom_url', 'backend_model', CustomUrl::class);

        return $this;
    }
}
