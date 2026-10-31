<?php declare(strict_types=1);

namespace Yireo\CategoryExtended\Test\Unit\Model;

use PHPUnit\Framework\TestCase;
use Yireo\CategoryExtended\Model\CategoryExtended;

class CategoryExtendedTest extends TestCase
{
    public function testDisplayModeConstants(): void
    {
        $this->assertSame('CMS_PAGE', CategoryExtended::DM_CMS_PAGE);
        $this->assertSame('CUSTOM_URL', CategoryExtended::DM_CUSTOM_URL);
    }

    public function testGetExtendedDisplayModesIsKeyedByStoredDisplayMode(): void
    {
        $displayModes = (new CategoryExtended())->getExtendedDisplayModes();

        $this->assertSame([CategoryExtended::DM_CMS_PAGE, CategoryExtended::DM_CUSTOM_URL], array_keys($displayModes));
    }
}
