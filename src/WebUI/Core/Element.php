<?php
namespace WebUI\Core;
use Exception;
use DOMDocument;
use DOMNode;
use DOMText;
use ArrayAccess;

class Element implements ArrayAccess
{
    // extracted from CascadingAttribute
    const  ATTR_ANY = 0;
    const  ATTR_ARRAY = 1;
    const  ATTR_STRING = 2;
    const  ATTR_INTEGER = 3;
    const  ATTR_FLOAT = 4;
    const  ATTR_CALLABLE = 5;
    const  ATTR_FLAG = 6;


    /**
     * @var bool should we allow users to set undefined 
     * attributes?
     */
    public $allowUndefinedAttribute = true;

    /**
     * @var array $supportedAttributes
     */
    protected $_supportedAttributes = array();

    protected $_attributes = array();

    protected $_ignoredAttributes = array();


    /**
     *
     * @param string $tagName Tag name
     */
    public function __construct($tagName, $attributes = array() )
    {
        $this->tagName = $tagName;
        $this->setAttributeType( 'class', self::ATTR_ARRAY );
        $this->setAttributes( $attributes );
        $this->init($attributes);
    }


    public function isIgnoredAttribute($name)
    {
        return isset($this->_ignoredAttributes[$name]);
    }

    public function addIgnoredAttribute($name)
    {
        $this->_ignoredAttributes[$name] = true;
    }

    public function removeIgnoredAttribute($name)
    {
        unset($this->_ignoredAttributes[$name]);
    }

    /**
     * Register new attribute with type,
     * This creates accessors for objects.
     *
     * @param string $name  attribute name
     * @param integer $type  attribute type
     */
    public function setAttributeType( $name , $type ) 
    {
        $this->_supportedAttributes[ $name ] = $type;
    }


    /**
     * Remove attribute
     *
     * @param string $name
     */
    public function removeAttributeType($name)
    {
        unset( $this->_supportedAttributes[ $name ] );
    }


    /**
     * Get attribute value
     *
     * @param string $name
     * @return mixed value
     */
    public function __get($name)
    {
        if (isset($this->_attributes[ $name ] ))
            return $this->_attributes[ $name ];
    }

    /**
     * Set attribute value
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name,$value)
    {
        $this->_attributes[ $name ] = $value;
    }



    /**
     * Check property and set attribute value without type 
     * checking.
     *
     * If there is a property with the same name
     * Then the value will be set to the property.
     *
     * Or the value will be stored in $this->_attributes array.
     *
     * This is for internal use.
     *
     * @param string $name
     * @param mixed $arg
     */
    public function setAttributeValue($name,$arg)
    {
        if (property_exists($this, $name)) {
            $this->$name = $arg;
        } else {
            $this->_attributes[ $name ] = $arg;
        }
    }

    public function getAttributeValue($name)
    {
        if ( property_exists($this, $name) ) {
            return $this->$name;
        } elseif (array_key_exists($name, $this->_attributes)) {
            return $this->_attributes[ $name ];
        }
    }

    public function hasAttribute($name)
    {
        return property_exists($this, $name) || array_key_exists($name, $this->_attributes);
    }

    /**
     * Check if the attribute is registered 
     * if it's registered, the type registered will 
     * change the behavior of setting values.
     *
     *
     * @param string $name
     * @param array $args
     */
    public function setAttribute($name,$args)
    {
        if ( $this->isIgnoredAttribute($name) )
            continue;

        // check if it's registered.
        if( isset($this->_supportedAttributes[ $name ]) ) 
        {
            $c = count($args);
            $t = $this->_supportedAttributes[ $name ];

            if( $t != self::ATTR_FLAG && $c == 0 ) {
                throw new Exception( 'Attribute value is required.' );
            }

            switch( $t ) 
            {
                case self::ATTR_ANY:
                    $this->setAttributeValue( $name, $args[0] );
                    break;

                case self::ATTR_ARRAY:
                    if( $c > 1 ) {
                        $this->setAttributeValue( $name,  $args );
                    }
                    elseif( is_array($args[0]) ) 
                    {
                        $this->setAttributeValue( $name , $args[0] );
                    } 
                    else
                    {
                        $this->setAttributeValue( $name , (array) $args[0] );
                    }
                    break;

                case self::ATTR_STRING:
                    if( is_string($args[0]) ) {
                        $this->setAttributeValue($name,$args[0]);
                    }
                    else {
                        throw new Exception("attribute value of $name is not a string.");
                    }
                    break;

                case self::ATTR_INTEGER:
                    if( is_integer($args[0])) {
                        $this->setAttributeValue( $name , $args[0] );
                    }
                    else {
                        throw new Exception("attribute value of $name is not a integer.");
                    }
                    break;

                case self::ATTR_CALLABLE:

                    /**
                     * handle for __invoke, array($obj,$name), 'function_name 
                     */
                    if( is_callable($args[0]) ) {
                        $this->setAttributeValue( $name, $args[0] );
                    } else {
                        throw new Exception("attribute value of $name is not callable type.");
                    }
                    break;

                case self::ATTR_FLAG:
                    $this->setAttributeValue($name,true);
                    break;

                default:
                    throw new Exception("Unsupported attribute type: $name");
            }
            return $this;
        }
        // save unknown attribute by default
        else if( $this->allowUndefinedAttribute ) 
        {
            $this->setAttributeValue( $name, $args[0] );
        }
        else {
            throw new Exception("Undefined attribute $name, Do you want to use allowUndefinedAttribute option?");
        }
    }


    public function __call($method,$args)
    {
        $this->setAttribute($method,$args);
        return $this;
    }



    /**
     * ==========================================
     * Magic methods for convinence.
     * ==========================================
     */


    public function offsetSet($name,$value)
    {
        $this->setAttribute($name, array($value));
    }
    
    public function offsetExists($name)
    {
        return isset($this->_attributes[ $name ]);
    }
    
    public function offsetGet($name)
    {
        if( ! isset( $this->_attributes[ $name ] ) ) {
            // detect type for setting up default value.
            $type = @$this->_supportedAttributes[ $name ];
            if( $type == self::ATTR_ARRAY ) {
                $this->_attributes[ $name ] = array();
            }
        }
        $val =& $this->_attributes[ $name ];
        return $val;
    }
    
    public function offsetUnset($name)
    {
        unset($this->_attributes[$name]);
    }



    // -------------------------- end of cascading attributes


    // =================================
    // Element methods and members
    // =================================


    /**
     * element tag name
     */
    public $tagName;

    /**
     * @var array class name
     */
    protected $class = array();


    /**
     * @var Use close tag with empty children, when this option is on, 
     *      A tag with no children is rendered as "<foo> </foo>".
     */
    public $closeEmpty = false;

    /**
     * Children elements
     *
     * @var array
     */
    public $children = array();


    /**
     * @var id field
     */
    public $id;

    /**
     * @var array Standard attribute from element class member.
     */
    protected $standardAttributes = array( 
        /* core attributes */
        'class','id' 
    );

    /**
     * @var array Custom attributes (append your attribute name to render class 
     *            member as attribute)
     */
    protected $customAttributes = array();





    protected function init($attributes)
    {

    }


    /**
     * Add custom attribute name (will be rendered)
     *
     * @param string $attribute Attribute name
     */
    public function registerCustomAttribute($attribute)
    {
        $this->customAttributes[] = $attribute;
        return $this;
    }


    /**
     * Add attribute to customAttribute list
     *
     * @param string|array $attributes
     *
     *    $this->addAttributes('id class for');
     */
    public function registerCustomAttributes($attributes) 
    {
        if( is_string($attributes) ) {
            $attributes = explode(' ',$attributes);
        }
        $this->customAttributes = array_merge( $this->customAttributes , (array) $attributes );
        return $this;
    }


    /**
     *
     * @param string $class class name
     */
    public function addClass($class)
    {
        if ( is_array($this->class) ) {
            if( is_array($class) ) {
                $this->class = array_merge( $this->class , $class );
            } else {
                $this->class[] = $class;
            }
        } elseif ( is_string($this->class) ) {
            $this->class .= " " . $class;
        } else {
            throw new Exception("Wrong class name type, array expected.");
        }
        return $this;
    }

    /**
     * @param string $class
     * @return bool 
     */
    public function hasClass($class) 
    {
        return array_search($class,$this->class) !== false;
    }


    /**
     * @param string $class class name
     */
    public function removeClass($class)
    {
        $index = array_search( $class, $this->class );
        array_splice( $this->class, $index , 1 );
        return $this;
    }

    /**
     * Add class
     */
    public function getClass()
    {
        return $this->class;
    }


    /**
     * Set element id
     *
     * @param string $id add identifier attribute
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getId($id)
    {
        return $this->id;
    }


    /**
     * Prepend Child element.
     *
     * @param FormKit\Element
     */
    public function prepend($child)
    {
        array_splice($this->children,0,0,array($child));
        return $this;
    }


    public function insert($child)
    {
        array_splice($this->children,0,0,array($child));
        return $this;
    }

    /**
     * Insert child at index position.
     *
     * @param FormKit\Element $child
     * @param integer $pos index position
     */
    public function insertChild($child, $pos = 0)
    {
        array_splice($this->children, $pos, 0, $child);
        return $this;
    }


    /**
     * Append child element at the end of list.
     *
     * @param FormKit\Element $child
     */
    public function append($child)
    {
        $this->children[] = $child;
        return $this;
    }


    public function setInnerText($text)
    {
        $this->children = array(new DOMText($text));
    }


    public function setInnerHtml($html)
    {
        $this->children = array(strval($html));
    }

    /**
     * Append DOMText node to children list.
     */
    public function appendText($text)
    {
        if ( $text ) {
            $this->addChild( new DOMText($text) );
        }
        return $this;
    }



    /**
     * As same as `append` method
     *
     * @param FormKit\Element $child
     */
    public function addChild($child)
    {
        $this->children[] = $child;
        return $this;
    }


    public function appendTo($container)
    {
        $container->append($this);
        return $this;
    }


    /**
     * Check if this node contains children.
     *
     * @return bool
     */
    public function hasChildren()
    {
        return !empty($this->children);
    }

    /**
     * Return children elements
     *
     * @return array FormKit\Element[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    public function getChildrenSize()
    {
        return count($this->children);
    }

    public function getChildAt($index)
    {
        if (isset($this->children[$index])) {
            return $this->children[$index];
        }
    }

    public function removeChildAt($index)
    {
        $removed = array_splice($this->children, $index, 1);
        return $removed;
    }


    protected function _renderNodes(array $nodes)
    {
        $html = '';
        foreach($nodes as $node)
        {
            if ($node instanceof DOMText || $node instanceof DOMNode ) {
                // to use C14N(), the DOMNode must be belongs to an instance of DOMDocument.
                $dom = new DOMDocument;
                $node2 = $dom->importNode($node);
                $dom->appendChild($node2);
                $html .= $node2->C14N();;
            } elseif (is_string($node) ) {
                $html .= $node;
            } elseif (is_object($node)
                        && ($node instanceof \FormKit\Element
                         || $node instanceof \FormKit\Layout\BaseLayout 
                         || method_exists($node,'render') )) 
            {
                $html .= $node->render();
            }
            else
            {
                throw new Exception('Unknown node type');
            }
        }
        return $html;
    }


    /**
     * Render children nodes
     */
    public function renderChildren()
    {
        if ($this->hasChildren()) {
            return $this->_renderNodes($this->children);
        }
        return '';
    }

    /**
     * Set attributes from array
     *
     * @param array $attributes
     */
    public function setAttributes($attributes)
    {
        foreach( $attributes as $k => $val ) {
            if ($this->isIgnoredAttribute($k)) {
                continue;
            }

            // this is for adding new class name with
            //   +=newClass
            if (is_string($val) && strpos($val ,'+=') !== false ) {
                $origValue = $this->getAttributeValue($k);
                if( is_string($origValue) ) {
                    $origValue .= ' ' . substr($val,2);
                } elseif ( is_array($origValue) ) {
                    $origValue[] = substr($val,2);
                } elseif ( is_object($origValue) ) {
                    throw new Exception('Invalid Object for attribute: ' . get_class($origValue) );
                } else {
                    throw new Exception('Unknown attribute value type.');
                }
                $this->setAttributeValue($k,$origValue);
            } else {
                $this->setAttributeValue($k, $val);
            }
        }
    }

    /**
     * Render attributes string
     *
     * @return string Standard Attribute string
     */
    public function renderAttributes() 
    {
        return $this->_renderAttributes($this->standardAttributes)
            . $this->_renderAttributes($this->customAttributes)
            . $this->_renderAttributes(array_keys($this->_attributes), true);
    }

    /**
     * Render attributes
     *
     * @param array $keys
     * @return string 
     */
    protected function _renderAttributes($keys, $allowNull = false) 
    {
        $html = '';
        foreach($keys as $key) {
            if ($this->hasAttribute($key)) {
                $val = $this->getAttributeValue($key);

                if (!$allowNull && ($val === NULL || (is_array($val) && empty($val))) )
                    continue;

                if (is_array($val)) {
                    // check if the array is an indexed array, check keys of 
                    // array[0..cnt] 
                    //
                    // if it's an indexed array
                    // for attributes key like "class", the value can be 
                    //
                    //     array('class1','class2')
                    //
                    // this renders the attribute as "class1 class2"
                    //
                    // if it's an associative array
                    // for attribute key like "style", the value can be 
                    //
                    //      array( 'border' => '1px solid #ccc' )
                    //
                    // this renders the attribute as 
                    //
                    //      "border: 1px solid #ccc;"
                    //
                    if (array_keys($val) === range(0, count($val)-1) ) {
                        $val = join(' ', $val);
                    } else {
                        $val0 = $val;
                        $val = '';
                        foreach( $val0 as $name => $data ) {
                            $val .= "$name:$data;";
                        }
                    }
                }
                // for boolean type values like readonly attribute, 
                // we render it as readonly="readonly".
                elseif ($val === TRUE || $val === FALSE) {
                    $val = $key;
                }

                // Convert camalcase name to dash-separated name
                //
                // for dataUrl attributes, render these attributes like data-url
                // ( use dash separator)
                if ($val === NULL) {
                    $html .= sprintf(' %s',strtolower(preg_replace('/[A-Z]/', '-$0', $key)));
                } else {
                    $html .= sprintf(' %s="%s"', 
                            strtolower(preg_replace('/[A-Z]/', '-$0', $key)),
                            htmlspecialchars( $val )
                        );
                }
            }
        }
        return $html;
    }


    /**
     * Render open tag
     *
     *
     * $form->open();
     *
     * $form->renderChildren();
     *
     * $form->close();
     */
    public function open( $attributes = array() ) {
        $this->setAttributes( $attributes );
        $html = '<' . $this->tagName
                    . $this->renderAttributes()
                    ;
        // should we close it ?
        if ($this->closeEmpty || $this->hasChildren()) {
            $html .= '>';
        } else {
            $html .= '/>';
        }
        return $html;
    }


    /**
     * Render close tag
     */
    public function close() {
        $html = '';
        if( $this->closeEmpty || $this->hasChildren() ) {
            $html .= '</' . $this->tagName . '>';
        }
        return $html;
    }


    /**
     * Render the whole element.
     *
     * @param array $attributes attributes to override.
     * @param string HTML
     */
    public function render($attributes = array()) 
    {
        if (!$this->tagName) {
            throw new Exception('tagName is not defined.');
        }
        $html  = $this->open( $attributes );
        $html .= $this->renderChildren();
        $html .= $this->close();
        return $html;
    }

    public function formatRender($attributes = array())
    {
        $html = $this->render($attributes);
        $dom = new DOMDocument('1.0');
        $dom->preserveWhiteSpace = true;
        $dom->formatOutput = true;
        $dom->strictErrorChecking = true;
        $dom->validateOnParse = false;
        $dom->resolveExternals = false;
        $dom->loadXML($html);

        $prettyHTML = '';
        foreach ($dom->childNodes as $node) {
            $prettyHTML .= $dom->saveXML($node) . "\n";
        }
        return $prettyHTML;
    }


    public function __toString()
    {
        return $this->render();
    }
}

