<?php
namespace WebUI\Components\React;
use WebUI\Core\Element;

class ReactComponent extends Element
{
    protected $jsClassName;

    protected $props = array();

    protected $debug = false;

    public $closeEmpty = true;


    public function __construct($jsClassName, array $props = array())
    {
        parent::__construct('div');
        $this->jsClassName = $jsClassName;
        $this->props = $props;
        $this->id = uniqid($jsClassName);
        $this->addClass('react-component react-app');
    }

    public function setDebug($debugLevel = 1)
    {
        $this->debug = $debugLevel;
    }

    public function render($attributes = array())
    {
        $varId = uniqid('app');
        // render the div element
        $out = parent::render($attributes) . "\n";
        $out .= "<script>\n";
        $out .= "document.addEventListener('load', function(evt) {\n";
        if ($this->debug) {
            $out .= "console.log('React.createElement {$this->jsClassName}');";
        }
        $out .= "var {$varId} = React.createElement({$this->jsClassName}," . json_encode($this->props ?: array(), JSON_PRETTY_PRINT) . ");\n";
        $out .= "React.render({$varId}, document.getElementById('{$this->id}'));\n";
        $out .= "});\n";
        $out .= "</script>\n";
        return $out;
    }


}
