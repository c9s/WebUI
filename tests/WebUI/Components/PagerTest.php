<?php
use WebUI\Components\Pager;

class PagerTest extends PHPUnit_Framework_TestCase
{
    public function testPager()
    {
        $pager = new Pager(1, 30);
        $pager->setBaseUrl('/product');
        $html = $pager->render();

        $dom = new DOMDocument('1.0');
        $dom->loadXml($html);
        $navs = $dom->getElementsByTagName('nav');
        is(1, $navs->length);

        $lis = $dom->getElementsByTagName('li');
        is(8, $lis->length);
    }
}

