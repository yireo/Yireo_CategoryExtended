import {test, LUMA_THEME} from './lib/category-extended';
import {defineCategoryExtendedTests} from './lib/category-extended-tests';

test.describe('Category Extended (Luma)', function () {
    defineCategoryExtendedTests(LUMA_THEME);
});
