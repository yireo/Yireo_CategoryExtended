<?php declare(strict_types=1);

namespace Yireo\CategoryExtended\Model;

class CategoryExtended
{
    public const DM_CMS_PAGE = 'CMS_PAGE';
    public const DM_CUSTOM_URL = 'CUSTOM_URL';

    public function getExtendedDisplayModes(): array
    {
        return [
            self::DM_CMS_PAGE => 'cms_page',
            self::DM_CUSTOM_URL => 'custom_url',
        ];
    }
}
