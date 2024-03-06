<?php declare(strict_types=1);

namespace Yireo\CategoryExtended\Plugin;

use Yireo\CategoryExtended\Exception\InvalidCmsPageId;
use Yireo\CategoryExtended\Model\CategoryExtended;
use Yireo\CategoryExtended\Util\GetUrlByCmsPageId;
use Yireo\CategoryExtended\Util\NormalizeUrl;

class ModifyCategoryUrlIfDisplayModeIsCmsPage
{
    public function __construct(
        private GetUrlByCmsPageId $getUrlByCmsPageId
    ) {
    }

    /**
     * @param mixed $subject
     * @param string $result
     * @param Category $category
     * @return string
     */
    public function afterGetCategoryUrl($subject, string $originalCategoryUrl, $category): string
    {
        if ($category->getDisplayMode() !== CategoryExtended::DM_CMS_PAGE) {
            return $originalCategoryUrl;
        }

        try {
            return $this->getUrlByCmsPageId->get((int)$category->getCmsPage());
        } catch(InvalidCmsPageId $e) {
            return $originalCategoryUrl;
        }
    }
}
