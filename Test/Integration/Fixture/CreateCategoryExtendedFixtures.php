<?php declare(strict_types=1);

namespace Yireo\CategoryExtended\Test\Integration\Fixture;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ResourceModel\Category as CategoryResource;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Cms\Model\PageFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

trait CreateCategoryExtendedFixtures
{
    private function createCmsPage(string $identifier, bool $active = true): PageInterface
    {
        $objectManager = Bootstrap::getObjectManager();

        /** @var PageInterface $page */
        $page = $objectManager->get(PageFactory::class)->create();
        $page->setIdentifier($identifier);
        $page->setTitle('Category Extended ' . $identifier);
        $page->setContent('<p>Category Extended test page ' . $identifier . '</p>');
        $page->setPageLayout('1column');
        $page->setIsActive($active);
        $page->setData('stores', [0]);

        return $objectManager->get(PageRepositoryInterface::class)->save($page);
    }

    private function createCategory(string $name, ?string $displayMode = null, array $data = []): Category
    {
        $objectManager = Bootstrap::getObjectManager();
        $storeManager = $objectManager->get(StoreManagerInterface::class);
        $rootCategoryId = (int)$storeManager->getDefaultStoreView()->getRootCategoryId();

        /** @var Category $parentCategory */
        $parentCategory = $objectManager->get(CategoryRepositoryInterface::class)->get($rootCategoryId, 0);

        /** @var Category $category */
        $category = $objectManager->get(CategoryFactory::class)->create();
        $category->setData(array_merge([
            'name' => $name,
            'url_key' => strtolower(str_replace(' ', '-', $name)) . '-' . uniqid(),
            'parent_id' => $rootCategoryId,
            'path' => $parentCategory->getPath(),
            'is_active' => 1,
            'include_in_menu' => 1,
            'available_sort_by' => ['position'],
            'default_sort_by' => 'position',
            'store_id' => 0,
            'display_mode' => $displayMode ?? Category::DM_PRODUCT,
        ], $data));

        $objectManager->get(CategoryResource::class)->save($category);

        return $this->loadCategory((int)$category->getId());
    }

    private function loadCategory(int $categoryId): Category
    {
        $objectManager = Bootstrap::getObjectManager();
        $storeId = (int)$objectManager->get(StoreManagerInterface::class)->getDefaultStoreView()->getId();

        /** @var Category $category */
        $category = $objectManager->get(CategoryFactory::class)->create();
        $category->setStoreId($storeId);
        $objectManager->get(CategoryResource::class)->load($category, $categoryId);

        return $category;
    }
}
