# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Added
- Processor supports selecting value paths (single and multiple).
- Processor supports selecting single value.
- Query object now provides query source for convenience.
### Deprecated
- `QueryInterface::getCapabilities()` method deprecates `::getProperties()`.
### Changed
- Many classes from `Query` namespace were renamed.
- Processor results moved in new namespace.

## [0.1.1] - 2019-07-26
### Added
- Query object now provides properties interface with `isPath` and `isDefinite` flags.
- `QueryCallbackBuilderInterface` implemented.
### Deprecated
- `SelectResultInterface::encode()` method deprecates `::toJson()`.

## [0.1.0] - 2019-07-23
- Initial release.