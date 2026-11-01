import {test, expect, Page, Locator} from '@playwright/test';

export const HYVA_THEME = 'Hyva/default-csp';
export const LUMA_THEME = 'Magento/luma';

export const categories = {
    cmsPage: 'CE CMS Page',
    inactiveCmsPage: 'CE Inactive CMS Page',
    customUrlAbsolute: 'CE Custom URL Absolute',
    customUrlRelative: 'CE Custom URL Relative',
    regular: 'CE Regular Category',
};

export const cmsPageIdentifier = 'category-extended-test-page';
export const cmsPageTitle = 'Category Extended Test Page';
export const absoluteCustomUrl = 'https://example.com/category-extended';
export const relativeCustomUrl = 'customer/account/login';

const token = () => process.env.TEST_TOKEN;

/**
 * The Loki_FunctionalTests configure endpoint resets the theme to Magento/luma when `theme` is missing,
 * so the theme is passed with every call.
 */
export async function configureTheme(page: Page, theme: string) {
    const response = await page.request.post('/loki_functional_tests/index/configure?token=' + token(), {
        form: {
            config: JSON.stringify({theme, config: {}}),
        },
    });

    const body = await response.text();
    let data: any;
    try {
        data = JSON.parse(body);
    } catch (error) {
        throw new Error(`Configure endpoint returned no JSON (status ${response.status()}): ${body.slice(0, 500)}`);
    }

    if (data.error) {
        throw new Error('Configure error: ' + data.error);
    }

    const themeErrors: string[] = data.result?.theme?.errors ?? [];
    test.skip(themeErrors.some(error => error.includes('not found')), `Theme "${theme}" is not installed`);
    expect(themeErrors, 'Configure theme errors').toEqual([]);
}

export async function openHomepage(page: Page) {
    const response = await page.goto('/', {waitUntil: 'load'});
    expect(response, 'Homepage response').not.toBeNull();
    expect(response!.status()).toBe(200);
}

/**
 * Return the menu link of a category, from either the Luma or the Hyva menu
 *
 * Not filtered on visibility: On the first Luma request after a cache clean, the theme CSS is not compiled yet and
 * the Luma JS hides the menu as if on mobile, while the links are rendered already.
 */
export function getMenuLink(page: Page, categoryName: string): Locator {
    return page.locator('nav a[href]', {hasText: categoryName}).first();
}

export async function getMenuHref(page: Page, categoryName: string): Promise<string> {
    const link = getMenuLink(page, categoryName);
    await expect(link, `Menu link of category "${categoryName}" (is Yireo_CategoryExtendedTestFixture enabled?)`).toBeAttached({timeout: 20000});

    return (await link.getAttribute('href')) ?? '';
}

export function getBaseUrl(page: Page): string {
    return new URL(page.url()).origin + '/';
}

export {test, expect};
