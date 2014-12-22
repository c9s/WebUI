<?php
namespace WebUI\Components;
use WebUI\Core\Element;
use WebUI\Core\Div;

class Breadcrumbs extends Div
{
    protected $separator;

    protected $class = array('breadcrumbs');

    protected $items = array();

    public function setSeparatorElement(Element $element) {
        $this->separator = $element;
    }

    public function setSeparatorHtml($html) {
        $this->separator = $html;
    }

    public function appendElement(Element $element)
    {
        $this->items[] = $element;
    }

    public function appendLink($label, $url, array $attributes = array() )
    {
        $element = new Element;
        $this->items[] = $element;
    }


    public function toHtml()
    {
        $sep = '';
        if ($this->separator instanceof Element) {
            $sep = $this->separator->render();
        } elseif (is_string($this->separator)) {
            $sep = $this->separator;
        } else {
            // the default separator
            $sep = '<span class="arrow-space">&#62;</span>';
        }

        foreach($this->items as $idx => $item) {
            if ($idx > 0) {
                // append separator
                $this->addChild($sep);
            }
            // append the link element

        }
    }


    public function __toString()
    {

    }



}



