<?php
include('config.php');
include('formatPhtml.php');
include('condPhtml.php');


/** 
 * -- TAREAS IMPORTANTES -- 
 * 
 * (*) crear compilador ternario
 * (*) agregar soporte variables globales multinivel
 * (*) aplicar formateo de salida
 * (*) eliminar comentarios si commpress lo pide
 * (*) compilar var const y aplicar segComentarios solo al contenido del include
 * (*) agregar sistema captura errores en depuracion
 */
class Phtml
{

    /**
     * @var string $_strContent
     */
    private $_strContent = '';

    /**
     * @var array $_arrContent
     */
    private $_arrContent;

    /**
     * @var boolean $_bolCatch
     */
    private $_bolCatch = false;

    /**
     * @var string $_openVar
     */
    private $_openVar;

    /**
     * @var string $_closeVar
     */
    private $_closeVar;

    /**
     * @var string $_openConst
     */
    private $_openConst;

    /**
     * @var string $_closeConst
     */
    private $_closeConst;

    /**
     * @var boolean $_bolPermitted_GLOBALS
     */
    private $_bolPermitted_GLOBALS;

    /**
     * @var boolean $_bolPermitted_SERVER
     */
    private $_bolPermitted_SERVER;

    /**
     * @var boolean $_bolPermitted_GET
     */
    private $_bolPermitted_GET;

    /**
     * @var boolean $_bolPermitted_POST
     */
    private $_bolPermitted_POST;

    /**
     * @var boolean $_bolPermitted_FILES
     */
    private $_bolPermitted_FILES;

    /**
     * @var boolean $_bolPermitted_COOKIE
     */
    private $_bolPermitted_COOKIE;

    /**
     * @var boolean $_bolPermitted_SESSION
     */
    private $_bolPermitted_SESSION;

    /**
     * @var boolean $_bolPermitted_REQUEST
     */
    private $_bolPermitted_REQUEST;

    /**
     * @var boolean $_bolPermitted_ENV
     */
    private $_bolPermitted_ENV;

    /**
     * @var boolean $_bolExecutePhp
     */
    private $_bolExecutePhp;

    /**
     * @var boolean $_bolCompress
     */
    private $_bolCompress;

    /**
     * @var array $_arrVar
     */
    private $_arrVar = array();

    /**
     * @var boolean $_bolEjecutarMetodos
     */
    private $_bolEjecutarMetodos;

    /**
     * @var string $_randID
     */
    private $_randID;

    /**
     * @var boolean $_bolClearComment
     */
    private $_bolClearComment;

    /**
     * @var string $_cadEncoding
     */
    private $_cadEncoding;

    /**
     * @var object formatPhtml $_objFormat 
     */
    private $_objFormat;

    /**
     * @var object condPhtml $_objCond 
     */
    private $_objCond;

    /**
     * @var boolean $_bolIsset
     */
    private $_bolIsset;


    /**
     * Inicializar los componentes necesarios 
     */
    public function __construct()
    {
        $this->_openVar          = defined('PHTML_OPEN_VAR')       ? PHTML_OPEN_VAR       : '{{';
        $this->_closeVar        = defined('PHTML_CLOSE_VAR')     ? PHTML_CLOSE_VAR     : '}}';
        $this->_openConst         = defined('PHTML_OPEN_CONST')      ? PHTML_OPEN_CONST      : '[[';
        $this->_closeConst       = defined('PHTML_CLOSE_CONST')    ? PHTML_CLOSE_CONST    : ']]';
        $this->_bolEjecutarMetodos    = defined('PHTML_EXECUTE_METHOD')     ? PHTML_EXECUTE_METHOD     : true;
        $this->_bolPermitted_GLOBALS   = defined('PHTML_PERMITTED_GLOBALS')    ? PHTML_PERMITTED_GLOBALS    : true;
        $this->_bolPermitted_SERVER    = defined('PHTML_PERMITTED_SERVER')     ? PHTML_PERMITTED_SERVER     : false;
        $this->_bolPermitted_GET       = defined('PHTML_PERMITTED_GET')        ? PHTML_PERMITTED_GET        : true;
        $this->_bolPermitted_POST      = defined('PHTML_PERMITTED_POST')       ? PHTML_PERMITTED_POST       : true;
        $this->_bolPermitted_FILES     = defined('PHTML_PERMITTED_FILES')      ? PHTML_PERMITTED_FILES      : false;
        $this->_bolPermitted_COOKIE    = defined('PHTML_PERMITTED_COOKIE')     ? PHTML_PERMITTED_COOKIE     : true;
        $this->_bolPermitted_SESSION   = defined('PHTML_PERMITTED_SESSION')    ? PHTML_PERMITTED_SESSION    : true;
        $this->_bolPermitted_REQUEST   = defined('PHTML_PERMITTED_REQUEST')    ? PHTML_PERMITTED_REQUEST    : true;
        $this->_bolPermitted_ENV       = defined('PHTML_PERMITTED_ENV')        ? PHTML_PERMITTED_ENV        : false;
        $this->_bolCompress          = defined('PHTML_COMPRESS')           ? PHTML_COMPRESS           : false;
        $this->_bolExecutePhp        = defined('PHTML_EXECUTE_PHP')        ? PHTML_EXECUTE_PHP        : true;
        $this->_bolClearComment = defined('PHTML_CLEAR_COMMENT') ? PHTML_CLEAR_COMMENT : true;
        $this->_bolIsset              = defined('PHTML_COND_ISSET')          ? PHTML_COND_ISSET          : false;
        $this->_cadEncoding           = defined('PHTML_ENCODING')            ? PHTML_ENCODING            : 'UTF-8';
        $cadClave                     = defined('PHTML_STR_KEY')        ? PHTML_STR_KEY        : 'phtml';
        $this->_objFormat             = new formatPhtml;
        $this->_objCond               = new condPhtml;
        $this->_randID           = $this->_createRandID($cadClave);
        $this->_arrContent[$this->_randID] = '';
    }


    /**
     * Agrega contenido phtml al para futura compiladion
     * 
     * @param string $content
     * @param mixed $index
     */
    public function addContent($content = '', $index = null)
    {
        if (isset($index)) {
            if (isset($this->_arrContent[$index])) {
                $this->_arrContent[$index] .= $content;
            } else {
                $this->_arrContent[$index] = $content;
            }
        } else {
            $this->_arrContent[$this->_randID] .= $content;
        }
    }



    /**
     * Recoge el contenido del archivo y lo agrega para compilar
     * 
     * @param string $path
     * @param mixed $index
     */
    public function addFile($path, $index = null)
    {
        if (file_exists($path)) {
            if ($this->_bolExecutePhp) {
                ob_start();
                include($path);
                $content = ob_get_contents();
                ob_end_clean();
            } else {
                $content = file_get_contents($path);
            }
            $this->addContent($content, $index);
        }
    }



    /**
     * Recoge el contenido que se esta ejecutando de una determinada parte del archivo
     * 
     * @param mixed $index
     */
    public function catchContent($index = null)
    {
        if ($this->_bolCatch) {
            $content = ob_get_contents();
            ob_end_clean();
            $this->_bolCatch = false;
            $this->addContent($content, $index);
        } else {
            ob_start();
            $this->_bolCatch = true;
        }
    }


    /**
     * Agrega una variable para su disponibilidad en el contenido
     * 
     * @param mixed $index
     * @param mixed $value
     */
    public function addVar($index, $value = '')
    {
        if (isset($index)) {
            $this->_arrVar[$index] = $value;
        }
    }


    /**
     * Devuelve el objeto DOMDocument con el contenido html cargado
     * 
     * @param string $html
     * @return object
     */
    private function _getObjDOM($html = '')
    {
        $objDom = new DOMDocument;
        libxml_use_internal_errors(true);
        $html = '<phtml id="' . $this->_randID . '">' . $html . '</phtml>';
        $htmlEncoded = mb_convert_encoding($html, 'HTML-ENTITIES', $this->_cadEncoding);
        @$objDom->loadHTML($htmlEncoded);
        libxml_clear_errors();
        return ($objDom);
    }


    /**
     * Agrega un objeto de formatear personalizado
     * 
     * @param object $objFormat un objeto de tipo formatPhtml
     */
    public function userFormat(formatPhtml $objFormat)
    {
        if($objFormat instanceof $objFormat) {
            $this->_objFormat = $objFormat;
        }
    }


    /**
     * Agrega un objeto de condiciones personalizado
     * 
     * @param object $objCond un objeto de tipo condPhtml
     */
    public function userCond(condPhtml $objCond)
    {
        if($objCond instanceof condPhtml) {
            $this->_objCond = $objCond;
        }
    }


    /**
     * Convierte la cadena html en elementos DOMDocument
     * 
     * @param object $objDom
     * @param string $html
     * @return object
     */
    private function _convertHTMLinElements(DOMDocument $objDom, $html)
    {
        $thisObjDom = $this->_getObjDOM($html);
        $objPhtml = $thisObjDom->getElementById($this->_randID);
        $objFrag = $objDom->createDocumentFragment();
        $thisNode = $objPhtml->firstChild;
        while ($thisNode) {
            $objFrag->appendChild($objDom->importNode($thisNode, true));
            $thisNode = $thisNode->nextSibling;
        }
        return ($objFrag);
    }



    /**
     * Recoge todo los elementos del nodo
     * 
     * @param object DOMDocument $objDom
     * @param object DOMNode $node
     * @return object DOMDocumentFragment 
     */
    private function _getElements(DOMDocument $objDom, DOMNode $node)
    {
        $objFrag = $objDom->createDocumentFragment();
        while ($thisNode = $node->firstChild) {
            $objFrag->appendChild($objDom->importNode($thisNode, true));
            $thisNode = $thisNode->nextSibling;
        }
        return ($objFrag);
    }


    /**
     * Crea una cadena con el contenido de un nodo
     *  
     * @param object DOMNode $node el nodo del que se quere crear el contenido
     * @return string la cadena con el contenido del nodo especificado
     */
    private function _getHTML(DOMNode $node)
    {
        $html = '';
        $childs = $node->childNodes;
        foreach ($childs as $child) {
            $html .= $node->ownerDocument->saveHTML($child);
        }
        return ($html);
    }


    /**
     * Elimina los comentarios antes del nodo especificado
     * 
     * @param object $node el nodo del que se quere eliminar los comentarios
     */
    private function _clearComments(DOMNode $node)
    {
        while (@$node->previousSibling->nodeType == XML_COMMENT_NODE || (@$node->previousSibling->nodeType == XML_TEXT_NODE && ctype_space(@$node->previousSibling->textContent))) {
            $node->parentNode->removeChild($node->previousSibling);
        }
    }


    /**
     * Transforma las cadenas pasadas en la plantilla en variables
     * 
     * @param string $cadVariable captura del supuesto nombre de variable
     * 
     * @return mixed devuelve el valor que contiene la variable
     */
    private function _importVar($cadVariable)
    {
        if (preg_match('/[^0-9][a-zA-Z0-9_\.]+/', trim($cadVariable))) {
            $arr = explode('.', $cadVariable);
            $numParametros    = sizeof($arr);
            switch ($numParametros) {
                case 1:
                    if (isset($this->_arrVar[$arr[0]])) {
                        return($this->_arrVar[$arr[0]]);
                    }
                case 2:
                    switch ($arr[0]) { // obtener super globales
                        case 'GLOBALS':
                            if ($this->_bolPermitted_GLOBALS && isset($_GLOBALS[$arr[1]])) {
                                return($GLOBALS[$arr[1]]);
                            }
                            break;
                        case '_SERVER':
                            if ($this->_bolPermitted_SERVER && isset($_SERVER[$arr[1]])) {
                                return($_SERVER[$arr[1]]);
                            }
                            break;
                        case '_GET':
                            if ($this->_bolPermitted_GET && isset($_GET[$arr[1]])) {
                                return($_GET[$arr[1]]);
                            }
                            break;
                        case '_POST':
                            if ($this->_bolPermitted_POST && isset($_POST[$arr[1]])) {
                                return($_POST[$arr[1]]);
                            }
                            break;
                        case '_FILES':
                            if ($this->_bolPermitted_FILES && isset($_FILES[$arr[1]])) {
                                return($_FILES[$arr[1]]);
                            }
                            break;
                        case '_COOKIE':
                            if ($this->_bolPermitted_COOKIE && isset($_COOKIE[$arr[1]])) {
                                return($_COOKIE[$arr[1]]);
                            }
                            break;
                        case '_SESSION':
                            if ($this->_bolPermitted_SESSION && isset($_SESSION[$arr[1]])) {
                                return($_SESSION[$arr[1]]);
                            }
                            break;
                        case '_REQUEST':
                            if ($this->_bolPermitted_REQUEST && isset($_REQUEST[$arr[1]])) {
                                return($_REQUEST[$arr[1]]);
                            }
                            break;
                        case '_ENV':
                            if ($this->_bolPermitted_ENV && isset($_ENV[$arr[1]])) {
                                return($_ENV[$arr[1]]);
                            }
                            break;
                        default:
                            if (isset($this->_arrVar[$arr[0]]) && is_array($this->_arrVar[$arr[0]])) {
                                if (isset($this->_arrVar[$arr[0]][$arr[1]])) {
                                    return($this->_arrVar[$arr[0]][$arr[1]]); // arreglo[]
                                }
                            } else if (isset($this->_arrVar[$arr[0]]) && is_object($this->_arrVar[$arr[0]])) {
                                if (property_exists($this->_arrVar[$arr[0]], $arr[1])) {
                                    return($this->_arrVar[$arr[0]]->{$arr[1]}); // objeto->propiedad
                                } else if ($this->_bolEjecutarMetodos && method_exists($this->_arrVar[$arr[0]], $arr[1])) {
                                    return($this->_arrVar[$arr[0]]->{$arr[1]}()); // objeto->metodo()
                                }
                            }
                            break;
                    }
                    break;
                case 3:
                    /** (*) agregar soporte variables globales multinivel */
                    if (isset($this->_arrVar[$arr[0]][$arr[1]]) && is_array($this->_arrVar[$arr[0]][$arr[1]])) {
                        if (isset($this->_arrVar[$arr[0]][$arr[1]][$arr[2]])) {
                            return($this->_arrVar[$arr[0]][$arr[1]][$arr[2]]); // arreglo[][]
                        }
                    } else if (isset($this->_arrVar[$arr[0]]) && is_object($this->_arrVar[$arr[0]])) {
                        if ($this->_bolEjecutarMetodos && method_exists($this->_arrVar[$arr[0]], $arr[1])) {
                            return($this->_arrVar[$arr[0]]->{$arr[1]}($arr[2])); // objeto->metodo(param)
                        }
                    } else if (isset($this->_arrVar[$arr[0]][$arr[1]]) && is_object($this->_arrVar[$arr[0]][$arr[1]])) {
                        if (property_exists($this->_arrVar[$arr[0]][$arr[1]], $arr[2])) {
                            return($this->_arrVar[$arr[0]][$arr[1]]->{$arr[2]}); // objeto->propiedad
                        } else if ($this->_bolEjecutarMetodos && method_exists($this->_arrVar[$arr[0]][$arr[1]], $arr[2])) {
                            return($this->_arrVar[$arr[0]][$arr[1]]->{$arr[2]}()); // objeto->metodo()
                        }
                    }
                    break;
                case 4:
                    /** (*) agregar soporte variables globales multinivel */
                    if (isset($this->_arrVar[$arr[0]][$arr[1]][$arr[2]]) && is_array($this->_arrVar[$arr[0]][$arr[1]][$arr[2]])) {
                        if (isset($this->_arrVar[$arr[0]][$arr[1]][$arr[2]][$arr[3]])) {
                            return($this->_arrVar[$arr[0]][$arr[1]][$arr[2]][$arr[3]]); // arreglo[][][]
                        }
                    } else if (isset($this->_arrVar[$arr[0]]) && is_object($this->_arrVar[$arr[0]])) {
                        if ($this->_bolEjecutarMetodos && method_exists($this->_arrVar[$arr[0]], $arr[1])) {
                            return($this->_arrVar[$arr[0]]->{$arr[1]}($arr[2], $arr[3])); // objeto->metodo(param, param)
                        }
                    } else if (isset($this->_arrVar[$arr[0]]) && is_object($this->_arrVar[$arr[0]][$arr[1]])) {
                        if ($this->_bolEjecutarMetodos && method_exists($this->_arrVar[$arr[0]][$arr[1]], $arr[2])) {
                            return( $this->_arrVar[$arr[0]][$arr[1]]->{$arr[2]}($arr[3])); // objeto->metodo(param)
                        }
                    } else if (isset($this->_arrVar[$arr[0]][$arr[1]][$arr[2]]) && is_object($this->_arrVar[$arr[0]][$arr[1]][$arr[2]])) {
                        if (property_exists($this->_arrVar[$arr[0]][$arr[1]][$arr[2]], $arr[3])) {
                            return($this->_arrVar[$arr[0]][$arr[1]][$arr[2]]->{$arr[3]}); // objeto->propiedad
                        } else if ($this->_bolEjecutarMetodos && method_exists($this->_arrVar[$arr[0]][$arr[1]][$arr[2]], $arr[3])) {
                            return($this->_arrVar[$arr[0]][$arr[1]][$arr[2]]->{$arr[3]}()); // objeto->metodo()
                        }
                    }
                    break;
            }
        } else {
            if (is_numeric($cadVariable)) { // solo numeros
                return($cadVariable);
            }
        }
        return (null);
    }



    /**
     * Crea un identificador aleatorio
     * 
     * @param string $key la cadena que se quiere encriptar
     * @return string devuelve una cadena encriptada aleatoria
     */
    private function _createRandID($key)
    {
        $context = hash_init('sha256', HASH_HMAC, '/^P|-|T|V||_$dPm/' . rand(0, 1000));
        hash_update($context, $key);
        return (hash_final($context));
    }


    /**
     * Escapa los metacaracteres usados por expresiones regulares
     * 
     * @param string $entry la cadena que contiene los caracteres
     * 
     * @return string la cadena con los caracteres escapados
     */
    private function _escapeMetaChars($entry)
    {
        $arrMetaChars = array('\\', '.',  '+', '*', '?', '[', '^', ']', '$', '(', ')', '{', '}', '=', '!', '<', '>', '|', ':', '-');
        $arrEscapedChars = array('\\\\', '\.', '\+', '\*', '\?', '\[', '\^', '\]', '\$', '\(', '\)', '\{', '\}', '\=', '\!', '\<', '\>', '\|', '\:', '\-');
        $entry = str_replace($arrMetaChars, $arrEscapedChars, $entry);
        return ($entry);
    }



    /**
     * Comprueba que la variable cumpla una condicion
     * 
     * @param string $strCond
     * @param mixed $mixedVar
     * @return boolean
     */
    private function _checkCond($mixedVar, $strCond = '')
    {
        if ($strCond != '') {
            $arrCond = explode('.', $strCond, 2);
            if (sizeof($arrCond) == 1 && method_exists($this->_objCond, 'phtml_' . $arrCond[0])) {
                return ($this->_objCond->{'phtml_' . $arrCond[0]}($mixedVar));
            } else if (sizeof($arrCond) == 2 && method_exists($this->_objCond, 'phtml_' . $arrCond[0])) {
                return ($this->_objCond->{'phtml_' . $arrCond[0]}($mixedVar, $arrCond[1]));
            } else {
                return (false);
            }
        } else {
            return ($this->_bolIsset ? isset($mixedVar) : false);
        }
    }


    /**
     * Reemplaza los las variables de los bucles for - foreach - while
     * var="id.var.key.XXX.etc"
     * var='id.var.key.XXX.etc'
     * var="var.key.XXX.etc"
     * var='var.key.XXX.etc'
     * var="id.var.i.XXX.etc"
     * var='id.var.i.XXX.etc'
     * var="var.i.XXX.etc"
     * var='var.i.XXX.etc'
     * var='key.XXX.etc'
     * var='id.key.XXX.etc'
     * var="key.XXX.etc"
     * var="id.key.XXX.etc"
     * 
     * @param string $search
     * @param string $replacement
     * @param string $subject
     * 
     * @return string
     */
    private function _replaceQuotesEtc($search, $replacement, $subject)
    {
        $pattern = '/(?i)var(?-i)\s*=\s*[\'|"]\s*' . str_replace('.', '\.', $search) . '\.(.*?)\s*[\'|"]/';
        while (@preg_match($pattern, $subject, $arrResult)) {
            $replacement = 'var="' .  $replacement . '.' . $arrResult[1] . '"';
            $subject = @preg_replace('/' . $this->_escapeMetaChars($arrResult[0]) . '/', $replacement, $subject);
        }
        return ($subject);
    }


    /**
     * Reemplaza los las variables de los bucles for - foreach - while
     * var='id.i'
     * var='i'
     * var="id.i"
     * var="i"
     * var="var.i"
     * var="id.var.i"
     * var='var.i'
     * var='id.var.i'
     * var="id.var.key"
     * var="var.key"
     * var='id.var.key'
     * var='var.key'
     * var='value'
     * var='id.value'
     * var="value"
     * var="id.value"
     * var='key'
     * var='id.key'
     * var="key"
     * var="id.key"
     * 
     * @param string $search
     * @param string $replacement
     * @param string $subject
     * 
     * @return string
     */
    private function _replaceQuotes($search, $replacement, $subject)
    {
        $pattern = '/(?i)var(?-i)\s*=\s*[\'|"]\s*' . str_replace('.', '\.', $search) . '\s*[\'|"]/';
        while (@preg_match($pattern, $subject, $arrResult)) {
            $replacement = 'var="' .  $replacement . '"';
            $subject = @preg_replace('/' . $this->_escapeMetaChars($arrResult[0]) . '/', $replacement, $subject);
        }
        return ($subject);
    }



    /**
     * Reemplaza los las variables de los bucles for - foreach - while
     * 
     * {{var.key}}
     * {{id.var.key}}
     * {{key}}
     * {{id.key}}
     * {{value}}
     * {{id.value}}
     * {{id.var.i}}
     * {{var.i}}
     * {{id.i}}
     * {{i}}
     * @param string $search
     * @param string $replacement
     * @param string $subject
     * 
     * @return string
     */
    private function _replaceVar($search, $replacement, $subject)
    {
        $pattern = '/' . $this->_escapeMetaChars($this->_openVar) . '\s*' . str_replace('.', '\.', $search) . '\s*' . $this->_escapeMetaChars($this->_closeVar)  . '/';
        while (@preg_match($pattern, $subject, $arrResult)) {
            $subject = @preg_replace('/' . $this->_escapeMetaChars($arrResult[0]) . '/', $replacement, $subject);
        }
        return ($subject);
    }



    /**
     * Reemplaza las variables de los bucles for - foreach - while
     * 
     * {{var.key.XXX.etc}}
     * {{id.var.key.XXX.etc}}
     * {{id.var.i.XXX.etc}} 
     * {{var.i.XXX.etc}}
     * @param string $search     antiguo valor
     * @param string $replacement nuevo valor
     * @param string $subject el contenido que hay que modificar
     * 
     * @return string devuele el contenido con los valores reemplazados
     */
    private function _replaceVarEtc($search, $replacement, $subject)
    {
        $pattern = '/' . $this->_escapeMetaChars($this->_openVar) . '\s*' . str_replace('.', '\.', $search) . '\.(.*?)\s*' . $this->_escapeMetaChars($this->_closeVar)  . '/';
        while (@preg_match($pattern, $subject, $arrResult)) {
            $mixedVar = $this->_importVar($replacement . '.' . $arrResult[1]);
            if (isset($mixedVar)) {
                $replacement =  $this->_openVar . $mixedVar . $this->_closeVar;
            } else {
                $replacement = $this->_openVar . $replacement . '.' . $arrResult[1] . $this->_closeVar;
            }
            $subject = @preg_replace('/' . $this->_escapeMetaChars($arrResult[0]) . '/', $replacement, $subject);
        }
        return ($subject);
    }


    /**
     * Imprime todas las variables
     * 
     * @param boolean $deleteVar elimina las coencidencias si esta en true
     */
    private function _compile_var($deleteVar = false)
    {
        $pattern = '/' . $this->_escapeMetaChars($this->_openVar) . '\s*([^0-9][a-zA-Z0-9_\.]+)\s*' . $this->_escapeMetaChars($this->_closeVar) .  '/';
        if (preg_match_all($pattern, $this->_strContent, $arrResult)) {
            $size = sizeof($arrResult[0]);
            for ($i = 0; $i < $size; $i++) {
                $arrVar = explode('.', $arrResult[1][$i], 2);
                if (sizeof($arrVar) == 2 && method_exists($this->_objFormat, 'phtml_' . $arrVar[0])) { // {{func_format.mixedVar}}
                    $mixedVar = $this->_importVar($arrVar[1]);
                    if (!empty($mixedVar) && isset($mixedVar)) {
                        $content = $this->_objFormat->{'phtml_' . $arrVar[0]}($mixedVar);
                    } else {
                        $content = $deleteVar ? '' : null;
                    }
                } else {
                    $mixedVar = $this->_importVar($arrResult[1][$i]);
                    if (!empty($mixedVar) && isset($mixedVar)) { // {{mixedVar}}
                        $content = $mixedVar;
                    } else {
                        $content = $deleteVar ? '' : null;
                    }
                }
                if (isset($content)) {
                    $this->_strContent = str_replace($arrResult[0][$i], $content, $this->_strContent);
                }
            }
        }
    }



    /**
     * Imprime todas las constantes
     */
    private function _compile_const()
    {
        $pattern = '/' . $this->_escapeMetaChars($this->_openConst) . '\s*([^0-9][A-Z0-9_]+)\s*' . $this->_escapeMetaChars($this->_closeConst) .  '/';
        while (preg_match($pattern, $this->_strContent, $arrResult)) {
            if (defined($arrResult[1])) { // [[CONSTANTE]]
                $content = constant($arrResult[1]);
            } else {
                $content = '';
            }
            $this->_strContent = str_replace($arrResult[0], $content, $this->_strContent);
        }
    }


    /**
     * Compila de TAG include
     * 
     * <!-- La eliminacion de este comentario depende de $_bolClearComment -->
     * <include>
     * <!-- Este comentario se eliminara -->
     * ruta/del/archivo.phtml
     * <!-- Este comentario se eliminara -->
     * </include>
     */
    private function _compile_include()
    {
        $objDom = $this->_getObjDOM($this->_strContent);
        $objInclude = $objDom->getElementsByTagName('include')->item(0);
        if ($this->_bolClearComment) {
            $this->_clearComments($objInclude);
        }
        $path = trim(preg_replace('/(\\n|\\t|\\r)/s', '', $objInclude->textContent));
        if (file_exists($path)) {
            if ($this->_bolExecutePhp) {
                ob_start();
                include($path);
                $content = ob_get_contents();
                ob_end_clean();
            } else {
                $content = file_get_contents($path);
            }
            /* (*) compilar var const y aplicar seguridadComentarios solo al contenido del include */
            $objFrag = $this->_convertHTMLinElements($objDom, $content);
            $objInclude->parentNode->replaceChild($objFrag, $objInclude);
        } else {
            $objInclude->parentNode->removeChild($objInclude);
        }
        $objDom->saveHTML();
        $objPhtml = $objDom->getElementById($this->_randID);
        $this->_strContent = $this->_getHTML($objPhtml);
        // modificar esto
        $this->_seguridadComentarios();
        $this->_compile_const();
        $this->_compile_var();
    }



    /**
     * Compila los TAG if - elseif - else
     * <!-- La eliminacion de este comentario depende de $_bolClearComment -->
     * <if var="variable" cond="condicion" and="condicion" or="condicion">
     *      contenido si pasa la condicion
     * </if>
     * <!-- Este comentario se eliminara -->
     * <elseif var="variable" cond="condicion" and="condicion" or="condicion">
     *      contenido si pasa la condicion
     * </elseif>
     * <!-- Este comentario se eliminara -->
     * <else>
     *      contenido si han fallado las condiciones
     * </else>
     */
    private function _compile_if()
    {
        $objDom     = $this->_getObjDOM($this->_strContent);
        $objIf      = $objDom->getElementsByTagName('if')->item(0);
        $varIf      = $objIf->hasAttribute('var') && $objIf->getattribute('var') != '' ? $this->_importVar($objIf->getAttribute('var')) : null;
        $bolCondIf  = $objIf->hasAttribute('cond') ? $this->_checkCond($varIf, $objIf->getAttribute('cond')) : isset($varIf);
        $cadCompare = $objIf->hasAttribute('compare') ? strtolower(trim($objIf->getAttribute('compare'))) : 'and';
        $objFrag    = null;
        if ($objIf->hasAttribute('and') && !$objIf->hasAttribute('or')) {
            $bolPaseIf = $bolCondIf && $this->_checkCond($varIf, $objIf->getAttribute('and'));
        } else if ($objIf->hasAttribute('or') && !$objIf->hasAttribute('and')) {
            $bolPaseIf = $bolCondIf || $this->_checkCond($varIf, $objIf->getAttribute('or'));
        } else if ($objIf->hasAttribute('and') && $objIf->hasAttribute('or') && $cadCompare == 'and') {
            $bolPaseIf = $bolCondIf && $this->_checkCond($varIf, $objIf->getAttribute('and')) || $this->_checkCond($varIf, $objIf->getAttribute('or'));
        } else if ($objIf->hasAttribute('and') && $objIf->hasAttribute('or') && $cadCompare == 'or') {
            $bolPaseIf = $bolCondIf || $this->_checkCond($varIf, $objIf->getAttribute('or')) && $this->_checkCond($varIf, $objIf->getAttribute('and'));
        } else {
            $bolPaseIf = $bolCondIf;
        }
        if ($bolPaseIf) {
            $objFrag = $this->_getElements($objDom, $objIf);
            while (strtolower(@$objIf->nextSibling->nodeName) == 'elseif' || strtolower(@$objIf->nextSibling->nodeName) == 'else' || @$objIf->nextSibling->nodeType == XML_COMMENT_NODE || (@$objIf->nextSibling->nodeType == XML_TEXT_NODE && ctype_space(@$objIf->nextSibling->textContent))) {
                $objIf->parentNode->removeChild($objIf->nextSibling);
            }
        } else {
            while (strtolower(@$objIf->nextSibling->nodeName) == 'elseif' || @$objIf->nextSibling->nodeType == XML_COMMENT_NODE || (@$objIf->nextSibling->nodeType == XML_TEXT_NODE && ctype_space(@$objIf->nextSibling->textContent))) {
                if (strtolower(@$objIf->nextSibling->nodeName) == 'elseif') {
                    $objElseif = $objIf->nextSibling;
                    if (!$objFrag) {
                        $varElseIf      = $objElseif->hasAttribute('var') && $objElseif->getattribute('var') != '' ? $this->_importVar($objElseif->getAttribute('var')) : null;
                        $bolCondElseIf  = $objElseif->hasAttribute('cond') ? $this->_checkCond($varElseIf, $objElseif->getAttribute('cond')) : isset($varElseIf);
                        $cadCompareElseIf = $objElseif->hasAttribute('compare') ? strtolower(trim($objElseif->getAttribute('compare'))) : 'and';
                        if ($objElseif->hasAttribute('and') && !$objElseif->hasAttribute('or')) {
                            $bolPaseElseIf = $bolCondElseIf && $this->_checkCond($varElseIf, $objElseif->getAttribute('and'));
                        } else if ($objElseif->hasAttribute('or') && !$objElseif->hasAttribute('and')) {
                            $bolPaseElseIf = $bolCondElseIf || $this->_checkCond($varElseIf, $objElseif->getAttribute('or'));
                        } else if ($objElseif->hasAttribute('and') && $objElseif->hasAttribute('or') && $cadCompareElseIf == 'and') {
                            $bolPaseElseIf = $bolCondElseIf && $this->_checkCond($varElseIf, $objElseif->getAttribute('and')) || $this->_checkCond($varElseIf, $objElseif->getAttribute('or'));
                        } else if ($objElseif->hasAttribute('and') && $objElseif->hasAttribute('or') && $cadCompareElseIf == 'or') {
                            $bolPaseElseIf = $bolCondElseIf || $this->_checkCond($varElseIf, $objElseif->getAttribute('or')) && $this->_checkCond($varElseIf, $objElseif->getAttribute('and'));
                        } else {
                            $bolPaseElseIf = $bolCondElseIf;
                        }
                        if ($bolPaseElseIf) {
                            $objFrag = $this->_getElements($objDom, $objElseif);
                        }
                    }
                }
                $objIf->parentNode->removeChild($objIf->nextSibling);
            }
            if (strtolower(@$objIf->nextSibling->nodeName) == 'else') {
                if (!$objFrag) {
                    $objFrag = $this->_getElements($objDom, $objIf->nextSibling);
                }
                $objIf->parentNode->removeChild($objIf->nextSibling);
            }
        }
        if ($this->_bolClearComment) {
            $this->_clearComments($objIf);
        }
        if (!$objFrag) {
            $objIf->parentNode->removeChild($objIf);
        } else {
            $objIf->parentNode->replaceChild($objFrag, $objIf);
        }
        $objDom->saveHTML();
        $objPhtml = $objDom->getElementById($this->_randID);
        $this->_strContent = $this->_getHTML($objPhtml);
    }



    /**
     * Compila los TAG switch - case - default
     * <!-- La eliminacion de este comentario depende de $_bolClearComment -->
     * <switch var="variable">
     *      <!-- Este comentario se eliminara -->
     *      <case cond="condicion">
     *          contenido si se cumple la condicion
     *      </case>
     *      <!-- Este comentario se eliminara -->
     *      <case cond="condicion">
     *          contenido si se cumple la condicion
     *      </case>
     *      <!-- Este comentario se eliminara -->
     *      <default>
     *          contenido si falla la condicion
     *      </default>
     * </switch>
     */
    private function _compile_switch()
    {
        $objDom = $this->_getObjDOM($this->_strContent);
        $objSwitch = $objDom->getElementsByTagName('switch')->item(0);
        $varSwitch      = $objSwitch->hasAttribute('var') && $objSwitch->getattribute('var') != '' ? $this->_importVar($objSwitch->getAttribute('var')) : null;
        $objFrag = null;
        while (strtolower(@$objSwitch->firstChild->nodeName) == 'case' || @$objSwitch->firstChild->nodeType == XML_COMMENT_NODE || (@$objSwitch->firstChild->nodeType == XML_TEXT_NODE && ctype_space(@$objSwitch->firstChild->textContent))) {
            if (strtolower(@$objSwitch->firstChild->nodeName) == 'case') {
                if (!$objFrag) {
                    $objCase = $objSwitch->firstChild;
                    $bolCond  = $objCase->hasAttribute('cond') ? $this->_checkCond($varSwitch, $objCase->getAttribute('cond')) : isset($varSwitch);
                    $cadCompare = $objCase->hasAttribute('compare') ? $objCase->getAttribute('compare') : 'and';
                    if ($objCase->hasAttribute('and') && !$objCase->hasAttribute('or')) {
                        $bolPase = $bolCond && $this->_checkCond($varSwitch, $objCase->getAttribute('and'));
                    } else if ($objCase->hasAttribute('or') && !$objCase->hasAttribute('and')) {
                        $bolPase = $bolCond || $this->_checkCond($varSwitch, $objCase->getAttribute('or'));
                    } else if ($objCase->hasAttribute('and') && $objCase->hasAttribute('or') && $cadCompare == 'and') {
                        $bolPase = $bolCond && $this->_checkCond($varSwitch, $objCase->getAttribute('and')) || $this->_checkCond($varSwitch, $objCase->getAttribute('or'));
                    } else if ($objCase->hasAttribute('and') && $objCase->hasAttribute('or') && $cadCompare == 'or') {
                        $bolPase = $bolCond || $this->_checkCond($varSwitch, $objCase->getAttribute('or')) && $this->_checkCond($varSwitch, $objCase->getAttribute('and'));
                    } else {
                        $bolPase = $bolCond;
                    }
                    if ($bolPase) {
                        $objFrag = $this->_getElements($objDom, $objCase);
                    }
                }
            }
            $objSwitch->removeChild($objSwitch->firstChild);
        }
        if (strtolower(@$objSwitch->firstChild->nodeName) == 'default') {
            if (!$objFrag) {
                $objFrag = $this->_getElements($objDom, $objSwitch->firstChild);
            }
            $objSwitch->removeChild($objSwitch->firstChild);
        }
        if ($this->_bolClearComment) {
            $this->_clearComments($objSwitch);
        }
        if (!$objFrag) {
            $objSwitch->parentNode->removeChild($objSwitch);
        } else {
            $objSwitch->parentNode->replaceChild($objFrag, $objSwitch);
        }
        $objDom->saveHTML();
        $objPhtml = $objDom->getElementById($this->_randID);
        $this->_strContent = $this->_getHTML($objPhtml);
    }



    /**
     * Compila el TAG foreach
     * (!) Testar arreglos multidimensionales
     * 
     * <!-- La eliminacion de este comentario depende de $_bolClearComment -->
     * <foreach var="variable" key="key" value="value" id="id">
     * </forach>
     */
    private function _compile_foreach()
    {
        $objDom         = $this->_getObjDOM($this->_strContent);
        $objForeach     = $objDom->getElementsByTagName('foreach')->item(0);
        $cadVariable    = $objForeach->hasAttribute('var') && $objForeach->getAttribute('var') != '' ? $objForeach->getAttribute('var') : null;
        $cadClave       = $objForeach->hasAttribute('key') ? $objForeach->getAttribute('key') : 'key';
        $cadValor       = $objForeach->hasAttribute('value') ? $objForeach->getAttribute('value') : 'value';
        $id             = $objForeach->hasAttribute('id') ? $objForeach->getAttribute('id') . '.' : '';
        $content   = $this->_getHTML($objForeach);
        $mixedVar       = $this->_importVar($cadVariable);
        $objFrag        = null;
        $cadProcesada   = '';
        if (is_array($mixedVar) || is_object($mixedVar)) {
            foreach ($mixedVar as $clave => $valor) {
                $cadProcesada .= $content;
                $cadProcesada = $this->_replaceVar($id . $cadVariable . '.' . $cadClave,  is_object($mixedVar) ? $mixedVar->$clave : $mixedVar[$clave], $cadProcesada);
                $cadProcesada = $this->_replaceVar($id . $cadClave,  $clave, $cadProcesada);
                $cadProcesada = $this->_replaceVar($id . $cadValor,  $valor, $cadProcesada);
                $cadProcesada = $this->_replaceVarEtc($id . $cadVariable . '.' . $cadClave,  $id . $cadVariable . '.' . $clave, $cadProcesada);
                $cadProcesada = $this->_replaceQuotes($id . $cadClave, $clave, $cadProcesada);
                $cadProcesada = $this->_replaceQuotes($id . $cadValor, $valor, $cadProcesada);
                $cadProcesada = $this->_replaceQuotes($id . $cadVariable . '.' . $cadClave, $id . $cadVariable . '.' . $clave, $cadProcesada);
                $cadProcesada = $this->_replaceQuotesEtc($id . $cadClave, $id .  $cadVariable . '.' . $clave, $cadProcesada);
                $cadProcesada = $this->_replaceQuotesEtc($id . $cadVariable . '.' . $cadClave, $id . $cadVariable . '.' . $clave, $cadProcesada);
            }
            $objFrag = $this->_convertHTMLinElements($objDom, $cadProcesada);
        }
        if ($this->_bolClearComment) {
            $this->_clearComments($objForeach);
        }
        if (!$objFrag) {
            $objForeach->parentNode->removeChild($objForeach);
        } else {
            $objForeach->parentNode->replaceChild($objFrag, $objForeach);
        }
        $objDom->saveHTML();
        $objPhtml = $objDom->getElementById($this->_randID);
        $this->_strContent = $this->_getHTML($objPhtml);
    }



    /**
     * Compila el TAG for
     * (!) repasar las fechas pasadas por puntos
     * (!) crear una funcion el inicializar fechas para no repetir codigo
     * 
     * <!-- La eliminacion de este comentario depende de $_bolClearComment --> 
     * <for index="i" var="variable" init="0" size="count" order="asc" id="id" offset="+1">
     * </for>
     */
    private function _compile_for()
    {
        $objDom            = $this->_getObjDOM($this->_strContent);
        $objFor            = $objDom->getElementsByTagName('for')->item(0);
        $cadVariable       = $objFor->getAttribute('var');
        $mixedVar          = $this->_importVar($cadVariable);
        $id                = $objFor->hasAttribute('id') ? $objFor->getAttribute('id') . '.' : '';
        $offset            = $objFor->getAttribute('offset');
        $cadIndice         = $objFor->hasAttribute('index') ? $objFor->getAttribute('index')    : 'i';
        $cadTotal          = $objFor->getAttribute('size');
        $init              = $objFor->getAttribute('init');
        $format            = $objFor->hasAttribute('format') ? $objFor->getAttribute('format') : 'd-m-Y';
        $content      = $this->_getHTML($objFor);
        $objFrag           = null;
        $cadProcesada      = '';
        $esCadena          = false;
        $esFecha           = false;

        if ($objFor->hasAttribute('order')) {
            switch (strtolower(trim($objFor->getAttribute('order')))) {
                case 'desc':
                    $asc = false;
                    break;
                case 'asc':
                    $asc = true;
                    break;
            }
        } else {
            $asc = true;
        }

        if ($init == '') {
            $arrIndice = explode('.', $cadIndice);
            $cadIndice = $arrIndice[0];
            $init = isset($arrIndice[1]) ? $arrIndice[1] : 0;
        }

        if ($objFor->hasAttribute('var') && !empty($mixedVar)) { // solo variables
            if (is_array($mixedVar)) { // arreglos
                switch ($cadTotal) {
                    case '':
                    case 'size':
                    case 'sizeof':
                    case 'count':
                    case 'length':
                        $max = sizeof($mixedVar);
                        break;
                    default:
                        $max = $cadTotal;
                        break;
                }
            } else if (is_string($mixedVar)) { // cadenas
                switch ($cadTotal) {
                    case '':
                    case 'size':
                    case 'strlen':
                    case 'length':
                        $max = strlen($mixedVar);
                        break;
                    default:
                        $max = $cadTotal;
                        break;
                }
            }
        } else { // no variables solo index
            if (is_numeric($cadTotal)) { // numeros
                $max = (int)$cadTotal;
            } else {
                $patronFecha = '/^\s*?([0-9]{1,2})[\.\-\/\s]([0-9]{1,2})[\.\-\/\s]([0-9]{4})\s*?([0-9]{1,2}[:][0-9]{1,2})?([:][0-9]{1,2})?\s*?$/';
                if (preg_match($patronFecha, $init, $arrResult)) {
                    $fecha = $arrResult[1] . '-' .  $arrResult[2] . '-' . $arrResult[3] . (isset($arrResult[4]) ? ' ' . $arrResult[4]  . (isset($arrResult[5]) ?  $arrResult[5] : '') : '');
                    $init = strtotime($fecha);
                    if (preg_match($patronFecha, $cadTotal, $arrResult)) { // fechas
                        $fecha = $arrResult[1] . '-' .  $arrResult[2] . '-' . $arrResult[3] . (isset($arrResult[4]) ? ' ' . $arrResult[4]  . (isset($arrResult[5]) ?  $arrResult[5] : '') : '');
                        $max = strtotime($fecha);
                        $max += 86400;
                    } else {
                        $max = strtotime('now');
                    }
                    $esFecha = true;
                } else { // cadenas
                    $max = $cadTotal;
                    if (preg_match('/[a-zA-Z]/', $max) || preg_match('/[a-zA-Z]/', $init)) {
                        if (preg_match('/[A-Z]/', $max) || preg_match('/[A-Z]/', $init)) {
                            $max = strtoupper($max);
                            $init = strtoupper($init);
                        }
                        // seguridad por tiempo limite de procesamiento
                        $max = strlen($max) > 3 ? substr($max, 0, 3) : $max;
                        $init = strlen($init) > 3 ? substr($init, 0, 3) : $init;
                        $max++;
                        $esCadena = true;
                    }
                }
            }
        }
        for ($esCadena || $asc ? $i = $init : $i = $max - 1; $esCadena ? ($i != $max) : ($asc ? $i < $max : $init <= $i); $esFecha ? ($asc ? $i += 86400 : $i -= 86400) : ($esCadena || $asc ? $i++ : $i--)) {
            $cadProcesada .= $content;
            if ($cadVariable != '') {
                if (@is_array($mixedVar) || @is_scalar($mixedVar[$i])) {
                    $cadProcesada = @$this->_replaceVar($id . $cadVariable . '.' . $cadIndice,  $mixedVar[$i], $cadProcesada);
                }
                $cadProcesada = $this->_replaceVarEtc($id . $cadVariable . '.' . $cadIndice, $id . $cadVariable . '.' . $i, $cadProcesada);
                $cadProcesada = $this->_replaceQuotes($id . $cadVariable . '.' . $cadIndice, $id . $cadVariable . '.' . $i, $cadProcesada);
                $cadProcesada = $this->_replaceQuotesEtc($id . $cadVariable . '.' . $cadIndice, $id . $cadVariable . '.' . $i, $cadProcesada);
            }
            if ($esFecha) {
                $cadProcesada = $this->_replaceVar($id . $cadIndice,  date($format, $i), $cadProcesada);
                $cadProcesada = $this->_replaceQuotes($id . $cadIndice, date($format, $i), $cadProcesada);
            } else {
                if ($offset != '') {
                    eval('$o=' . $i . $offset . ';');
                }
                $cadProcesada = $this->_replaceVar($id . $cadIndice,  isset($o) ? $o : $i, $cadProcesada);
                $cadProcesada = $this->_replaceQuotes($id . $cadIndice, $i, $cadProcesada);
            }
        }
        $objFrag = $this->_convertHTMLinElements($objDom, $cadProcesada);
        if ($this->_bolClearComment) {
            $this->_clearComments($objFor);
        }
        if (!$objFrag) {
            $objFor->parentNode->removeChild($objFor);
        } else {
            $objFor->parentNode->replaceChild($objFrag, $objFor);
        }
        $objDom->saveHTML();
        $objPhtml = $objDom->getElementById($this->_randID);
        $this->_strContent = $this->_getHTML($objPhtml);
    }



    private function _seguridadComentarios()
    {
        $objDom     = $this->_getObjDOM($this->_strContent);
        //$objDom->preserveWhiteSpace = false;
        //$objDom->formatOutput = true;
        $objXPath = new DOMXPath($objDom);
        $comments = $objXPath->query('//comment()');
        foreach ($comments as $comment) {
            // (*) eliminar comentarios si commpress lo pide
            $pattern = '/<(if|elseif|else|switch|foreach|while|include|for[\s])[\s]*.*?>(.*?)<\/(if|elseif|else|switch|foreach|while|include|for)>/is';
            if (preg_match($pattern, $comment->textContent)) {
                $comment->parentNode->removeChild($comment);
            }
        }
        $objDom->saveHTML();
        $objPhtml = $objDom->getElementById($this->_randID);
        $this->_strContent = $this->_getHTML($objPhtml);
    }


    /**
     * Compila el TAG while
     * <do>
     *      contenido de do
     * </do>
     * <while var="variable" init="0" fin="count" index="i">
     *      contenido del bloque while
     * </while>
     */
    private function _compile_while()
    {
        return (true);
    }



    /**
     * Se encarga de compilar todo el contenido phtml
     */
    private function _compile()
    {
        $this->_seguridadComentarios();
        $this->_compile_const();
        $this->_compile_var();
        $pattern = '/<(if|switch|foreach|while|include|for(?!.)*)[\s]*.*?>(.*?)<\/\1>/is';
        while (preg_match($pattern, $this->_strContent, $arrResult)) {
            print_pre($arrResult[1]);
            $nombreTag = strtolower(trim($arrResult[1]));
            $this->{'_compile_' . $nombreTag}();
        }
        $this->_compile_var(true);
    }


    /**
     * Devuelve el contenido compilado
     * 
     * @param mixed $index
     * @return string
     */
    public function output($index = null)
    {
        if (isset($index)) {
            $this->_strContent = $this->_arrContent[$index];
        } else {
            $this->_strContent = $this->_arrContent[$this->_randID];
        }
        $this->_compile();
        /* if ($this->_bolCompress) {
            $this->_strContent = preg_replace('/(\\n|\\t|\\r|\\s+)/', ' ', $this->_strContent);
        } */
        return ($this->_strContent);
    }
}
