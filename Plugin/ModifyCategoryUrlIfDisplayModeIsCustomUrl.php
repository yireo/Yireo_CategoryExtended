<?php declare(strict_types=1);

namespace Yireo\CategoryExtended\Plugin;

use Yireo\CategoryExtended\Model\CategoryExtended;
use Yireo\CategoryExtended\Util\NormalizeUrl;

class ModifyCategoryUrlIfDisplayModeIsCustomUrl
{
    public function __construct(
        private NormalizeUrl $normalizeUrl
    ) {
    }

    /**
     * @param mixed $subject
     * @param string $result
     * @param Category $category
     * @return string
     */
    public function afterGetCategoryUrl($subject, string $result, $category): string
    {
        if ($category->getDisplayMode() !== CategoryExtended::DM_CUSTOM_URL) {
            return $result;
        }

        $customUrl = trim((string)$category->getCustomUrl());
        if (empty($customUrl)) {
            return $result;
        }

        return $this->normalizeUrl->normalize($customUrl);
    }
}
