<?php
use WebUI\Components\Menu\MenuFolder;
use WebUI\Components\Menu\MenuItem;
use WebUI\Components\Menu\Menu;

class MenuTest extends PHPUnit_Framework_TestCase
{

    public function testMenuItem()
    {
        $item = new MenuItem('Product Collection');
        $item->setTitle('Product Collection');
        $item->setLink('Product Collection', [
            'href' => '/products',
            'data-target' => '#productContainer',
            'dataContainer' => 'body'
        ]);
        ok('<li role="menuitem" itemprop="itemListElement"><a href="/products" data-target="#productContainer" data-container="body">Product Collection</a></li>',$item->render());
    }


    public function testMenuFolder()
    {
        $menu = new MenuFolder('Products');
        $item1 = $menu->appendLink('Car', [ 'href' => '/products/car' ]);
        ok($item1);
        $item2 = $menu->appendLink('Bicycle', [ 'href' => '/products/bicycle' ]);
        ok($item2);
        $item3 = $menu->appendLink('Truck', [ 'href' => '/products/truck' ]);
        ok($item3);

        $item4 = $menu->appendLink('Others', [ 'href' => '/products/others' ]);
        ok($item4);
        $html = $menu->render();
        // var_dump( $html );
        // file_put_contents('test.html', '<html><head><style> </style></head><body>' . $html . '</body></html>');
    }

    public function testMenu()
    {
        $menu = new Menu;
        $menu->appendLink('Car', ['href' => '/products/car']);
        $menu->appendLink('Bicycle', ['href' => '/products/bicycle']);
        $folder = $menu->appendFolder('Others');
        $folder->appendLink('A',  [ 'href' => '/products/a']);
        $folder->appendLink('B',  [ 'href' => '/products/b']);
        $html = $menu->render();
        // echo $html;
        file_put_contents('test.html', '<html><head><style> </style></head><body>' . $html . '</body></html>');
    }


}

