<?php
namespace WebUI\Components\Menu;
use Exception;
use WebUI\Core\Element;
use WebUI\Components\Menu\MenuItemInterface;
use WebUI\Components\Menu\MenuItem;
use WebUI\Components\Menu\MenuFolder;


/**
 * <nav>
 *
 * </nav>
 */
class Menu extends MenuFolder
{
    protected $ul;

    protected $classes = array('webui-menu');

    public function __construct(array $attributes = array())
    {
        Element::__construct('nav', array_merge(array(
            "role" => "menubar",
            "itemscope" => NULL,
            "itemtype" => "http://schema.org/SiteNavigationElement",
        ), $attributes));

        // $this->collection = new MenuItemCollection;

        $this->ul = new Element('ul');
        $this->ul->setAttributeValue('role', 'menu');
        $this->ul->setAttributes(array(
            'itemscope' => NULL,
            'itemtype' => "http://schema.org/ItemList",
        ));
        $this->append($this->ul);
    }

    public function getMenuItemCollection()
    {
        return $this->menuItemCollection;
    }

    public function render($attrs = array())
    {
        foreach ($this->menuItems as $item) {
            $this->ul->append($item);
        }
        return Element::render($attrs);
    }
}




