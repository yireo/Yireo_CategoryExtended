<?php declare(strict_types=1);

namespace Yireo\CategoryExtended\Util;

use Magento\Framework\UrlInterface;

class NormalizeUrl
{
    public function __construct(
        private UrlInterface $urlModel
    ) {
    }

    public function normalize(string $url): string
    {
        if (!preg_match('#^https?://#i', $url)) {
            $url = $this->urlModel->getUrl($url);
        }

        return $url;
    }
}
