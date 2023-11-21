# ODM Search Param

## Rationale
- you use [Doctrine MongoDB ODM](https://github.com/doctrine/mongodb-odm)
- you need some **search** functionality, that can be expressed through the `QueryBuilder` or the `MatchPhase` of the Aggregation Builder
- you have a class that holds the search parameters (i.e. a domain class in Symfony)
- you don't want to manually map the search parameters to builder statements

## Usage
1. Search filter class can be any class with **public** properties
2. Properties should be marked with `#[SearchParam]` attribute
3. Use the `SearchParamParser::parse` with a `QueryBuilder|MatchStage` instance to build the query on

## Examples
### Verbatim (`String` and `Bool` types)
```php
class SearchFilter
{
    #[SearchParam(type: SearchParamType::String)]
    public string $name;
}
```
Builds the query to:
```php
$builder->field('name')->equals($propertyValue);
```

### Using different property names
```php
class SearchFilter
{
    #[SearchParam(type: SearchParamType::Exists, field: 'images')]
    public bool $hasImages;
}
```
Builds the query to:
```php
$builder->field('images')->exists(true);
```

### Type casting (`Int` type)
```php
class SearchFilter
{
    #[SearchParam(type: SearchParamType::Int)]
    public $age;
}
```
Builds the query to:
```php
$builder->field('age')->equals((int) $propertyValue);
```

### Type casting with arrays (`IntArray` and `StringArray` types)
```php
class SearchFilter
{
    /**
    * @var int[] 
    */
    #[SearchParam(type: SearchParamType::IntArray)]
    public array $grades;
}
```
Builds the query to:
```php
$builder->field('grades')->in($propertyValuesAllCastedToInt);
```

### Using backing values from enums (`StringEnum` and `IntEnum` types)
```php
class SearchFilter
{
    #[SearchParam(type: SearchParamType::StringEnum)]
    public SideOfTheWorldEnum $sideOfTheWorld;
}
```
Builds the query to:
```php
$builder->field('sideOfTheWorld')->equals($propertyValue->value);
```

### Using backing values from enum arrays (`StringEnumArray` and `IntEnumArray` types)
```php
class SearchFilter
{
    /**
    * @var SideOfTheWorldEnum[]
    */
    #[SearchParam(type: SearchParamType::StringEnumArray)]
    public array $sidesOfTheWorld;
}
```
Builds the query to:
```php
$builder->field('sideOfTheWorld')->in($backingValuesOfPropertyValue);
```

### Querying by range (`RangeInt`, `RangeInt` and `RangeFloat` types)
```php
class SearchFilter
{
    #[SearchParam(type: SearchParamType::RangeInt, direction: SearchParamDirection::From)]
    public int $price;
}
```
Builds the query to:
```php
$builder->field('price')->gte((int) $propertyValue);
```

### Custom query building by specifying the callable
```php
class SearchFilter
{
    #[SearchParam(type: SearchParamType::Callable, callable: [SomeClass::class, 'setStatus'])]
    public string $status;
}

class SomeClass
{
    public static function setStatus(Builder|MatchStage $builder, $value, $filter)
    {
    // Call $builder methods to build the query
    }
}
```