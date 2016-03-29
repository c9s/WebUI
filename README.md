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

The code above renders the html below:

```html
<div class="react-component react-app" id="CRUDListApp56faad9210df6"></div>
<script>
document.addEventListener('load', function(evt) {
var app56faad9210e79 = React.createElement(CRUDListApp,{
    "prop1": "setting"
});
React.render(app56faad9210e79, document.getElementById('CRUDListApp56faad9210df6'));
});
</script>
```




