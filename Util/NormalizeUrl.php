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
        if (false === preg_match('#^(http|https)://#', $url)) {
            $url = $this->urlModel->getUrl($url);
        }

        return $url;
    }
}
