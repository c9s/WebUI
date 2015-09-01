<?php
namespace WebUI\Components\Pager;

/**
 * A simple pager, does not depends on Pager interface.
 *
 * @version 2
 */
class BootstrapPager
{
    public $firstText;
    public $lastText;
    public $nextText;
    public $prevText;

    public $showHeader = false;

    /**
     * @var boolena show navigator
     */
    public $navigator = true;

    public $showPageNumbers = true;
    public $wrapperClass = array('pagination', 'pagination-centered', 'pagination-mini');
    public $whenOverflow  = true;

    public $rangeLimit = 3;

    public $totalPages = 0;
    public $pageSize = 20;
    public $currentPage = 1;

    /**
     *
     * @param integer current page
     * @param integer total size
     * @param integer page size (optional)
     */
    public function __construct()
    {
        $this->firstText = _('Page.First');
        $this->lastText  = _('Page.Last');
        $this->nextText  = _('Page.Next');
        $this->prevText  = _('Page.Previous');
        if ( $args = func_get_args() ) {
            if ( 2 === count($args) ) {
                $this->currentPage = $args[0] ?: 1;
                $this->pageSize = 10;
                $this->calculatePages($args[1]);
            } elseif ( 3 === count($args) ) {
                $this->currentPage = $args[0] ?: 1;
                $this->pageSize = intval($args[2]) ?: 10;
                $this->calculatePages($args[1],$args[2]);
            }
        }
    }

    public function setFirstPageText($text)
    {
        $this->firstText = $text;
    }

    public function setLastPageText($text)
    {
        $this->lastText = $text;
    }

    public function setNextPagetext($text)
    {
        $this->nextText = $text;
    }

    public function setPrevPageText($text)
    {
        $this->prevText = $text;
    }

    public function addClass($class)
    {
        $this->wrapperClass[] = $class;
    }

    /**
     * @param integer $total
     * @param integer $size  = null  (optional)
     */
    public function calculatePages($total)
    {
        $this->totalPages = $total > 0 ? (int) ceil($total / $this->pageSize ) : 0;
    }

    public function mergeQuery( $orig_params , $params = array() )
    {
        $params = array_merge(  $orig_params , $params );

        return '?' . http_build_query( $params );
    }

    public function renderLink( $num , $text = null , $moreclass = "" , $disabled = false , $active = false )
    {
        if ($text === null) {
            $text = $num;
        }

        if ($disabled) {
            return $this->renderLinkDisabled( $text , $moreclass );
        }

        $args = array_merge( $_GET , $_POST );
        $href = $this->mergeQuery( $args , array( "page" => $num ) );
        $liClass = '';
        if ($active) {
            $liClass = 'active';
        }
        return <<<EOF
 <li class="$liClass"><a data-target-page="$num" class="pager-link $moreclass" href="$href">$text</a></li>
EOF;


    }

    public function renderLinkDisabled( $text , $moreclass = "" )
    {
        return <<<EOF
<li class="disabled"><a class="pager-link pager-disabled $moreclass">$text</a></li>
EOF;
    }

    public function __toString()
    {
        return $this->render();
    }

    public function render2()
    {
        $heredoc = new \Phifty\View\Heredoc('twig');
        $heredoc->content =<<<TWIG
<div class="pager">
    {% for i in 0 .. totalPages %}

    {% endfor %}
</div>
TWIG;
        $html = $heredoc->render(array(
            'currentPage' => $this->currentPage,
            'totalPages'  => $this->totalPages,
        ));
    }

    public function render()
    {
        $cur = $this->currentPage;
        $total_pages = $this->totalPages;

        if ($this->whenOverflow && $this->totalPages <= 1) {
            return "";
        }

        $pagenum_start = $cur > $this->rangeLimit ? $cur - $this->rangeLimit : 1 ;
        $pagenum_end   = $cur + $this->rangeLimit < $total_pages ?  $cur + $this->rangeLimit : $total_pages;

        $output = "";
        $output .= '<ul class="'. join(' ',$this->wrapperClass) .'">';

        if ($this->navigator) {
            $output .= $this->renderLink( 1       , $this->firstText , 'pager-first' , $cur == 1 );
            $output .= $this->renderLink( $cur - 1 , $this->prevText  , 'pager-prev'  , $cur == 1 );
        }


        if ( $this->showPageNumbers ) {
            if ( $cur > 5 ) {
                $output .= $this->renderLink( 1 , 1 , 'pager-number' );
                $output .= '<li><a>...</a></li>';
            }

            for ($i = $pagenum_start ; $i <= $pagenum_end ; $i++) {
                if ( $i == $this->currentPage ) {
                    $output .= $this->renderLink( $i , $i , 'pager-number active pager-number-current', false, true);
                } else {
                    $output .= $this->renderLink( $i , $i , 'pager-number' );
                }
            }

            if ( $cur + 5 < $total_pages ) {
                $output .= '<li><a>...</a></li>';
                $output .= $this->renderLink( $total_pages , $total_pages , 'pager-number' );
            }
        }

        if ($this->navigator) {
            $output .= $this->renderLink( $cur + 1,
                        $this->nextText , 'pager-next' , $cur == $this->totalPages );
            $output .= $this->renderLink( $this->totalPages,
                        $this->lastText , 'pager-last' , $cur == $this->totalPages );
        }
        $output .= '</ul>';
        return $output;
    }
}
