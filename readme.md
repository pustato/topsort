# Topological Sort in PHP

Simple [Topological Sorting](https://en.wikipedia.org/wiki/Topological_sorting "Wikipedia Link") (aka Dependency Resolution) algorithm implementation in PHP.

## Example usage

Implement interface `\Pustato\TopSort\Contracts\Sortable`:
```php
// in SiteAsset.php
class SiteAsset implements Pustato\TopSort\Contracts\Sortable
{
    
    public function getId()
    {
        return static::class;
    }
    
    public function getDependencies()
    {
        return [
            JQueryAsset::class, BootstrapAsset::class
        ];
    }
    
    ...
    // Asset implementation
}
```

```php
// in BootstrapAsset.php
class BootstrapAsset implements Pustato\TopSort\Contracts\Sortable 
{
    
    public function getId()
    {
        return static::class;
    }
    
    public function getDependencies()
    {
        return [
            JQueryAsset::class
        ];
    }
    
    ...
    // Asset implementation
}
```

```php
// in JQueryAsset.php
class JQueryAsset implements Pustato\TopSort\Contracts\Sortable 
{
    
    public function getId()
    {
        return static::class;
    }
    
    public function getDependencies()
    {
        return [];
    }
    
    ...
    // Asset implementation
}
```

Add all sortable classes to `\Pustato\TopSort\Collection` and sort them:

```php
$assetsCollection = new \Pustato\TopSort\Collection([
    new SiteAsset(), new JQueryAsset(), new BootstrapAsset()
]);
$result = $assetsCollection->getSorted();
var_dump($result);
```

And you will get:
```
array(3) {
  [0] => class JQueryAsset#1 (0) {}
  [1] => class BootstrapAsset#2 (0) {}
  [2] => class SiteAsset#3 (0) {}
}
```