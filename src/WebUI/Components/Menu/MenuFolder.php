<?php
namespace WebUI\Components\Menu;
use Exception;
use WebUI\Core\Element;
use WebUI\Components\Menu\MenuItemInterface;
use WebUI\Components\Menu\MenuItem;

/**
 * The top level menu container
 *
 *  <li class="has-children">
 *     <a href="/...">Folder</a>
 *     <ul> 
 *       ... menu items...
 *     </ul>
 *  </li>
 *
 */
class MenuFolder extends Element implements MenuItemInterface
{
    static $defaultMenuClasses = [ 'nav' ];

    protected $label;

    protected $linkAttributes = array();

    protected $menuItems;

    public function __construct($label, array $attributes = array())
    {
        $this->setLabel($label);
        parent::__construct('li', array_merge(array(
            "role" => "menuitem",
            "itemprop" => "itemListElement",
            "itemtype" => "http://schema.org/ItemList",
        ), $attributes));

        // $this->collection = new MenuItemCollection;

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

    public function appendLink($label, array $linkAttributes = array(), array $attributes = array()) {
        $item = new MenuItem($label, $attributes);
        $item->setLinkAttributes($linkAttributes);
        $this->addMenuItem($item);
        return $item;
    }

    public function appendFolder($label, array $attributes = array()) {
        $folder = new self($attributes);
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

        $ul = new Element('ul');
        $ul->setAttributeValue('role', 'menu');
        $ul->setAttributes(array(
            'itemscope' => null,
            'itemtype' => "http://schema.org/ItemList",
        ));
        $this->append($ul);

        foreach( $this->menuItems as $item) {
            $ul->append($item);
        }
        return parent::render($attrs);
    }

}




