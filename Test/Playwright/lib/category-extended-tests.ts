import {
    test,
    expect,
    configureTheme,
    openHomepage,
    getMenuHref,
    getBaseUrl,
    categories,
    cmsPageIdentifier,
    cmsPageTitle,
    absoluteCustomUrl,
    relativeCustomUrl,
} from './category-extended';

/**
 * Shared test cases for Luma and Hyva, so both themes are covered with the very same assertions
 */
export function defineCategoryExtendedTests(theme: string) {
    test.beforeEach(async function ({page}) {
        await configureTheme(page, theme);
        await openHomepage(page);
    });

    test('links a category with display mode CMS Page to the CMS page', async function ({page}) {
        const href = await getMenuHref(page, categories.cmsPage);
        expect(new URL(href).pathname).toBe('/' + cmsPageIdentifier + '/');

        await page.goto(href);
        await expect(page).toHaveURL(new RegExp('/' + cmsPageIdentifier + '/?$'));
        await expect(page.locator('h1')).toContainText(cmsPageTitle);
    });

    test('links a category with an absolute custom URL to that URL', async function ({page}) {
        expect(await getMenuHref(page, categories.customUrlAbsolute)).toBe(absoluteCustomUrl);
    });

    test('links a category with a relative custom URL to an absolute shop URL', async function ({page}) {
        const href = await getMenuHref(page, categories.customUrlRelative);
        expect(href.replace(/\/$/, '')).toBe(getBaseUrl(page) + relativeCustomUrl);
    });

    test('keeps the category URL when the CMS page is inactive', async function ({page}) {
        const href = await getMenuHref(page, categories.inactiveCmsPage);
        expect(new URL(href).pathname).toBe('/ce-inactive-cms-page.html');

        await page.goto(href);
        await expect(page).toHaveURL(/\/ce-inactive-cms-page\.html$/);
        await expect(page.locator('h1')).toContainText(categories.inactiveCmsPage);
    });

    test('redirects a direct request of a CMS Page category to the CMS page', async function ({page}) {
        const response = await page.request.get('/ce-cms-page.html', {maxRedirects: 0});
        expect(response.status()).toBe(302);
        expect(new URL(response.headers()['location']).pathname).toBe('/' + cmsPageIdentifier + '/');
    });

    test('redirects a direct request of a custom URL category to the custom URL', async function ({page}) {
        const response = await page.request.get('/ce-custom-url-absolute.html', {maxRedirects: 0});
        expect(response.status()).toBe(302);
        expect(response.headers()['location']).toBe(absoluteCustomUrl);
    });

    test('keeps the URL of a regular category', async function ({page}) {
        const href = await getMenuHref(page, categories.regular);
        expect(new URL(href).pathname).toBe('/ce-regular-category.html');
    });
}
