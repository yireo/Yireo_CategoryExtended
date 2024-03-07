<?php declare(strict_types=1);

namespace Yireo\CategoryExtended\Setup\Patch\Data;

use Magento\Catalog\Model\Category;
use Magento\Cms\Model\Config\Source\Page;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Yireo\CategoryExtended\Model\Category\Attribute\Source\CmsPage;

class AddCmsPageAttributeToCategory implements DataPatchInterface
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
        $eavSetup->addAttribute(Category::ENTITY, 'cms_page', [
            'type' => 'int',
            'label' => 'Add CMS Page',
            'input' => 'select',
            'source' => CmsPage::class,
            'required' => false,
            'sort_order' => 21,
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'group' => 'Display Settings',
        ]);
    }
}
