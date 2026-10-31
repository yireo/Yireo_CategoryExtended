<?php declare(strict_types=1);

namespace Yireo\CategoryExtended\Test\Unit\Plugin;

use Magento\Catalog\Model\Category\Attribute\Source\Mode;
use PHPUnit\Framework\TestCase;
use Yireo\CategoryExtended\Model\CategoryExtended;
use Yireo\CategoryExtended\Plugin\AppendAdditionalOptions;

class AppendAdditionalOptionsTest extends TestCase
{
    public function testAppendsCmsPageAndCustomUrlOptions(): void
    {
        $original = [
            ['value' => 'PRODUCTS', 'label' => 'Products only'],
        ];

        $result = (new AppendAdditionalOptions())->afterGetAllOptions($this->createMock(Mode::class), $original);

        $this->assertSame([
            ['value' => 'PRODUCTS', 'label' => 'Products only'],
            ['value' => CategoryExtended::DM_CMS_PAGE, 'label' => 'CMS Page'],
            ['value' => CategoryExtended::DM_CUSTOM_URL, 'label' => 'Custom URL'],
        ], $result);
    }
}
