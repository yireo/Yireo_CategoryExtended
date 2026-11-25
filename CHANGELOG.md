# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.1.2] - 25 November 2026
### Removed
- Dropped support for PHP 7.4 and 8.1; PHP 8.2 up to 8.5 is now required

### Changed
- Development files (tests, CI configuration) are no longer shipped in the Composer distribution archive

### Fixed
- Custom URLs are now validated: only absolute http(s) URLs and relative store paths are accepted on save and when rendering or redirecting, preventing stored XSS and unsafe redirects
- Redirects for categories with a CMS page or custom URL could end in a 200 response showing the category page instead of a 302 redirect
- Relative custom URLs are now turned into absolute store URLs
- Categories could link to a CMS page that is not assigned to the current store view
- Categories with a CMS page or custom URL display mode were not removed from the XML sitemap, and display modes set per store view were ignored
- The XML sitemap generation could fail when no categories were returned
- The "CMS Page" and "Custom URL" display mode labels could not be translated
- Data patches did not return themselves from apply()
- Composer dependencies on the Magento CMS, EAV, Sitemap and Store modules were missing
- trim() calls prepared for PHP 8.6 compatibility
