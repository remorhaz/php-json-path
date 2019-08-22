# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.4.2] - 2019-08-22
### Fixed
- Issue #6: filtering by non-existing property comparison throws exception. 

## [0.4.1] - 2019-08-21
### Fixed
- Numeric properties in objects can be addressed correctly (bracket notation only).

## [0.4.0] - 2019-08-12
### Added
- Processor is now able to delete and replace parts of JSON document by query.
- Events redesigned, value walker introduced.
### Changed
- Query capability `isPath` renamed to `isAddressable`. 
- Query constructor accepts callback builder instead of it's parts.
- Query rethrows convenient exception on failure. It contains original query source and source code of generated callback.
- Structure values incapsulate child iterators now.
- Value fetcher methods renamed from `fetchValue*` to `create*Iterator`.
- Path supports checking if it contains another path.
### Removed
- Value iterator factory removed.
- Data events totally removed.
- Value fetcher `fetchArrayLength` method removed.

## [0.3.0] - 2019-07-31
### Added
- Value list builders implemented.
- Interface for value list fetcher.
## Changed
- Evaluator is no more part of runtime object.
- Query invocation signature changed.
- Filter object joined with fetcher.
- Child matchers accept address, value and container now.
- Runtime object is now just an aggregate of lesser parts (like evaluator and matcher factory) and is not passed to callback anymore.

## [0.2.0] - 2019-07-28
### Added
- Processor supports selecting value paths (single and multiple).
- Processor supports selecting single value.
- Query object now provides query source for convenience.
### Removed
- `QueryInterface::getCapabilities()` method replaces `::getProperties()`.
- `SelectResultInterface::toJson()` method removed.
### Changed
- Many classes from `Query` namespace were renamed.
- Processor results moved in new namespace.
- `Query` suffix removed from`CallbackBuilderInterface` methods.

## [0.1.1] - 2019-07-26
### Added
- Query object now provides properties interface with `isPath` and `isDefinite` flags.
- `QueryCallbackBuilderInterface` implemented.
### Deprecated
- `SelectResultInterface::encode()` method deprecates `::toJson()`.

## [0.1.0] - 2019-07-23
- Initial release.