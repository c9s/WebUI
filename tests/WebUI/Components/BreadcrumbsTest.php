<?php
use WebUI\Components\Breadcrumbs;
use WebUI\Core\Element;

class BreadcrumbsTest extends PHPUnit_Framework_TestCase
{
    public function testOutput()
    {
        $el = new Element('span');
        $el->append('&#62;');
        $el->addClass('separator');

        $breadcrumbs = new Breadcrumbs;
        ok($breadcrumbs);

        $breadcrumbs->setSeparatorElement($el);

        $breadcrumbs->appendIndexLink('Home', '/', 'The Home Page');

        $breadcrumbs->appendLink('Product', '/product', 'All Products');

        $breadcrumbs->appendLink('Product A123', '/product/a123', 'Product A123');

        ok($html = $breadcrumbs->render());
        // echo $html, "\n\n";
    }
}

