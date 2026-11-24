<?php declare(strict_types=1);

namespace Yireo\CategoryExtended\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class AddExtendedAttributesToCategoryCollectionBeforeLoad implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        $collection = $observer->getEvent()->getCategoryCollection();
        $collection->addAttributeToSelect(['display_mode', 'cms_page', 'custom_url']);
    }
}
