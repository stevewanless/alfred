alfred
======

Alfred is a connivence Service Layer between your Codeigniter controllers and Datamapper (http://datamapper.wanwizard.eu) models.

When you need to interact with a model, just let Alfred handle it. Tell him the model your after and he will do the rest. 

For example, if your model was named Foo, and you wanted to get the record with ID = 3;

```php
  $foo = $this->alfred->foo->get('3');
```

But want if you wanted to get more specific, Alfred can do that too;

```php
  $foo = $this->alfred->foo->get(array(
    'where' => array(
      'name' => 'Alfred',
      'status' => 'active'
    ),
    'limit' => 1
  ));
```

Saving, yep, Alfred can do that too;

```php
  $array = array(
    'name' => 'This is only a test',
    'active' => 'active'
  );
  $foo = $this->alfred->foo->set($array); 
```    

Perhaps you find yourself querying for the same thing in different parts of your application, like 'Latest Events'. Alfred can also help with that. 

The Alfred class comes with the basic getters and setters but you can also define your own per model. 

```php
  $latest = $this->alfred->event->getLatest(1);
```

All you need to do is create a class in the alfred directory that extends 'Alfred_base'. Here you could also override the base getters and setters if you needed to.

```php
Alfred_event extends Alfred_base {
  
  /*
   * Write custom functions for specific things. 
   */
   
  public function getLatest($limit = 3) {

    $model = $this->get(array(
      'order_by' => array(
        'id' => 'DESC'      
      ),
      'limit' => $limit
    ));
    
    return $model;
    
  }

}
```

### installation

copy the alfred directory to the application/libraries/ then remember to load this library. 

alfred/alfred_foo.php is an example for how you can extend alfred with custom functions.


### dependencies 

The current version is built to work with Codeigniter and the Datamapper ORM. Although it could work with lots more without much effort. 



Please let me know if you have any ideas for improvements.