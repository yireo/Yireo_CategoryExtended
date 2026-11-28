<?php declare(strict_types=1);

namespace Yireo\CategoryExtended\Util;

use Magento\Framework\UrlInterface;
use Yireo\CategoryExtended\Exception\InvalidCustomUrl;

class NormalizeUrl
{
    public function __construct(
        private readonly UrlInterface $urlModel,
        private readonly CustomUrlValidator $customUrlValidator
    ) {
    }

    /**
     * @throws InvalidCustomUrl
     */
    public function normalize(string $url): string
    {
        if (!$this->customUrlValidator->isValid($url)) {
            throw new InvalidCustomUrl('Invalid custom URL');
        }

        if (!preg_match('#^https?://#i', $url)) {
            $url = $this->urlModel->getUrl(null, ['_direct' => ltrim($url, '/')]);
        }

        return $url;
    }
}
