# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.2] - 2021-02-01
### Fixed
- Undefined source for billing information details component (on production mode with bundled JS)

## [2.0.1] - 2021-01-31
### Fixed
- "Next" button not responsive when "braintree_cc_vault" payment method is set, but credit card is not chosen

## [2.0.0] - 2021-01-31
### Added
- Braintree Vault payment method title generation in Billing Information block

### Changed
- (breaking) Custom billing address form handling switched back to core Magento

## [1.1.0] - 2021-01-30
### Added
- Possibility to move payment methods additional validations to the "Next" button
- Braintree Credit Card payment method coverage
- Braintree Vault payment method coverage
- Alert with error message after unsuccessful order placing

### Changed
- Payment methods templates are now not overwritten to remove "Place order" buttons and checkout agreements blocks. Instead, they are hidden using CSS.

### Fixed
- Error that occurred sometimes while setting payment method title
- Issues with Braintree payment information save for Vault method