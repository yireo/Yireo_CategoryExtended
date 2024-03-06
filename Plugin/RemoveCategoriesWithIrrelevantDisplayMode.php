<?php declare(strict_types=1);

namespace Yireo\CategoryExtended\Plugin;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Sitemap\Model\ResourceModel\Catalog\Category;
use Yireo\CategoryExtended\Model\CategoryExtended;

class RemoveCategoriesWithIrrelevantDisplayMode
{
    public function __construct(
        private CollectionFactory $collectionFactory,
        private CategoryExtended $categoryExtended
    ) {
    }

    /**
     * @param Category $subject
     * @param array $categories
     * @param int $storeId
     * @return array
     */
    public function afterGetCollection(Category $subject, array $categories, $storeId): array
    {
        return array_filter($categories, function($categoryId) {
            return false === in_array($categoryId, $this->getCategoryIdsWithIrrelevantDisplayMode());
        }, ARRAY_FILTER_USE_KEY);
    }

    private function getCategoryIdsWithIrrelevantDisplayMode(): array
    {
        $collection = $this->collectionFactory->create();
        $collection->addAttributeToFilter('display_mode', ['in' => $this->categoryExtended->getExtendedDisplayModes()]);
        return $collection->getAllIds();
    }
}
