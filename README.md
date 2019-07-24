# PHP JSONPath
[![Latest Stable Version](https://poser.pugx.org/remorhaz/php-json-path/version)](https://packagist.org/packages/remorhaz/php-json-path)
[![Build Status](https://travis-ci.org/remorhaz/php-json-path.svg?branch=master)](https://travis-ci.org/remorhaz/php-json-path)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/remorhaz/php-json-path/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/remorhaz/php-json-path/?branch=master)
[![codecov](https://codecov.io/gh/remorhaz/php-json-path/branch/master/graph/badge.svg)](https://codecov.io/gh/remorhaz/php-json-path)
[![Infection MSI](https://badge.stryker-mutator.io/github.com/remorhaz/php-json-path/master)](https://infection.github.io)
[![License](https://poser.pugx.org/remorhaz/php-json-path/license)](https://packagist.org/packages/remorhaz/php-json-path)

JSONPath is a simple query language for JSON documents, inspired by XPath for XML and originally designed by [Stefan Goessner](https://goessner.net/articles/JsonPath/).

## Requirements

- PHP 7.3+
- [JSON extension](https://www.php.net/manual/en/book.json.php) (ext-json)
- [Internationalization functions](https://www.php.net/manual/en/book.intl.php) (ext-intl)

## Installation
You can use Composer to install this package:
```
composer require remorhaz/php-json-path
```

## Example
```php
use Remorhaz\JSON\Data\Value\DecodedJson;
use Remorhaz\JSON\Data\Value\EncodedJson;
use Remorhaz\JSON\Path\Processor\Processor;
use Remorhaz\JSON\Path\Query\QueryFactory;

// Creating processor and query factory:
$processor = Processor::create();
$queryFactory = QueryFactory::create();

// Creating query that finds all 'a' properties:
$query1 = $queryFactory->createQuery('$..a');

// Creating JSON document from JSON string:
$json1 = '{"a":1,"b":{"a":2}}';
$jsonDocument1 = EncodedJson\NodeValueFactory::create()->createValue($json1);

// Applying query to document and getting result as decoded JSON.
$result1 = $processor
    ->select($query1, $jsonDocument1)
    ->decode(); 
// $result1 now contains array of integers: [1, 2]

// Creating JSON document from decoded JSON data:
$json2 = (object) ['a' => (object) ['a' => 1, 'b' => 2]];
$jsonDocument2 = DecodedJson\NodeValueFactory::create()->createValue($json2);

// Applying same query to new document and getting result as encoded JSON:
$result2 = $processor
    ->select($query1, $jsonDocument2)
    ->encode();
// $result2 now contains array of JSON strings:
//     ['{"a":1,"b":2}', '1']

// Creating another query:
$query2 = $queryFactory->createQuery('$..a[?(@.b=2)]');

// Applying new query to same data and getting result as encoded JSON
$result3 = $processor
    ->select($query2, $jsonDocument2)
    ->encode();
// $result3 now contains array of JSON strings:
//     ['{"a":1,"b":2}']
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
|`$[*]`|Selecta all children. Same as `$.*`.|

### Filter expressions
When filter is being applied to nodeset, it leaves only those nodes for which the expression evaluates to true.

|Example|Description|
|---|---|
|`$..a[?(@.b]`|Selects all properties `a` that contain objects with property `b`.|
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
