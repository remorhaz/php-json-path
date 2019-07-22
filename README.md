# PHP JSONPath
[![License](https://poser.pugx.org/remorhaz/php-json-path/license)](https://packagist.org/packages/remorhaz/php-json-path)
[![Build Status](https://travis-ci.org/remorhaz/php-json-path.svg?branch=master)](https://travis-ci.org/remorhaz/php-json-path)
[![Latest Stable Version](https://poser.pugx.org/remorhaz/php-json-path/version)](https://packagist.org/packages/remorhaz/php-json-path)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/remorhaz/php-json-path/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/remorhaz/php-json-path/?branch=master)
[![codecov](https://codecov.io/gh/remorhaz/php-json-path/branch/master/graph/badge.svg)](https://codecov.io/gh/remorhaz/php-json-path)

JSONPath is a simple query language for JSON documents, inspired by XPath for XML and originally designed by [Stefan Goessner](https://goessner.net/articles/JsonPath/).

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
    ->asJson();
// $result2 now contains array of JSON strings:
//     ['{"a":1,"b":2}', '1']

// Creating another query:
$query2 = $queryFactory->createQuery('$..a[?(@b=2)]');

// Applying new query to same data and getting result as encoded JSON
$result3 = $processor
    ->select($query2, $jsonDocument2)
    ->asJson();
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
|`*`|Wildcard that matches any property of current object / index of current array.|
|`[?(<expression>)]`|Filters values.|

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
|`$..book[(@.length-1)]`|The last book in order.|No|Original implementation uses _underlying script engine_ in expressions. This behaviour can break interoperability, so expressions are not implemented.|
|`$..book[-1:]`|The last book in order.|Yes| |
|`$..book[0,1]`|The first two books.|Yes| |
|`$..book[:2]`|The first two books.|Yes| |
|`$..book[?(@.isbn)]`|Filter all books with isbn number.|Yes| |
|`$..book[?(@.price<10)]`|Filter all books cheapier than 10.|Yes| |
|`$..*`|All members of JSON structure.|Yes| |
