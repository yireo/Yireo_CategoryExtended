<?php declare(strict_types=1);

namespace Yireo\CategoryExtended\Observer;

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Framework\App\Response\Http;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Yireo\CategoryExtended\Exception\InvalidCmsPageId;
use Yireo\CategoryExtended\Model\CategoryExtended;
use Yireo\CategoryExtended\Util\GetUrlByCmsPageId;

class RedirectCategoryRequestIfDisplayModeIsCmsPage implements ObserverInterface
{
    public function __construct(
        private ResponseFactory $responseFactory,
        private GetUrlByCmsPageId $getUrlByCmsPageId
    ) {
    }

    public function execute(Observer $observer)
    {
        /** @var CategoryInterface $category */
        $category = $observer->getEvent()->getCategory();
        if ($category->getDisplayMode() !== CategoryExtended::DM_CMS_PAGE) {
            return;
        }

        try {
            $url = $this->getUrlByCmsPageId->get((int)$category->getData('cms_page'));
        } catch(InvalidCmsPageId $e) {
            return;
        }

        /** @var Http $response */
        $response = $this->responseFactory->create();
        $response->setRedirect($url);
        $response->sendResponse();
    }
}
