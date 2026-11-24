<?php declare(strict_types=1);

namespace Yireo\CategoryExtended\Util;

use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Yireo\CategoryExtended\Exception\InvalidCmsPageId;

class GetUrlByCmsPageId
{
    public function __construct(
        private readonly UrlInterface $urlModel,
        private readonly PageRepositoryInterface $cmsPageRepository,
        private readonly StoreManagerInterface $storeManager
    ) {
    }

    /**
     * @param int $cmsPageId
     * @return string
     * @throws InvalidCmsPageId
     */
    public function get(int $cmsPageId): string
    {
        if (false === $cmsPageId > 0) {
            throw new InvalidCmsPageId('Empty CMS Page ID');
        }

        try {
            $cmsPage = $this->cmsPageRepository->getById($cmsPageId);
        } catch (LocalizedException $e) {
            throw new InvalidCmsPageId('CMS Page not found');
        }

        if ($cmsPage->isActive() !== true) {
            throw new InvalidCmsPageId('CMS Page is inactive');
        }

        if (!$this->isAssignedToCurrentStore((array)$cmsPage->getStores())) {
            throw new InvalidCmsPageId('CMS Page is not assigned to the current store');
        }

        return $this->urlModel->getUrl($cmsPage->getIdentifier());
    }

    private function isAssignedToCurrentStore(array $storeIds): bool
    {
        $storeIds = array_map('intval', $storeIds);
        if (in_array(Store::DEFAULT_STORE_ID, $storeIds, true)) {
            return true;
        }

        try {
            $currentStoreId = (int)$this->storeManager->getStore()->getId();
        } catch (LocalizedException $e) {
            return false;
        }

        return in_array($currentStoreId, $storeIds, true);
    }
}
