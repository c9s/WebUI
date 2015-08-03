<?php
namespace WebUI\Components\Menu;
use Exception;
use WebUI\Core\Element;
use WebUI\Components\Menu\MenuItemInterface;
use WebUI\Components\Menu\MenuItem;
use WebUI\Components\Menu\MenuFolder;

class Menu extends Element implements MenuItemInterface
{
    protected $classes = array('webui-menu');
    protected $menuItemCollections = array();

    public function __construct(array $attributes = array())
    {
        Element::__construct('nav', array_merge(array(
            "role" => "menubar",
            "itemscope" => NULL,
            "itemtype" => "http://schema.org/SiteNavigationElement",
        ), $attributes));
    }

    public function appendCollection($id = null, array $attributes = array())
    {
        $collection = new MenuItemCollection($id, $attributes);
        $this->menuItemCollections[] = $collection;
        return $collection;
    }

    public function render($attrs = array())
    {
        foreach( $this->menuItemCollections as $collection) {
            $this->append($collection);
        }
        return Element::render($attrs);
    }
}




