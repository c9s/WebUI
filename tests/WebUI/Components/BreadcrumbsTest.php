<?php
use WebUI\Components\Breadcrumbs;
use WebUI\Core\Element;

class BreadcrumbsTest extends PHPUnit_Framework_TestCase
{
    public function testOutput()
    {
        $el = new Element('span');
        $el->append('&#62;');
        $el->addClass('arrow-space');

        $breadcrumbs = new Breadcrumbs;
        ok($breadcrumbs);

        $breadcrumbs->setSeparatorElement($el);

        $breadcrumbs->appendLink('Home', '/', 'The Home Page');

        $breadcrumbs->appendLink('Product', '/', 'All Products');

        ok($html = $breadcrumbs->render());
        var_dump( $html ); 
    }
}

