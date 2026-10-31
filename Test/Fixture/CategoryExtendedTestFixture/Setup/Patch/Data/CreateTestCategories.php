<?php declare(strict_types=1);

namespace Yireo\CategoryExtendedTestFixture\Setup\Patch\Data;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ResourceModel\Category as CategoryResource;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Cms\Model\Page;
use Magento\Cms\Model\PageFactory;
use Magento\Cms\Model\ResourceModel\Page as PageResource;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Model\StoreManagerInterface;
use Yireo\CategoryExtended\Model\CategoryExtended;
use Yireo\CategoryExtended\Setup\Patch\Data\AddCmsPageAttributeToCategory;
use Yireo\CategoryExtended\Setup\Patch\Data\AddCustomUrlAttributeToCategory;

class CreateTestCategories implements DataPatchInterface
{
    public function __construct(
        private readonly PageFactory $pageFactory,
        private readonly PageResource $pageResource,
        private readonly CategoryFactory $categoryFactory,
        private readonly CategoryResource $categoryResource,
        private readonly CategoryCollectionFactory $categoryCollectionFactory,
        private readonly StoreManagerInterface $storeManager
    ) {
    }

    public static function getDependencies(): array
    {
        return [
            AddCmsPageAttributeToCategory::class,
            AddCustomUrlAttributeToCategory::class,
        ];
    }

    public function getAliases(): array
    {
        return [];
    }

    public function apply(): self
    {
        $activePageId = $this->createCmsPage('category-extended-test-page', 'Category Extended Test Page', true);
        $inactivePageId = $this->createCmsPage('category-extended-inactive-page', 'Category Extended Inactive Page', false);

        $this->createCategory('CE CMS Page', CategoryExtended::DM_CMS_PAGE, ['cms_page' => $activePageId]);
        $this->createCategory('CE Inactive CMS Page', CategoryExtended::DM_CMS_PAGE, ['cms_page' => $inactivePageId]);
        $this->createCategory('CE Custom URL Absolute', CategoryExtended::DM_CUSTOM_URL, [
            'custom_url' => 'https://example.com/category-extended',
        ]);
        $this->createCategory('CE Custom URL Relative', CategoryExtended::DM_CUSTOM_URL, [
            'custom_url' => 'customer/account/login',
        ]);

        return $this;
    }

    private function createCmsPage(string $identifier, string $title, bool $active): int
    {
        /** @var Page $page */
        $page = $this->pageFactory->create();
        $this->pageResource->load($page, $identifier, 'identifier');

        $page->addData([
            'identifier' => $identifier,
            'title' => $title,
            'content_heading' => $title,
            'content' => '<p>' . $title . '</p>',
            'page_layout' => '1column',
            'is_active' => (int)$active,
            'stores' => [0],
        ]);
        $this->pageResource->save($page);

        return (int)$page->getId();
    }

    private function createCategory(string $name, string $displayMode, array $data): void
    {
        $rootCategoryId = (int)$this->storeManager->getDefaultStoreView()->getRootCategoryId();
        $collection = $this->categoryCollectionFactory->create();
        $collection->addAttributeToFilter('name', $name);
        $collection->addFieldToFilter('parent_id', $rootCategoryId);
        if ($collection->getSize() > 0) {
            return;
        }

        /** @var Category $parentCategory */
        $parentCategory = $this->categoryFactory->create();
        $this->categoryResource->load($parentCategory, $rootCategoryId);

        /** @var Category $category */
        $category = $this->categoryFactory->create();
        $category->setData(array_merge([
            'name' => $name,
            'url_key' => strtolower(str_replace(' ', '-', $name)),
            'parent_id' => $rootCategoryId,
            'path' => $parentCategory->getPath(),
            'is_active' => 1,
            'include_in_menu' => 1,
            'is_anchor' => 1,
            'available_sort_by' => ['position'],
            'default_sort_by' => 'position',
            'store_id' => 0,
            'display_mode' => $displayMode,
        ], $data));

        $this->categoryResource->save($category);
    }
}
