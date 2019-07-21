# PHP JSONPath
[![License](https://poser.pugx.org/remorhaz/php-json-path/license)](https://packagist.org/packages/remorhaz/php-json-path)
[![Build Status](https://travis-ci.org/remorhaz/php-json-path.svg?branch=master)](https://travis-ci.org/remorhaz/php-json-path)
[![Latest Stable Version](https://poser.pugx.org/remorhaz/php-json-path/version)](https://packagist.org/packages/remorhaz/php-json-path)
[![Maintainability](https://api.codeclimate.com/v1/badges/b905555b5d1fbdc6cc91/maintainability)](https://codeclimate.com/github/remorhaz/php-json-path/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/b905555b5d1fbdc6cc91/test_coverage)](https://codeclimate.com/github/remorhaz/php-json-path/test_coverage)

JSONPath is a simple query language for JSON documents, inspired by XPath for XML and originally designed by [Stefan Goessner](https://goessner.net/articles/JsonPath/).

## Installation
You can use Composer to install this package:
```
composer require remorhaz/php-json-path
```

## Grammar
To be written.

## Example
Let's use original Goessner's example JSON data:
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
To fetch all the books' authors we will use the following query: `$.store.book[*].author`.
```php
use Remorhaz\JSON\Data\Value\DecodedJson\NodeValueFactory;
use Remorhaz\JSON\Path\Processor\Processor;
use Remorhaz\JSON\Path\Query\QueryFactory;

$json = '...'; // JSON string

// Loading JSON document.
$decodedJson = json_decode($json); // raw JSON is not supported yet
$jsonDocument = (new NodeValueFactory)->createValue($decodedJson);

// Creating query. It can be applied multiple times to different data.
$query = QueryFactory::create()->createQuery('$.store.book[*].author');

// Applying query to document.
$result = Processor::create()->select($query, $jsonDocument);

// Now $authors will contain array of all books' authors:
// ['Nigel Rees', 'Evelyn Waugh', 'Herman Melville', 'J. R. R. Tolkien']
$authors = $result->decode();
```
