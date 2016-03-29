<?php
use WebUI\Components\React\ReactComponent;

class ReactComponentTest extends PHPUnit_Framework_TestCase
{
    public function testReactComponent()
    {
        $component = new ReactComponent('CRUDListApp', array( 'prop1' => 'setting' ));
        $out = $component->render();
        echo $out;
    }
}
