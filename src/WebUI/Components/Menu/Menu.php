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
class Menu extends Element implements MenuItemInterface, IdentityFinder
{
    protected $classes = array('webui-menu');

    protected $menuItemCollections = array();

    public function __construct(array $attributes = array())
    {
        Element::__construct('nav', array_merge(array(
            "role" => "menubar",
            "itemscope" => NULL,
            "itemtype" => "http://schema.org/SiteNavigationElement",
            "class" => $this->classes,
        ), $attributes));
    }

    public function appendCollection(array $attributes = array(), $identity = null)
    {
        $collection = new MenuItemCollection($attributes, $identity);
        $this->menuItemCollections[] = $collection;
        return $collection;
    }

    public function findById($identity)
    {
        foreach ($this->menuItemCollections as $collection) {
            if ($collection instanceof IdentityFinder) {
                if ($result = $collection->findById($identity)) {
                    return $result;
                }
            } else if ( $collection->getIdentity() === $identity ) {
                return $collection;
            }
        }
    }

    public function render($attrs = array())
    {
        foreach( $this->menuItemCollections as $collection) {
            $this->append($collection);
        }
        return Element::render($attrs);
    }
}




