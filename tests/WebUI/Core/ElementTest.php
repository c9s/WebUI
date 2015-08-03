<?php
use WebUI\Core\Element;

class ElementTest extends PHPUnit_Framework_TestCase
{
    public function test()
    {
        $element = new Element('ul', [ 
            'role' => 'menu'
        ]);
        $element->append(new Element('li'));
        $element->append(new Element('li'));
        $html = $element->formatRender();
        $this->assertNotNull($html);
    }
}

