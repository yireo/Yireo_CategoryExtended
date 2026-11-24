<?php declare(strict_types=1);

namespace Yireo\CategoryExtended\Model\Category\Attribute\Source;

use Magento\Cms\Model\ResourceModel\Page\CollectionFactory;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class CmsPage extends AbstractSource
{
    public function __construct(
        private readonly CollectionFactory $pageCollectionFactory
    ) {
    }

    /**
     * @param bool $withEmpty
     * @return array
     */
    public function getAllOptions($withEmpty = true)
    {
        $collection = $this->pageCollectionFactory->create();
        $collection->addFieldToSelect(['page_id', 'title']);

        $options = [];
        foreach ($collection as $page) {
            $options[] = ['value' => $page->getId(), 'label' => $page->getTitle()];
        }

        if ($withEmpty) {
            array_unshift($options, ['value' => '', 'label' => __('Please select a static page.')]);
        }

        return $options;
    }
}
