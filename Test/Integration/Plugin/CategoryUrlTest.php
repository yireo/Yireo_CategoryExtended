<?php declare(strict_types=1);

namespace Yireo\CategoryExtended\Test\Integration\Plugin;

use Magento\Catalog\Helper\Category as CategoryHelper;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Yireo\CategoryExtended\Model\CategoryExtended;
use Yireo\CategoryExtended\Test\Integration\Fixture\CreateCategoryExtendedFixtures;

/**
 * @magentoAppArea frontend
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 */
class CategoryUrlTest extends TestCase
{
    use CreateCategoryExtendedFixtures;

    public function testCmsPageCategoryLinksToCmsPage(): void
    {
        $page = $this->createCmsPage('ce-integration-page');
        $category = $this->createCategory('CE CMS Page', CategoryExtended::DM_CMS_PAGE, ['cms_page' => $page->getId()]);

        $this->assertSame($this->getUrl('ce-integration-page'), $this->getCategoryUrl($category));
    }

    public function testCategoryWithInactiveCmsPageKeepsCategoryUrl(): void
    {
        $page = $this->createCmsPage('ce-integration-inactive-page', false);
        $category = $this->createCategory('CE Inactive', CategoryExtended::DM_CMS_PAGE, ['cms_page' => $page->getId()]);

        $this->assertSame($category->getUrl(), $this->getCategoryUrl($category));
        $this->assertStringNotContainsString('ce-integration-inactive-page', $this->getCategoryUrl($category));
    }

    public function testCategoryWithoutCmsPageKeepsCategoryUrl(): void
    {
        $category = $this->createCategory('CE Missing', CategoryExtended::DM_CMS_PAGE);

        $this->assertSame($category->getUrl(), $this->getCategoryUrl($category));
    }

    public function testAbsoluteCustomUrlIsUsedUnchanged(): void
    {
        $category = $this->createCategory('CE Absolute', CategoryExtended::DM_CUSTOM_URL, [
            'custom_url' => 'https://example.com/category-extended',
        ]);

        $this->assertSame('https://example.com/category-extended', $this->getCategoryUrl($category));
    }

    public function testRelativeCustomUrlIsMadeAbsolute(): void
    {
        $category = $this->createCategory('CE Relative', CategoryExtended::DM_CUSTOM_URL, [
            'custom_url' => 'customer/account/login',
        ]);

        $baseUrl = Bootstrap::getObjectManager()->get(StoreManagerInterface::class)->getStore()->getBaseUrl();
        $this->assertStringStartsWith($baseUrl . 'customer/account/login', $this->getCategoryUrl($category));
    }

    public function testUnsafeStoredCustomUrlKeepsCategoryUrl(): void
    {
        $category = $this->createCategory('CE Unsafe', CategoryExtended::DM_CUSTOM_URL);
        $originalUrl = $category->getUrl();

        foreach (['javascript://example.com/%0aalert(document.domain)', 'https://evil.example/" onmouseover="alert(1)'] as $customUrl) {
            $category->setData('custom_url', $customUrl);
            $this->assertSame($originalUrl, $this->getCategoryUrl($category));
        }
    }

    /**
     * @magentoDataFixture Magento/Store/_files/second_store.php
     */
    public function testCmsPageOfOtherStoreKeepsCategoryUrl(): void
    {
        $secondStoreId = (int)Bootstrap::getObjectManager()->get(StoreManagerInterface::class)
            ->getStore('fixture_second_store')->getId();
        $page = $this->createCmsPage('ce-integration-other-store-page', true, [$secondStoreId]);
        $category = $this->createCategory('CE Other Store', CategoryExtended::DM_CMS_PAGE, ['cms_page' => $page->getId()]);

        $this->assertSame($category->getUrl(), $this->getCategoryUrl($category));
    }

    public function testDefaultCategoryKeepsCategoryUrl(): void
    {
        $category = $this->createCategory('CE Default');

        $this->assertSame($category->getUrl(), $this->getCategoryUrl($category));
        $this->assertStringContainsString((string)$category->getUrlKey(), $this->getCategoryUrl($category));
    }

    private function getCategoryUrl($category): string
    {
        return Bootstrap::getObjectManager()->get(CategoryHelper::class)->getCategoryUrl($category);
    }

    private function getUrl(string $path): string
    {
        return Bootstrap::getObjectManager()->get(UrlInterface::class)->getUrl($path);
    }
}
