<?php declare(strict_types=1);

namespace Yireo\CategoryExtended\Test\Integration\Controller;

use Magento\Framework\App\Response\Http as HttpResponse;
use Magento\Framework\App\ResponseFactory;
use Magento\TestFramework\TestCase\AbstractController;
use Yireo\CategoryExtended\Model\CategoryExtended;
use Yireo\CategoryExtended\Observer\RedirectCategoryRequestIfDisplayModeIsCmsPage;
use Yireo\CategoryExtended\Observer\RedirectCategoryRequestIfDisplayModeIsCustomUrl;
use Yireo\CategoryExtended\Test\Integration\Fixture\CreateCategoryExtendedFixtures;

/**
 * @magentoAppArea frontend
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 */
class CategoryViewRedirectTest extends AbstractController
{
    use CreateCategoryExtendedFixtures;

    protected function setUp(): void
    {
        parent::setUp();

        $responseFactory = $this->createMock(ResponseFactory::class);
        $responseFactory->method('create')->willReturnCallback(fn () => $this->getResponse());
        $this->_objectManager->addSharedInstance($responseFactory, ResponseFactory::class);
        $this->_objectManager->removeSharedInstance(RedirectCategoryRequestIfDisplayModeIsCmsPage::class);
        $this->_objectManager->removeSharedInstance(RedirectCategoryRequestIfDisplayModeIsCustomUrl::class);
    }

    protected function tearDown(): void
    {
        $this->_objectManager->removeSharedInstance(ResponseFactory::class);
        $this->_objectManager->removeSharedInstance(RedirectCategoryRequestIfDisplayModeIsCmsPage::class);
        $this->_objectManager->removeSharedInstance(RedirectCategoryRequestIfDisplayModeIsCustomUrl::class);

        parent::tearDown();
    }

    public function testCmsPageCategoryRedirectsToCmsPage(): void
    {
        $page = $this->createCmsPage('ce-redirect-page');
        $category = $this->createCategory('CE Redirect CMS', CategoryExtended::DM_CMS_PAGE, [
            'cms_page' => $page->getId(),
        ]);

        $this->dispatch('catalog/category/view/id/' . $category->getId());

        $this->assertRedirect($this->stringContains('ce-redirect-page'));
    }

    public function testInactiveCmsPageCategoryDoesNotRedirect(): void
    {
        $page = $this->createCmsPage('ce-redirect-inactive-page', false);
        $category = $this->createCategory('CE Redirect Inactive', CategoryExtended::DM_CMS_PAGE, [
            'cms_page' => $page->getId(),
        ]);

        $this->dispatch('catalog/category/view/id/' . $category->getId());

        $this->assertFalse($this->getHttpResponse()->isRedirect());
    }

    public function testAbsoluteCustomUrlCategoryRedirectsToCustomUrl(): void
    {
        $category = $this->createCategory('CE Redirect Absolute', CategoryExtended::DM_CUSTOM_URL, [
            'custom_url' => 'https://example.com/category-extended',
        ]);

        $this->dispatch('catalog/category/view/id/' . $category->getId());

        $this->assertRedirect($this->equalTo('https://example.com/category-extended'));
    }

    public function testRelativeCustomUrlCategoryRedirectsToAbsoluteUrl(): void
    {
        $category = $this->createCategory('CE Redirect Relative', CategoryExtended::DM_CUSTOM_URL, [
            'custom_url' => 'customer/account/login',
        ]);

        $this->dispatch('catalog/category/view/id/' . $category->getId());

        $this->assertRedirect($this->matchesRegularExpression('#^https?://.+/customer/account/login#'));
    }

    public function testDefaultCategoryDoesNotRedirect(): void
    {
        $category = $this->createCategory('CE Redirect Default');

        $this->dispatch('catalog/category/view/id/' . $category->getId());

        $response = $this->getHttpResponse();
        $this->assertFalse($response->isRedirect());
        $this->assertSame(200, $response->getHttpResponseCode());
    }

    private function getHttpResponse(): HttpResponse
    {
        /** @var HttpResponse $response */
        $response = $this->getResponse();

        return $response;
    }
}
