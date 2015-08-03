<?php
namespace WebUI\Components\Menu;
use Exception;
use WebUI\Core\Element;
use WebUI\Components\Menu\MenuItemInterface;
use WebUI\Components\Menu\MenuItem;

/**
 * The top level menu container
 */
class MenuItem extends Element implements MenuItemInterface
{
    protected $label;

    protected $linkAttributes = array();

    public function __construct($label, array $attributes = array())
    {
        $this->setLabel($label);
        parent::__construct('li', array_merge(array(
            "role" => "presentation",
            "itemprop" => "itemListElement",
            "itemscope" => NULL,
        ), $attributes));
    }

    public function setLabel($label)
    {
        $this->label = $label;
    }

    public function setLinkAttributes(array $attributes)
    {
        $this->linkAttributes = $attributes;
    }

    public function setLink($label, array $attributes = array()) {
        $this->label = $label;
        $this->linkAttributes = $attributes;
    }

    public function render($attributes = array())
    {
        if (!$this->label) {
            throw new Exception('Missing menu label');
        }

        // create label with tag "a"
        $a = new Element('a', $this->linkAttributes);
        $a->setAttributeValue("role","menuitem");
        $a->appendText($this->label);
        $this->append($a);
        return parent::render($attributes);
    }
}
