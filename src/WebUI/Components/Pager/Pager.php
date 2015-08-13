<?php
namespace WebUI\Components\Pager;
use WebUI\Core\Div;
use WebUI\Core\Element;

/**
 * A simple pager, does not depends on Pager interface.
 *
 * @version 2
 */
class Pager extends Element
{
    public $firstPageLabel;
    public $lastPageLabel;
    public $nextPageLabel;
    public $prevPageLabel;

    public $showHeader = false;
    public $showNavigator = true;
    public $showNearbyPages = true;
    public $whenOverflow  = true;

    protected $navWrapper = true;

    protected $baseUrl;

    protected $class = array('pagination');

    public $rangeLimit = 3;

    public $totalPages = 0;
    public $currentPage = 1;

    /**
     *
     *
     * @param integer current page
     * @param integer total size
     * @param integer page size (optional)
     */
    public function __construct($currentPage, $totalPages)
    {
        $this->firstPageLabel = '&#171;';
        $this->lastPageLabel  = '&#187;';
        $this->nextPageLabel  = '&#8250;';
        $this->prevPageLabel  = '&#8249;';

        $this->currentPage = $currentPage;
        $this->totalPages = $totalPages;
        parent::__construct('ul');
    }

    public function setBaseUrl($url)
    {
        $this->baseUrl = $url;
    }

    public function setFirstPageLabel($text)
    {
        $this->firstPageLabel = $text;
    }

    public function setLastPageLabel($text)
    {
        $this->lastPageLabel = $text;
    }

    public function setNextPagetext($text)
    {
        $this->nextPageLabel = $text;
    }

    public function setPrevPageLabel($text)
    {
        $this->prevPageLabel = $text;
    }

    /**
     * @param integer $total
     * @param integer $size  = null  (optional)
     */
    public function calculatePages($total, $pageSize)
    {
        $this->totalPages = $total > 0 ? (int) ceil($total / $pageSize ) : 0;
    }

    public function buildQuery( $origParams , $params = array() )
    {
        $params = array_merge(  $origParams , $params );
        return $this->baseUrl . '?' . http_build_query($params);
    }

    public function __toString()
    {
        return $this->render();
    }

    protected function appendPageOmitNavItem($text = '...')
    {
        $li = new Element('li');
        $a = new Element('a');
        $a->appendTo($li);
        $a->setInnerHtml($text);
        return $li;
    }

    public function appendNavItem($text, $page, $active = false, $disabled = false) {
        $li = new Element('li');
        $a = new Element('a');
        $a->appendTo($li);
        $a->setInnerHtml($text);

        $href = $this->buildQuery($_REQUEST, array("page" => $page));
        $a->setAttributeValue('href', $href);

        $li->setAttributeValue('role', 'presentation');
        if ($disabled) {
            $li->addClass('disabled');
            $a->setAttributeValue('aria-disabled','true');
        }

        if ($active) {
            $li->addClass('active');
            $li->addClass('current');
        }
        $this->addChild($li);
        return $li;
    }

    public function render($attributes = array())
    {
        $cur = $this->currentPage;
        $totalPages = $this->totalPages;

        if ($this->whenOverflow && $this->totalPages <= 1) {
            return "";
        }

        $pageStart = $cur > $this->rangeLimit ? $cur - $this->rangeLimit : 1 ;
        $pageEnd   = $cur + $this->rangeLimit < $totalPages ?  $cur + $this->rangeLimit : $totalPages;

        // Create inner elements and append to children element list.
        if ($this->showNavigator) {
            if ($cur > 2) {
                $li = $this->appendNavItem($this->firstPageLabel, 1, $cur == 1, $cur == 1);
                $li->getChildAt(0)->setAttributeValue('rel','first');
            }
            $li = $this->appendNavItem($this->prevPageLabel, $cur - 1, false, $cur == 1);
            $li->getChildAt(0)->setAttributeValue('rel','prev');
        }

        if ($this->showNearbyPages) {
            if ($cur > 5) {
                $this->appendNavItem(1, 1);
                $this->appendPageOmitNavItem('...');
            }

            for ($i = $pageStart ; $i <= $pageEnd ; $i++) {
                $this->appendNavItem($i, $i, $cur == $i);
            }

            if ($cur + 5 < $totalPages) {
                $this->appendPageOmitNavItem('...');
                $this->appendNavItem($totalPages, $totalPages);
            }
        }

        if ($this->showNavigator) {
            $li = $this->appendNavItem($this->nextPageLabel, $cur + 1, $cur >= $totalPages);
            $li->getChildAt(0)->setAttributeValue('rel','next');

            if ($totalPages > 1 && $cur < $totalPages) {
                $li = $this->appendNavItem($this->lastPageLabel, $this->totalPages);
                $li->getChildAt(0)->setAttributeValue('rel','last');
            }
        }

        $html = parent::render();

        if ($this->navWrapper) {
            $nav = new Element('nav');
            $nav->setAttributeValue('role', 'navigation');
            $nav->setAttributeValue('aria-label', "Pagination");
            $nav->setInnerHtml($html);
            return $nav->render($attributes);
        }
        return $html;
    }
}
