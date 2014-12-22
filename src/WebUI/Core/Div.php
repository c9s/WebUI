<?php
namespace WebUI\Core;
use WebUI\Core\Element;

class Div extends Element
{
    public $tagName = 'div';
    public $closeEmpty = true;

    public function __construct($attributes = array() ) { 
        parent::__construct($this->tagName, $attributes);
    }
}



