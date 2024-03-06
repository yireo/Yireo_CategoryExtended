<?php declare(strict_types=1);

namespace Yireo\CategoryExtended\Setup\Patch\Data;

use Magento\Catalog\Model\Category;
use Magento\Cms\Model\Config\Source\Page;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddCustomUrlAttributeToCategory implements DataPatchInterface
{
    public function __construct(
        private EavSetupFactory $eavSetupFactory
    )
    {
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }

    public function apply()
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create();
        $eavSetup->addAttribute(Category::ENTITY, 'custom_url', [
            'type' => 'varchar',
            'label' => 'Custom URL',
            'input' => 'text',
            'required' => false,
            'sort_order' => 21,
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'group' => 'Search Engine Optimization',
        ]);
    }
}
