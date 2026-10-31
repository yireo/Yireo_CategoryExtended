<?php declare(strict_types=1);

namespace Yireo\CategoryExtended\Test\Integration\Plugin;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Category\Attribute\Source\Mode;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Yireo\CategoryExtended\Model\CategoryExtended;

/**
 * @magentoAppArea adminhtml
 */
class AppendAdditionalOptionsTest extends TestCase
{
    public function testDisplayModeOptionsContainCmsPageAndCustomUrl(): void
    {
        $options = Bootstrap::getObjectManager()->create(Mode::class)->getAllOptions();
        $values = array_column($options, 'value');

        $this->assertContains(Category::DM_PRODUCT, $values);
        $this->assertContains(CategoryExtended::DM_CMS_PAGE, $values);
        $this->assertContains(CategoryExtended::DM_CUSTOM_URL, $values);
    }
}
