<?php declare(strict_types=1);

namespace Yireo\CategoryExtended\Plugin;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sitemap\Model\ResourceModel\Catalog\Category;
use Magento\Store\Model\StoreManagerInterface;
use Yireo\CategoryExtended\Model\CategoryExtended;

class RemoveCategoriesWithIrrelevantDisplayMode
{
    public function __construct(
        private readonly CollectionFactory $collectionFactory,
        private readonly CategoryExtended $categoryExtended,
        private readonly StoreManagerInterface $storeManager
    ) {
    }

    /**
     * @param Category $subject
     * @param array|bool $categories
     * @param int|string|null $storeId
     * @return array|bool
     */
    public function afterGetCollection(Category $subject, array|bool $categories, $storeId): array|bool
    {
        if (!is_array($categories) || $categories === []) {
            return $categories;
        }

        try {
            $storeId = (int)$this->storeManager->getStore($storeId)->getId();
        } catch (NoSuchEntityException $e) {
            return $categories;
        }

        $irrelevantCategoryIds = $this->getCategoryIdsWithIrrelevantDisplayMode($storeId);

        return array_filter($categories, function ($categoryId) use ($irrelevantCategoryIds) {
            return false === in_array($categoryId, $irrelevantCategoryIds);
        }, ARRAY_FILTER_USE_KEY);
    }

    private function getCategoryIdsWithIrrelevantDisplayMode(int $storeId): array
    {
        $collection = $this->collectionFactory->create();
        $collection->setStoreId($storeId);
        $collection->addAttributeToFilter(
            'display_mode',
            ['in' => array_keys($this->categoryExtended->getExtendedDisplayModes())]
        );

        return $collection->getAllIds();
    }
}
