<?php declare(strict_types=1);

namespace Yireo\CategoryExtended\Util;

class CustomUrlValidator
{
    private const ALLOWED_SCHEMES = ['http', 'https'];

    private const ABSOLUTE_URL_PATTERN
        = '#^https?://(?:[^/?\#@\[\]]*@)?(?:[^/?\#:@\[\]]+|\[[0-9a-f:.]+\])(?::\d{1,5})?(?:[/?\#]|$)#i';

    public function isValid(string $url): bool
    {
        if ($url === '' || preg_match('#[\x00-\x20\x7F"\'<>\\\\`]#', $url)) {
            return false;
        }

        if (preg_match('#[\p{Z}\p{C}]#u', $url) !== 0) {
            return false;
        }

        if (!preg_match('#^([a-z][a-z0-9+.\-]*):#i', $url, $match)) {
            return !str_starts_with($url, '//');
        }

        if (!in_array(strtolower($match[1]), self::ALLOWED_SCHEMES, true)) {
            return false;
        }

        return (bool)preg_match(self::ABSOLUTE_URL_PATTERN, $url);
    }
}
