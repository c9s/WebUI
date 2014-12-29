<?php
namespace WebUI\Components;
use WebUI\Core\Element;
use WebUI\Core\Div;

class Breadcrumbs extends Div
{
    protected $separator;

    protected $class = array('breadcrumbs');

    public function setSeparatorElement(Element $element) {
        $this->separator = $element;
    }

    public function setSeparatorHtml($html) {
        $this->separator = $html;
    }

    public function appendLink($label, $url, $title = NULL, array $attributes = array() )
    {
        $span = new Element('span');
        $span['itemscope'] = NULL;
        $span['itemtype'] = 'http://data-vocabulary.org/Breadcrumb';
        $span['role'] = 'presentation';

        $a = new Element('a');
        $a['title'] = $title ?: $label;
        $a['itemprop'] = 'url';
        $a['href'] = $url;

        # <span itemprop="title">Home</span>
        $title = new Element('span');
        $title->appendText($label);
        $title['itemprop'] = 'title';
        $a->addChild($title);

        $span->addChild($a);
        $this->addChild($span);
    }


    public function renderChildren()
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

        $html = '';
        foreach($this->children as $idx => $item) {
            if ($idx > 0) {
                // append separator
                $html .= "\n  " . $sep;
            }
            // append the link element
            $html .= "\n  " . $item->render();
        }
        return $html;
    }



}



