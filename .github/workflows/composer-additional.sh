# The Playwright tests need the fixture module Yireo_CategoryExtendedTestFixture, which creates CMS pages and categories
case "${GITHUB_JOB:-$CI_JOB_NAME}" in playwright*)
  composer config repositories.local-source path /tmp/magento/package-source
  composer config repositories.local-source-fixture path /tmp/magento/package-source/Test/Fixture/CategoryExtendedTestFixture
  composer require -n -- yireo/magento2-category-extended:@dev yireo/magento2-category-extended-test-fixture:@dev
  php bin/magento module:enable Yireo_CategoryExtended Yireo_CategoryExtendedTestFixture || true
  ;;
esac
