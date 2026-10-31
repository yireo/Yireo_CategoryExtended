import {test, HYVA_THEME} from './lib/category-extended';
import {defineCategoryExtendedTests} from './lib/category-extended-tests';

test.describe('Category Extended (Hyva)', function () {
    defineCategoryExtendedTests(HYVA_THEME);
});
