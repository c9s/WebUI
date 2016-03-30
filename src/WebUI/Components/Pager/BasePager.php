<?php
namespace WebUI\Components\Pager;

class BasePager
{
    public static function calculatePages($numberOfTotalItems, $pageSize = 10)
    {
        return $numberOfTotalItems > 0 ? ceil($numberOfTotalItems / $pageSize) : 1;
    }
}




