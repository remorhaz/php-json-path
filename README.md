# PHP JSONPath
[![Latest Stable Version](https://poser.pugx.org/remorhaz/php-json-path/version)](https://packagist.org/packages/remorhaz/php-json-path)
[![Build Status](https://travis-ci.org/remorhaz/php-json-path.svg?branch=master)](https://travis-ci.org/remorhaz/php-json-path)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/remorhaz/php-json-path/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/remorhaz/php-json-path/?branch=master)
[![codecov](https://codecov.io/gh/remorhaz/php-json-path/branch/master/graph/badge.svg)](https://codecov.io/gh/remorhaz/php-json-path)
[![Infection MSI](https://badge.stryker-mutator.io/github.com/remorhaz/php-json-path/master)](https://infection.github.io)
[![License](https://poser.pugx.org/remorhaz/php-json-path/license)](https://packagist.org/packages/remorhaz/php-json-path)

JSONPath is a simple query language for JSON documents, inspired by XPath for XML and originally designed by [Stefan Goessner](https://goessner.net/articles/JsonPath/).

## Features
- Accepts encoded JSON strings as well as decoded PHP data as input, supports both representations in output.
- Selects, deletes or replaces parts of JSON document using JSONPath queries.
- Recognizes definite/indefinite JSONPath queries without executing them.
- Transforms indefinite JSONPath query to set of definite queries for given JSON document.

## Requirements
- PHP 7.3+
- [JSON extension](https://www.php.net/manual/en/book.json.php) (ext-json)
- [Internationalization functions](https://www.php.net/manual/en/book.intl.php) (ext-intl)

## Installation
You can use Composer to install this package:
```
composer require remorhaz/php-json-path
```

## Usage
### Accessing JSON document
You can create accessible JSON document either from encoded JSON string or from decoded JSON data using corresponding _node value factory_:
```php
use Remorhaz\JSON\Data\Value\EncodedJson;
use Remorhaz\JSON\Data\Value\DecodedJson;

// Creating document from JSON-encoded string:
$encodedValueFactory = EncodedJson\NodeValueFactory::create();
$encodedJson = '{"a":1}';
$document1 = $encodedValueFactory->createValue($encodedJson);

// Creating document from decoded JSON data:
$decodedValueFactory = DecodedJson\NodeValueFactory::create();
$decodedJson = (object) ['a' => 1];
$document2 = $decodedValueFactory->createValue($decodedJson);
```

### Creating query
You should use _query factory_ to create query from JSONPath expression:
```php
use Remorhaz\JSON\Path\Query\QueryFactory;

$queryFactory = QueryFactory::create();

// Creating query that selects all 'a' properties from any document:
$query = $queryFactory->createQuery('$..a');
```
_Definite_ query is the query that defines exactly one path in document. If query includes any filters, wildcards or deep children scan, it is considered _indefinite_.

_Addressable_ query is the query that returns unprocessed part(s) of the document. If query returns an aggregate function result, it is considered _non-addressable_.
### Processing query
You should use an instance of _query processor_ to execute queries on given JSON documents:
```php
use Remorhaz\JSON\Path\Processor\Processor;

$processor = Processor::create();
```

#### Selecting part of a JSON document
There are two ways to select part of JSON document using JSONPath query:

1. You can get all matching parts in array, using `::select()` method. This works with both _definite_ and _indefinite_ queries. You will get empty array if none of document parts matches your query.
2. You can get exactly one matching part, using `::selectOne()` method. Note that this works only with _definite_ queries. You will get an exception if your query is indefinite.

```php
use Remorhaz\JSON\Data\Value\EncodedJson;
use Remorhaz\JSON\Path\Processor\Processor;
use Remorhaz\JSON\Path\Query\QueryFactory;

$processor = Processor::create()
$queryFactory = QueryFactory::create();
$encodedValueFactory = EncodedJson\NodeValueFactory::create();

$document = $encodedValueFactory->createValue('{"a":{"a":1,"b":2}');

// Selecting all 'a' properties (indefinite query, values exist):
$query1 = $queryFactory->createQuery('$..a');
$result1 = $processor->select($query1, $document);
var_dump($result1->select()); // array: ['{"a":1,"b":2}', '1']

// Selecting single 'b' property nested in 'a' property (definite query, value exists):
$query2 = $queryFactory->createQuery('$.a.b');
$result2 = $processor->selectOne($query2, $document);
var_dump($result2->exists()); // boolean: true
var_dump($result2->decode()); // integer: 2

// Selecting single 'b' property (definite query, value doesn't exist):
$query3 = $queryFactory->createQuery('$.b');
$result3 = $processor->selectOne($query3, $document);
var_dump($result3->exists()); // boolean: false
var_dump($result3->decode()); // throws exception
```
Note that you can either encode result(s) of a selection to JSON string(s) or decode them to raw PHP data. Before accessing a result of `::selectOne()` you can check it's existence with `::exists()` method to avoid exception.

#### Deleting part of a JSON document
To delete part(s) of a JSON document use `::delete()` method. It works only with _addressable_ queries. You will get an exception if your query is non-addressable. If none of document parts match the query you will get the document unchanged. Special case is deleting root of a document - in this case you will get non-existing result.
```php
use Remorhaz\JSON\Data\Value\EncodedJson;
use Remorhaz\JSON\Path\Processor\Processor;
use Remorhaz\JSON\Path\Query\QueryFactory;

$processor = Processor::create()
$queryFactory = QueryFactory::create();
$encodedValueFactory = EncodedJson\NodeValueFactory::create();

$document = $encodedValueFactory->createValue('{"a":{"a":1,"b":2}');

// Deleting all 'b' properties (value exists):
$query1 = $queryFactory->createQuery('$..b');
$result1 = $processor->delete($query1, $document);
var_dump($result1->exists()); // boolean: true
var_dump($result1->encode()); // '{"a":{"a":1}}'

// Deleting all 'c' properties (value doesn't exist):
$query2 = $queryFactory->createQuery('$..c');
$result2 = $processor->delete($query2, $document);
var_dump($result1->exists()); // boolean: true
var_dump($result1->encode()); // '{"a":{"a":1,"b":2}}'

// Deleting root of the document:
$query3 = $queryFactory->createValue('$');
$result3 = $processor->delete($query3, $document);
var_dump($result3->exists()); // boolean: false
var_dump($result3->encode()); // throws exception
```

#### Replacing the part of a JSON document with another JSON document
To replace part(s) of a JSON document with another JSON document use `::replace()` method. It works only with _addressable_ queries. You will get an exception if your query is non-addressable. If none of document parts match the query you will get the document unchanged. If the query matches nested parts of a document, you will also get an exception.

```php
use Remorhaz\JSON\Data\Value\EncodedJson;
use Remorhaz\JSON\Path\Processor\Processor;
use Remorhaz\JSON\Path\Query\QueryFactory;

$processor = Processor::create()
$queryFactory = QueryFactory::create();
$encodedValueFactory = EncodedJson\NodeValueFactory::create();

$document1 = $encodedValueFactory->createValue('{"a":{"a":1,"b":2}');
$document2 = $encodedValueFactory->createValue('{"c":3}');

// Replacing 'a' property (value exists):
$query1 = $queryFactory->createQuery('$.a');
$result1 = $processor->replace($query1, $document1, $document2);
var_dump($result1->encode()); // string: '{"a":{"c":3}}'

// Replacing all 'c' properties (value doesn't exist)
$query2 = $queryFactory->createQuery('$..c');
$result2 = $processor->replace($query2, $document1, $document2);
var_dump($result2->encode()); // string: '{"a":{"a":1,"b":2}'

// Replacing all 'a' properties (values are nested):
$query3 = $queryFactory->createQuery('$..a');
$result3 = $processor->replace($query3, $document1, $document2); // throws exception
```

## Grammar
All JSONPath queries start with abstract `$` symbol that denotes outer level object. Internal structure can be 
matched with child operators and filters:

|Operation|Description|
|---|---|
|`$`|Root object of the JSON document.|
|`.a`|Property `a` of current object (dot-notation).|
|`..a`|Properties `a` of current and all it's nested objects.|
|`['a']`|Property `a` of current object (bracket-notation).|
|`['a', 'b']`|Properties `a` and `b` of current object.|
|`[1, 3]`|Indexes `1` and `3` of current array.|
|`[3:10:2]`|Sequence of indexes from `3` to `10` with step `2`.|
|`*`|Wildcard that matches any property of current object / index of current array.|
|`[?(<expression>)]`|Filters values by expression.|
|`.length()`|Aggregate function.|

### Child operators
There are two notations for selecting structure children: _dot_-notation and _bracket_-notation.

Dot-notation allows to select either exactly one property or all children (using a wildcard). _Double-dot_ notation
walks through the JSON structure recursively.

|Example|Description|
|---|---|
|`$.a`|Selects property `a` of a root object.|
|`$.*`|Selects all properties of a root objects or all elements of a root array.|
|`$..a`|Selects property `a` of all objects recursively.|
|`$..*`|Selects all properties/elements of all objects/arrays recursively.|

Bracket-notation allows to select a set of properties/elements:

|Example|Description|
|---|---|
|`$['a', 'b']`|Selects properties `a` and `b` of a root object.|
|`$[2, 3]`|Selects elements `2` and `3` from a root array.|
|`$[3:10:2]`|Selects a sequence of elements from `3` up to `10` with step `2`. This equivalent query is `$[3, 5, 7, 9]`. The notation is same as in Python.|
|`$[*]`|Select all children. Same as `$.*`.|

### Aggregate functions
Aggregate functions can be appended to any path in query and it will return calculated value.

|Function|Description|
|---|---|
|`.min()`|Returns minimal number from current array.|
|`.max()`|Returns maximal number from current array.|
|`.length()`|Returns amount of elements in current array.|
|`.avg()`|Returns average value from numbers in current array.|
|`.stddev()`|Returns standard deviation from numbers in current array.|

The set of aggregate functions and idea itself is taken from [Java implementation](https://github.com/json-path/JsonPath).

### Filter expressions
When filter is being applied to nodeset, it leaves only those nodes for which the expression evaluates to true.

|Example|Description|
|---|---|
|`$..a[?(@.b)]`|Selects all properties `a` that contain objects with property `b`.|
|`$..a[?(@.b > 2)]`|Selects all properties `a` that contain objects with property `b` that is number greater than `2`.|
|`$..a[?(true)]`|Boolean `true` is the only literal that evaluates to `true`; so this query is equivalent to `$..a`.|
|`$..a[?(1)]`|**Attention!** This evaluates to `false`, selecting nothing, because no automatic typecasting is performed.|

#### Filter context
Expression `@` points to the value to which the filter was applied.

#### Operators
_Comparison operators_ can be used to compare value with another value or with a literal. Supported operators are: 
`==`, `!=`, `>`, `>=`, `<` and `<=`. Brackets can be used for _grouping_, and _logical operators_ `&&`, `||` and `!` 
are also supported. _Regular expressions_ can be matched using `=~` operator.

|Example|Description|
|---|---|
|`$..a[?(@.b == @.c)]`|Selects property `a` of any object that is object with properties `b` and `c` with equal values.|
|`$..a[?(@.b || (@.c <= 1))]`|Selects property `a` of any object that is object with either property `b` or property `c` with int/float value lesser or equal to `1`.|
|`$..a[?(@.b =~ /^b/i)]`|Selects property `a` of any object that is object with string property `b` that starts from `b` or `B`.|

### Original definition
Goessner described JSONPath grammar with providing a set of example queries on JSON sample. Here's his original 
data sample:
```json
{ "store": {
    "book": [ 
      { "category": "reference",
        "author": "Nigel Rees",
        "title": "Sayings of the Century",
        "price": 8.95
      },
      { "category": "fiction",
        "author": "Evelyn Waugh",
        "title": "Sword of Honour",
        "price": 12.99
      },
      { "category": "fiction",
        "author": "Herman Melville",
        "title": "Moby Dick",
        "isbn": "0-553-21311-3",
        "price": 8.99
      },
      { "category": "fiction",
        "author": "J. R. R. Tolkien",
        "title": "The Lord of the Rings",
        "isbn": "0-395-19395-8",
        "price": 22.99
      }
    ],
    "bicycle": {
      "color": "red",
      "price": 19.95
    }
  }
}
```
And here are his original example queries with result descriptions:

|Query|Result|Supported|Comments|
|---|---|---|---|
|`$.store.book[*].author`|The authors of all books in the store.|Yes| |
|`$..author`|All authors.|Yes| |
|`$.store.*`|All things in store, which are some books and a red bicycle.|Yes| |
|`$.store..price`|The price of everything in the store.|Yes| |
|`$..book[2]`|The third book.|Yes| |
|`$..book[(@.length-1)]`|The last book in order.|No|Original implementation uses _underlying script engine_ (JavaScript, in his case) in expressions. In case of PHP allowing to call arbitrary code from expression is unsafe, so script expressions are not implemented.|
|`$..book[-1:]`|The last book in order.|Yes| |
|`$..book[0,1]`|The first two books.|Yes| |
|`$..book[:2]`|The first two books.|Yes| |
|`$..book[?(@.isbn)]`|Filter all books with isbn number.|Yes| |
|`$..book[?(@.price<10)]`|Filter all books cheapier than 10.|Yes| |
|`$..*`|All members of JSON structure.|Yes| |
