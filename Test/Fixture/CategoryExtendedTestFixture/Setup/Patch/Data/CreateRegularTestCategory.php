<?php declare(strict_types=1);

namespace Yireo\CategoryExtendedTestFixture\Setup\Patch\Data;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ResourceModel\Category as CategoryResource;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Model\StoreManagerInterface;

class CreateRegularTestCategory implements DataPatchInterface
{
    private const NAME = 'CE Regular Category';

    public function __construct(
        private readonly CategoryFactory $categoryFactory,
        private readonly CategoryResource $categoryResource,
        private readonly CategoryCollectionFactory $categoryCollectionFactory,
        private readonly StoreManagerInterface $storeManager
    ) {
    }

    public static function getDependencies(): array
    {
        return [CreateTestCategories::class];
    }

    public function getAliases(): array
    {
        return [];
    }

    public function apply(): self
    {
        $rootCategoryId = (int)$this->storeManager->getDefaultStoreView()->getRootCategoryId();
        $collection = $this->categoryCollectionFactory->create();
        $collection->addAttributeToFilter('name', self::NAME);
        $collection->addFieldToFilter('parent_id', $rootCategoryId);
        if ($collection->getSize() > 0) {
            return $this;
        }

        /** @var Category $parentCategory */
        $parentCategory = $this->categoryFactory->create();
        $this->categoryResource->load($parentCategory, $rootCategoryId);

        /** @var Category $category */
        $category = $this->categoryFactory->create();
        $category->setData([
            'name' => self::NAME,
            'url_key' => 'ce-regular-category',
            'parent_id' => $rootCategoryId,
            'path' => $parentCategory->getPath(),
            'is_active' => 1,
            'include_in_menu' => 1,
            'is_anchor' => 1,
            'available_sort_by' => ['position'],
            'default_sort_by' => 'position',
            'store_id' => 0,
            'display_mode' => Category::DM_PRODUCT,
        ]);
        $this->categoryResource->save($category);

        return $this;
    }
}
