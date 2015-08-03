<?php
namespace WebUI\Components\Menu;
use Exception;
use WebUI\Core\Element;
use WebUI\Components\Menu\MenuItemInterface;
use WebUI\Components\Menu\MenuItem;

class MenuItemCollection extends Element implements MenuItemInterface
{
    // MenuItem, MenuFolder
    protected $menuItems = array();

    public function __construct($id = null, array $attributes = array())
    {
        Element::__construct('ul', array_merge(array(
            "role" => "menu",
            "itemscope" => NULL,
            "itemtype" => "http://schema.org/ItemList",
        ), $attributes));

        $this->setId( $id ?: crc32(microtime()) );
    }

    public function render($attrs = array())
    {
        $this->setAttributeValue('role', 'menu');
        $this->setAttributes(array(
            'itemscope' => null,
            'itemtype' => "http://schema.org/ItemList",
        ));

        foreach ($this->menuItems as $item) {
            $this->append($item);
        }
        return Element::render($attrs);
    }

    public function appendLink($label, array $linkAttributes = array(), array $attributes = array()) {
        $item = new MenuItem($label, $attributes);
        $item->setLinkAttributes($linkAttributes);
        $this->addMenuItem($item);
        return $item;
    }

    public function appendFolder($label, array $attributes = array()) {
        $folder = new MenuFolder($attributes);
        $folder->setLabel($label);
        $this->addMenuItem($folder);
        return $folder;
    }

    public function addMenuItem(MenuItemInterface $item)
    {
        $this->menuItems[] = $item;
    }

    public function getMenuItemByIndex($index)
    {
        if (isset($this->menuItems[ $index ])) {
            return $this->menuItems[ $index ];
        }
    }

    public function removeMenuItemByIndex($index)
    {
        return array_splice($this->menuItems, $index, 1);
    }
}
