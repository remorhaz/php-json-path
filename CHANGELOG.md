# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.7.7] - 2021-10-13
### Fixed
- Queries now don't share same caching callback builder instance. 

## [0.7.6] - 2021-08-17
### Fixed
- PHP 8.1 compatibility issue ([#24](https://github.com/remorhaz/php-json-path/issues/24)).

## [0.7.5] - 2021-04-10
### Fixed
- Closure moved out from generated query code to prevent memory leak (see https://bugs.php.net/bug.php?id=76982).

## [0.7.4] - 2021-01-15
### Added
- PHP 8.0 support.

## [0.7.3] - 2020-03-12
### Added
- Upgraded `remorhaz/php-unilex` to version 0.3.1.
- Names in dot-notation now allow non-ASCII characters.

## [0.7.2] - 2020-03-26
### Fixed
- Order of array elements in result corresponds to order of indexes in filter.
- Order of object properties in result corresponds to order of properties in filter.
- Slices with negative steps are returned in reversed order.
- Names in dot-notation now allow hyphen symbol (`U+002D`) as non-starting symbol.
- Fixed duplicated results in some filtered queries.

## [0.7.1] - 2020-02-17
### Added
- Issue #18: processor results provide access to raw value objects.
- Upgraded `remorhaz/php-unilex` to version 0.1.0. 

## [0.7.0] - 2019-11-13
### Removed
- Comparators moved from runtime to `remorhaz/php-json-data`.

## [0.6.1] - 2019-11-12
### Fixed
- Issue #16: `min()`/`max()` aggregator functions fail on single-element array.

## [0.6.0] - 2019-11-06
### Changed
- Some methods of `MapIndexInterface` renamed.
- Issue #13: local data accessors/events replaced by `remorhaz/php-json-data`.

## [0.5.4] - 2019-09-21
### Added
- Issue #12: quoted names allowed in dot-notation.
- Issue #11: unquoted names allowed in bracket-notation.
### Fixed
- Incorrect deep scan before predicate.

## [0.5.3] - 2019-09-21
### Added
- Issue #8: single negative index in predicate is considered as start of a slice.
- Issue #9: deep scan of predicates (like `$..[0, 2]`) is supported.
- Fetcher is now able to merge value lists.
- Issue #10: dot is allowed before predicate.

## [0.5.2] - 2019-08-28
### Fixed
- Query callback builder clears it's state correctly before processing next AST.

## [0.5.1] - 2019-08-26
### Added
- Support for numeric properties in dot-notation.

## [0.5.0] - 2019-08-23
### Added
- Method for checking compatibility of index maps.
### Fixed
- Documentation improved.
- Issue #6: filtering by partially-existing property comparison throws exception.
### Changed
- Maps can store NULL outer indexes for missing values.

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