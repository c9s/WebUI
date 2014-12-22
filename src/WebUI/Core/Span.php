<?php
namespace WebUI\Core;
use WebUI\Core\Element;

class Span extends Element
{
    public $tagName = 'span';
    public $closeEmpty = true;

    public function __construct($attributes = array()) {
        parent::__construct($this->tagName, $attributes);
    }
}



