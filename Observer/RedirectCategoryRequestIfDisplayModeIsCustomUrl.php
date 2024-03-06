<?php declare(strict_types=1);

namespace Yireo\CategoryExtended\Observer;

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Framework\App\Response\Http;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\UrlInterface;
use Yireo\CategoryExtended\Model\CategoryExtended;
use Yireo\CategoryExtended\Util\NormalizeUrl;

class RedirectCategoryRequestIfDisplayModeIsCustomUrl implements ObserverInterface
{
    public function __construct(
        private ResponseFactory $responseFactory,
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

        $customUrl = trim((string)$category->getData('custom_url'));
        if (empty($customUrl)) {
            return;
        }

        $customUrl = $this->normalizeUrl->normalize($customUrl);

        /** @var Http $response */
        $response = $this->responseFactory->create();
        $response->setRedirect($customUrl);
        $response->sendResponse();
    }
}
