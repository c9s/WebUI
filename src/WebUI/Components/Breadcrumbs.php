<?php
namespace WebUI\Components;
use WebUI\Core\Element;

class Breadcrumbs
{
    protected $separator;

    public function setSeparatorElement(Element $element) {
        $this->separator = $element;
    }

}



