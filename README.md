WebUI
=========================

WebUI aims to provide a PHP interface to build HTML components with microdata.



Synopsis
-------------------------


```php
$el = new Element('span');
$el->append('&#62;');
$el->addClass('separator');

$breadcrumbs = new Breadcrumbs;

$breadcrumbs->setSeparatorElement($el);

$breadcrumbs->appendLink('Home', '/', 'The Home Page');
$breadcrumbs->appendLink('Product', '/', 'All Products');
$html = $breadcrumbs->render();
```

And we will get:

```html
<div class="breadcrumbs">
  <span itemscope itemtype="http://data-vocabulary.org/Breadcrumb">
    <a title="The Home Page" itemprop="url" href="/">
      <span itemprop="title">Home</span>
    </a>
  </span>
  <span class="separator">&#62;</span>
  <span itemscope itemtype="http://data-vocabulary.org/Breadcrumb">
    <a title="All Products" itemprop="url" href="/">
      <span itemprop="title">Product</span>
    </a>
  </span>
</div>
```


Components
----------------------


## ReactComponent

Rendering ReactComponent initializer from PHP settings:

```php
$component = new ReactComponent('CRUDListApp', array( 'prop1' => 'setting' ));
$out = $component->render();
```




