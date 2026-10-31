<?php declare(strict_types=1);

namespace Yireo\CategoryExtended\Test\Integration\Plugin;

use Magento\Catalog\Helper\Category as CategoryHelper;
use Magento\Catalog\Model\Category\Attribute\Source\Mode;
use Magento\Sitemap\Model\ResourceModel\Catalog\Category as SitemapCategory;
use PHPUnit\Framework\TestCase;
use Yireo\CategoryExtended\Plugin\AppendAdditionalOptions;
use Yireo\CategoryExtended\Plugin\ModifyCategoryUrlIfDisplayModeIsCmsPage;
use Yireo\CategoryExtended\Plugin\ModifyCategoryUrlIfDisplayModeIsCustomUrl;
use Yireo\CategoryExtended\Plugin\RemoveCategoriesWithIrrelevantDisplayMode;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\AssertInterceptorPluginIsRegistered;

class PluginRegistrationTest extends TestCase
{
    use AssertInterceptorPluginIsRegistered;

    public function testSitemapPluginIsRegisteredGlobally(): void
    {
        $this->assertInterceptorPluginIsRegistered(
            SitemapCategory::class,
            RemoveCategoriesWithIrrelevantDisplayMode::class,
            'Yireo_CategoryExtended::removeCategoriesWithIrrelevantDisplayMode'
        );
    }

    /**
     * @magentoAppArea adminhtml
     */
    public function testModeSourcePluginIsRegisteredInAdminhtml(): void
    {
        $this->assertInterceptorPluginIsRegistered(
            Mode::class,
            AppendAdditionalOptions::class,
            'Yireo_CategoryExtended::appendAdditionalOptions'
        );
    }

    /**
     * @magentoAppArea frontend
     */
    public function testCategoryHelperPluginsAreRegisteredInFrontend(): void
    {
        $this->assertInterceptorPluginIsRegistered(
            CategoryHelper::class,
            ModifyCategoryUrlIfDisplayModeIsCustomUrl::class,
            'Yireo_CategoryExtended::modifyCategoryUrlIfDisplayModeIsCustomUrl'
        );
        $this->assertInterceptorPluginIsRegistered(
            CategoryHelper::class,
            ModifyCategoryUrlIfDisplayModeIsCmsPage::class,
            'Yireo_CategoryExtended::modifyCategoryUrlIfDisplayModeIsCmsPage'
        );
    }
}
