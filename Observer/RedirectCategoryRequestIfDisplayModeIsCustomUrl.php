<?php declare(strict_types=1);

namespace Yireo\CategoryExtended\Observer;

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Yireo\CategoryExtended\Exception\InvalidCustomUrl;
use Yireo\CategoryExtended\Model\CategoryExtended;
use Yireo\CategoryExtended\Util\NormalizeUrl;

class RedirectCategoryRequestIfDisplayModeIsCustomUrl implements ObserverInterface
{
    public function __construct(
        private NormalizeUrl $normalizeUrl
    ) {
    }

    public function execute(Observer $observer)
    {
        /** @var CategoryInterface $category */
        $category = $observer->getEvent()->getCategory();
        if ($category->getDisplayMode() !== CategoryExtended::DM_CUSTOM_URL) {
            return;
        }

        $customUrl = trim((string)$category->getData('custom_url'), " \f\n\r\t\v\x00");
        if (empty($customUrl)) {
            return;
        }

        try {
            $customUrl = $this->normalizeUrl->normalize($customUrl);
        } catch (InvalidCustomUrl $e) {
            return;
        }

        /** @var Action $action */
        $action = $observer->getEvent()->getControllerAction();
        $action->getResponse()->setRedirect($customUrl);
    }
}
