# OdmSearchParam
## Make your search filter class generate the query for you

### `@SearchParam` annotation
is for you if..
* you use Doctrine MongoDB ODM
* you need some search functionality, that can be expressed through the [ODM Query Builder](https://www.doctrine-project.org/projects/doctrine-mongodb-odm/en/1.2/reference/query-builder-api.html#finding-documents)
* you have a class that holds the search parameters (i.e. a domain class in Symfony)
* you don't want to manually map the search parameters to query builder statements

### Search Filter class
1. Can be any class with `public` properties [<sup>1</sup>](#footnote1)
2. Properties should be annotated with [`@SearchParam`](src/SearchParam.php) annotation
3. Should use the [`SearchParamParseable`](src/SearchParamParseable.php) trait
4. Shold call `parseSearchParam` with a `QueryBuilder` instance to build the query on

### Examples
#### Verbatim (`string` and `bool` types)
```php
class SearchFilter {
  /**
  * @SearchParam(type="string")
  * @var string
  */
  public $name;
}
// Builds the query to..
$queryBuilder->field('name')->equals($propertyValue);
```

#### Using different property names
```php
class SearchFilter {
  /**
  * @SearchParam(type="exists", field="images")
  * @var bool
  */
  public $hasImages;
}
// Builds the query to..
$queryBuilder->field('images')->exists(true);
```

#### Type casting (`int` type) [<sup>2</sup>](#footnote2)
```php
class SearchFilter {
  /**
  * @SearchParam(type="int")
  * @var int
  */
  public $age;
}
// Builds the query to..
$queryBuilder->field('age')->equals((int) $propertyValue);
```

#### Arrays + optional casting (`string_array` and `int_array` types)
```php
class SearchFilter {
  /**
  * @SearchParam(type="int_array")
  * @var int[]
  */
  public $grades;
}
// Builds the query to..
$queryBuilder->field('grades')->in($propertyValuesAllCastedToInt);
```


#### Querying by range (`range_int` and `range_float` types) [<sup>3</sup>](#footnote3)
```php
class SearchFilter {
  /**
  * @SearchParam(type="range_int", direction="from")
  * @var int
  */
  public $price;
}
// Builds the query to..
$queryBuilder->field('price')->gte((int) $propertyValue);
```
#### Custom query building by specifying the filter's method as a callback [<sup>4</sup>](#footnote4)
```php
class SearchFilter {
  /**
  * @SearchParam(callback="setStatus")
  * @var string
  */
  public $status;
  
  public function setStatus($queryBuilder, $propertyValue){
    // modify $queryBuilder based on $propertyValue
  }
}
// Calls the..
$searchFilter->setStatus($queryBuilder, $propertyValue);
```

## TODO
- Guess property type automatically (PHP7.4 property type, DocBlock annotations...)
- Allow passing real PHP callables to as callbacks
- Allow method annotating (getters etc.)
- Add `float` type
- Allow choosing between inclusive and exclusive (`gt` or `gte`, `lt` or `lte`) queries for `range` type
