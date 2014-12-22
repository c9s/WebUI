<?php
use WebUI\Components\Breadcrumbs;
use WebUI\Core\Element;

class BreadcrumbsTest extends PHPUnit_Framework_TestCase
{
    public function testOutput()
    {
        $el = new Element('span');

        $breadcrumbs = new Breadcrumbs;
        ok($breadcrumbs);

        $breadcrumbs->setSeparatorElement($el);

        ok($breadcrumbs->render());
    }
}

