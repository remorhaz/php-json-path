# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Added
- Added `QueryInterface::isDefinite()` method that returns true if given query can address
only one value.
### Changed
- `SelectResultInterface::encode()` method added that deprecates `::toJson()`.

## [0.1.0] - 2019-07-23
- Initial release.