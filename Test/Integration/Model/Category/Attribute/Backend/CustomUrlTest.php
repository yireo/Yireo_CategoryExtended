<?php declare(strict_types=1);

namespace Yireo\CategoryExtended\Test\Integration\Model\Category\Attribute\Backend;

use Magento\Framework\Exception\LocalizedException;
use PHPUnit\Framework\TestCase;
use Yireo\CategoryExtended\Model\CategoryExtended;
use Yireo\CategoryExtended\Test\Integration\Fixture\CreateCategoryExtendedFixtures;

/**
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 */
class CustomUrlTest extends TestCase
{
    use CreateCategoryExtendedFixtures;

    public function testSavingUnsafeCustomUrlFails(): void
    {
        $this->expectException(LocalizedException::class);
        $this->createCategory('CE Save Unsafe', CategoryExtended::DM_CUSTOM_URL, [
            'custom_url' => 'javascript://example.com/%0aalert(document.domain)',
        ]);
    }

    public function testValidatingUnsafeCustomUrlFails(): void
    {
        $category = $this->createCategory('CE Validate Unsafe', CategoryExtended::DM_CUSTOM_URL);
        $category->setData('custom_url', 'https://evil.example/" onmouseover="alert(1)');

        $this->expectException(LocalizedException::class);
        $category->validate();
    }

    public function testSavingSafeCustomUrlSucceeds(): void
    {
        $category = $this->createCategory('CE Save Safe', CategoryExtended::DM_CUSTOM_URL, [
            'custom_url' => 'https://example.com/safe',
        ]);

        $this->assertSame('https://example.com/safe', $category->getData('custom_url'));
    }
}
