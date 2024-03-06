<?php declare(strict_types=1);

namespace Yireo\CategoryExtended\Util;

use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Yireo\CategoryExtended\Exception\InvalidCmsPageId;

class GetUrlByCmsPageId
{
    public function __construct(
        private UrlInterface $urlModel,
        private PageRepositoryInterface $cmsPageRepository
    )
    {
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

        return $this->urlModel->getUrl($cmsPage->getIdentifier());
    }
}
