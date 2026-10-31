#!/bin/bash
# Install the fixture module Yireo_CategoryExtendedTestFixture, needed by the Playwright tests (never in production)
set -e
MODULE_DIR=$(cd "$(dirname "$(readlink -f "$0")")" && pwd)

composer config repositories.yireo-category-extended-test-fixture path "$MODULE_DIR/Test/Fixture/CategoryExtendedTestFixture"
composer show yireo/magento2-category-extended-test-fixture >/dev/null 2>&1 \
  || composer require -n -- yireo/magento2-category-extended-test-fixture:@dev
bin/magento module:enable Yireo_CategoryExtendedTestFixture
