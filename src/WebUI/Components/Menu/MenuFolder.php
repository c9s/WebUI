<?php
namespace WebUI\Components\Menu;
use Exception;
use WebUI\Core\Element;
use WebUI\Components\Menu\MenuItemInterface;
use WebUI\Components\Menu\MenuItem;
use WebUI\Components\Menu\MenuItemCollection;
use BadMethodCallException;

/**
 * The top level menu container
 */
class MenuFolder extends Element implements MenuItemInterface
{
    protected $label;

    protected $linkAttributes = array();

    //protected $menuItems = array();
    protected $menuItemCollection;

    public function __construct($label, array $attributes = array())
    {
        $this->setLabel($label);
        parent::__construct('li', array_merge(array(
            "role" => "menuitem",
            "itemprop" => "itemListElement",
            "itemtype" => "http://schema.org/ItemList",
        ), $attributes));

        $this->menuItemCollection = new MenuItemCollection;
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

    public function __call($method, $args) {
        if (method_exists($this->menuItemCollection, $method)) {
            return call_user_func_array(array($this->menuItemCollection, $method), $args);
        } else {
            throw new BadMethodCallException;
        }
    }

    public function render($attrs = array())
    {
        // create a wrapper div
        // <div itemscope itemtype="http://schema.org/SiteNavigationElement">
        if (!$this->label) {
            throw new Exception('Missing menu label');
        }

        // create label with tag "a"
        $a = new Element('a');
        $a->appendText($this->label);
        $this->append($a);

        $this->append($this->menuItemCollection);

        return parent::render($attrs);
    }

}




