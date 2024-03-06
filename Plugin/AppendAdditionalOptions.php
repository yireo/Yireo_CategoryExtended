<?php declare(strict_types=1);

namespace Yireo\CategoryExtended\Plugin;

use Magento\Catalog\Model\Category\Attribute\Source\Mode;
use Yireo\CategoryExtended\Model\CategoryExtended;

class AppendAdditionalOptions
{
    /**
     * @param Mode $subject
     * @param array $result
     * @return array
     */
    public function afterGetAllOptions(Mode $subject, array $result): array
    {
        $result[] = ['value' => CategoryExtended::DM_CMS_PAGE, 'label' => 'CMS Page'];
        $result[] = ['value' => CategoryExtended::DM_CUSTOM_URL, 'label' => 'Custom URL'];

        return $result;
    }
}
