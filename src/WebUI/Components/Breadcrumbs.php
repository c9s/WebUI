<?php
namespace WebUI\Components;
use WebUI\Core\Element;
use WebUI\Core\Div;

class Breadcrumbs extends Element
{
    protected $separator;

    protected $class = array('breadcrumbs');

    public function __construct($attrs = array()) 
    {
        parent::__construct('ul', $attrs);
    }


    public function setSeparatorElement(Element $element) {
        $this->separator = $element;
    }

    public function setSeparatorHtml($html) {
        $this->separator = $html;
    }

    public function appendIndexLink($label, $url, $title = NULL, array $attributes = array())
    {
        $span = $this->appendLink($label, $url, $title, $attributes);
        $span->getChildAt(0)->setAttributeValue('rel','index');
    }


    /**
     * Append a link "a" element and add "span" element wrapper with microdata automatically.
     *
     * @param string $label The label of that link.
     * @param string $url   The url of that link.
     * @param string $title The title attribute of the link.
     * @param array $attributes The attributes of the link.
     */
    public function appendLink($label, $url, $title = NULL, array $attributes = array() )
    {
        $span = new Element('span');
        $span['itemscope'] = NULL;
        $span['itemtype'] = 'http://data-vocabulary.org/Breadcrumb';
        $span['role'] = 'presentation';

        $a = new Element('a', $attributes);
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
        return $span;
    }

    public function render($attributes = array()) 
    {
        // <nav aria-label="breadcrumb" role="navigation">
        $nav = new Element('nav', $attributes);
        $nav->setAttributeValue('aria-label','Breadcrumbs');
        $nav->setAttributeValue('role','navigation');
        $nav->addChild(parent::render());
        return $nav->render();
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



