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
     * @var object formatPhtml $_format 
     */
    private $_format;

    /**
     * @var object condPhtml $_cond 
     */
    private $_cond;

    /**
     * @var boolean $_bolIsset
     */
    private $_bolIsset;


    /**
     * Inicializar los componentes necesarios 
     */
    public function __construct()
    {
        $this->_openVar                    = defined('PHTML_OPEN_VAR')          ? PHTML_OPEN_VAR          : '{{';
        $this->_closeVar                   = defined('PHTML_CLOSE_VAR')         ? PHTML_CLOSE_VAR         : '}}';
        $this->_openConst                  = defined('PHTML_OPEN_CONST')        ? PHTML_OPEN_CONST        : '[[';
        $this->_closeConst                 = defined('PHTML_CLOSE_CONST')       ? PHTML_CLOSE_CONST       : ']]';
        $this->_bolEjecutarMetodos         = defined('PHTML_EXECUTE_METHOD')    ? PHTML_EXECUTE_METHOD    : true;
        $this->_bolPermitted_GLOBALS       = defined('PHTML_PERMITTED_GLOBALS') ? PHTML_PERMITTED_GLOBALS : true;
        $this->_bolPermitted_SERVER        = defined('PHTML_PERMITTED_SERVER')  ? PHTML_PERMITTED_SERVER  : false;
        $this->_bolPermitted_GET           = defined('PHTML_PERMITTED_GET')     ? PHTML_PERMITTED_GET     : true;
        $this->_bolPermitted_POST          = defined('PHTML_PERMITTED_POST')    ? PHTML_PERMITTED_POST    : true;
        $this->_bolPermitted_FILES         = defined('PHTML_PERMITTED_FILES')   ? PHTML_PERMITTED_FILES   : false;
        $this->_bolPermitted_COOKIE        = defined('PHTML_PERMITTED_COOKIE')  ? PHTML_PERMITTED_COOKIE  : true;
        $this->_bolPermitted_SESSION       = defined('PHTML_PERMITTED_SESSION') ? PHTML_PERMITTED_SESSION : true;
        $this->_bolPermitted_REQUEST       = defined('PHTML_PERMITTED_REQUEST') ? PHTML_PERMITTED_REQUEST : true;
        $this->_bolPermitted_ENV           = defined('PHTML_PERMITTED_ENV')     ? PHTML_PERMITTED_ENV     : false;
        $this->_bolCompress                = defined('PHTML_COMPRESS')          ? PHTML_COMPRESS          : false;
        $this->_bolExecutePhp              = defined('PHTML_EXECUTE_PHP')       ? PHTML_EXECUTE_PHP       : true;
        $this->_bolClearComment            = defined('PHTML_CLEAR_COMMENT')     ? PHTML_CLEAR_COMMENT     : true;
        $this->_bolIsset                   = defined('PHTML_COND_ISSET')        ? PHTML_COND_ISSET        : false;
        $this->_cadEncoding                = defined('PHTML_ENCODING')          ? PHTML_ENCODING          : 'UTF-8';
        $key                               = defined('PHTML_STR_KEY')           ? PHTML_STR_KEY           : 'phtml';
        $this->_format                     = new formatPhtml;
        $this->_cond                       = new condPhtml;
        $this->_randID                     = $this->_createRandID($key);
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
        $dom = new DOMDocument;
        libxml_use_internal_errors(true);
        $html = '<phtml id="' . $this->_randID . '">' . $html . '</phtml>';
        $htmlEncoded = mb_convert_encoding($html, 'HTML-ENTITIES', $this->_cadEncoding);
        @$dom->loadHTML($htmlEncoded);
        libxml_clear_errors();
        return ($dom);
    }


    /**
     * Agrega un objeto de formatear personalizado
     * 
     * @param object $objFormat un objeto de tipo formatPhtml
     */
    public function userFormat(formatPhtml $format)
    {
        if ($format instanceof $format) {
            $this->_format = $format;
        }
    }


    /**
     * Agrega un objeto de condiciones personalizado
     * 
     * @param object $cond un objeto de tipo condPhtml
     */
    public function userCond(condPhtml $cond)
    {
        if ($cond instanceof condPhtml) {
            $this->_cond = $cond;
        }
    }


    /**
     * Convierte la cadena html en elementos DOMDocument
     * 
     * @param object $dom
     * @param string $html
     * @return object
     */
    private function _convertHTMLinElements(DOMDocument $dom, $html)
    {
        $thisDom = $this->_getObjDOM($html);
        $phtml = $thisDom->getElementById($this->_randID);
        $frag = $dom->createDocumentFragment();
        $thisNode = $phtml->firstChild;
        while ($thisNode) {
            $frag->appendChild($dom->importNode($thisNode, true));
            $thisNode = $thisNode->nextSibling;
        }
        return ($frag);
    }



    /**
     * Recoge todo los elementos del nodo
     * 
     * @param object DOMDocument $dom
     * @param object DOMNode $node
     * @return object DOMDocumentFragment 
     */
    private function _getElements(DOMDocument $dom, DOMNode $node)
    {
        $frag = $dom->createDocumentFragment();
        while ($thisNode = $node->firstChild) {
            $frag->appendChild($dom->importNode($thisNode, true));
            $thisNode = $thisNode->nextSibling;
        }
        return ($frag);
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
        if ($this->_bolClearComment) {
            while (@$node->previousSibling->nodeType == XML_COMMENT_NODE || (@$node->previousSibling->nodeType == XML_TEXT_NODE && ctype_space(@$node->previousSibling->textContent))) {
                $node->parentNode->removeChild($node->previousSibling);
            }
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
                        return ($this->_arrVar[$arr[0]]);
                    }
                case 2:
                    switch ($arr[0]) { // obtener super globales
                        case 'GLOBALS':
                            if ($this->_bolPermitted_GLOBALS && isset($_GLOBALS[$arr[1]])) {
                                return ($GLOBALS[$arr[1]]);
                            }
                            break;
                        case '_SERVER':
                            if ($this->_bolPermitted_SERVER && isset($_SERVER[$arr[1]])) {
                                return ($_SERVER[$arr[1]]);
                            }
                            break;
                        case '_GET':
                            if ($this->_bolPermitted_GET && isset($_GET[$arr[1]])) {
                                return ($_GET[$arr[1]]);
                            }
                            break;
                        case '_POST':
                            if ($this->_bolPermitted_POST && isset($_POST[$arr[1]])) {
                                return ($_POST[$arr[1]]);
                            }
                            break;
                        case '_FILES':
                            if ($this->_bolPermitted_FILES && isset($_FILES[$arr[1]])) {
                                return ($_FILES[$arr[1]]);
                            }
                            break;
                        case '_COOKIE':
                            if ($this->_bolPermitted_COOKIE && isset($_COOKIE[$arr[1]])) {
                                return ($_COOKIE[$arr[1]]);
                            }
                            break;
                        case '_SESSION':
                            if ($this->_bolPermitted_SESSION && isset($_SESSION[$arr[1]])) {
                                return ($_SESSION[$arr[1]]);
                            }
                            break;
                        case '_REQUEST':
                            if ($this->_bolPermitted_REQUEST && isset($_REQUEST[$arr[1]])) {
                                return ($_REQUEST[$arr[1]]);
                            }
                            break;
                        case '_ENV':
                            if ($this->_bolPermitted_ENV && isset($_ENV[$arr[1]])) {
                                return ($_ENV[$arr[1]]);
                            }
                            break;
                        default:
                            if (isset($this->_arrVar[$arr[0]]) && is_array($this->_arrVar[$arr[0]])) {
                                if (isset($this->_arrVar[$arr[0]][$arr[1]])) {
                                    return ($this->_arrVar[$arr[0]][$arr[1]]); // arreglo[]
                                }
                            } else if (isset($this->_arrVar[$arr[0]]) && is_object($this->_arrVar[$arr[0]])) {
                                if (property_exists($this->_arrVar[$arr[0]], $arr[1])) {
                                    return ($this->_arrVar[$arr[0]]->{$arr[1]}); // objeto->propiedad
                                } else if ($this->_bolEjecutarMetodos && method_exists($this->_arrVar[$arr[0]], $arr[1])) {
                                    return ($this->_arrVar[$arr[0]]->{$arr[1]}()); // objeto->metodo()
                                }
                            }
                            break;
                    }
                    break;
                case 3:
                    /** (*) agregar soporte variables globales multinivel */
                    if (isset($this->_arrVar[$arr[0]][$arr[1]]) && is_array($this->_arrVar[$arr[0]][$arr[1]])) {
                        if (isset($this->_arrVar[$arr[0]][$arr[1]][$arr[2]])) {
                            return ($this->_arrVar[$arr[0]][$arr[1]][$arr[2]]); // arreglo[][]
                        }
                    } else if (isset($this->_arrVar[$arr[0]]) && is_object($this->_arrVar[$arr[0]])) {
                        if ($this->_bolEjecutarMetodos && method_exists($this->_arrVar[$arr[0]], $arr[1])) {
                            return ($this->_arrVar[$arr[0]]->{$arr[1]}($arr[2])); // objeto->metodo(param)
                        }
                    } else if (isset($this->_arrVar[$arr[0]][$arr[1]]) && is_object($this->_arrVar[$arr[0]][$arr[1]])) {
                        if (property_exists($this->_arrVar[$arr[0]][$arr[1]], $arr[2])) {
                            return ($this->_arrVar[$arr[0]][$arr[1]]->{$arr[2]}); // objeto->propiedad
                        } else if ($this->_bolEjecutarMetodos && method_exists($this->_arrVar[$arr[0]][$arr[1]], $arr[2])) {
                            return ($this->_arrVar[$arr[0]][$arr[1]]->{$arr[2]}()); // objeto->metodo()
                        }
                    }
                    break;
                case 4:
                    /** (*) agregar soporte variables globales multinivel */
                    if (isset($this->_arrVar[$arr[0]][$arr[1]][$arr[2]]) && is_array($this->_arrVar[$arr[0]][$arr[1]][$arr[2]])) {
                        if (isset($this->_arrVar[$arr[0]][$arr[1]][$arr[2]][$arr[3]])) {
                            return ($this->_arrVar[$arr[0]][$arr[1]][$arr[2]][$arr[3]]); // arreglo[][][]
                        }
                    } else if (isset($this->_arrVar[$arr[0]]) && is_object($this->_arrVar[$arr[0]])) {
                        if ($this->_bolEjecutarMetodos && method_exists($this->_arrVar[$arr[0]], $arr[1])) {
                            return ($this->_arrVar[$arr[0]]->{$arr[1]}($arr[2], $arr[3])); // objeto->metodo(param, param)
                        }
                    } else if (isset($this->_arrVar[$arr[0]]) && is_object($this->_arrVar[$arr[0]][$arr[1]])) {
                        if ($this->_bolEjecutarMetodos && method_exists($this->_arrVar[$arr[0]][$arr[1]], $arr[2])) {
                            return ($this->_arrVar[$arr[0]][$arr[1]]->{$arr[2]}($arr[3])); // objeto->metodo(param)
                        }
                    } else if (isset($this->_arrVar[$arr[0]][$arr[1]][$arr[2]]) && is_object($this->_arrVar[$arr[0]][$arr[1]][$arr[2]])) {
                        if (property_exists($this->_arrVar[$arr[0]][$arr[1]][$arr[2]], $arr[3])) {
                            return ($this->_arrVar[$arr[0]][$arr[1]][$arr[2]]->{$arr[3]}); // objeto->propiedad
                        } else if ($this->_bolEjecutarMetodos && method_exists($this->_arrVar[$arr[0]][$arr[1]][$arr[2]], $arr[3])) {
                            return ($this->_arrVar[$arr[0]][$arr[1]][$arr[2]]->{$arr[3]}()); // objeto->metodo()
                        }
                    }
                    break;
            }
        } else {
            if (is_numeric($cadVariable)) { // solo numeros
                return ($cadVariable);
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
            if (sizeof($arrCond) == 1 && method_exists($this->_cond, 'phtml_' . $arrCond[0])) {
                return ($this->_cond->{'phtml_' . $arrCond[0]}($mixedVar));
            } else if (sizeof($arrCond) == 2 && method_exists($this->_cond, 'phtml_' . $arrCond[0])) {
                return ($this->_cond->{'phtml_' . $arrCond[0]}($mixedVar, $arrCond[1]));
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
        $pattern = '/(?i)var(?-i)\s*=\s*([\'"])\s*' . str_replace('.', '\.', $search) . '\.(.*?)\s*\1/';
        while (@preg_match($pattern, $subject, $arrResult)) {
            $replacement = 'var="' .  $replacement . '.' . $arrResult[2] . '"';
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
        $pattern = '/(?i)var(?-i)\s*=\s*([\'"])\s*' . str_replace('.', '\.', $search) . '\s*\1/';
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
                if (sizeof($arrVar) == 2 && method_exists($this->_format, 'phtml_' . $arrVar[0])) { // {{func_format.mixedVar}}
                    $mixedVar = $this->_importVar($arrVar[1]);
                    if (!empty($mixedVar) && isset($mixedVar)) {
                        $content = $this->_format->{'phtml_' . $arrVar[0]}($mixedVar);
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
        $dom = $this->_getObjDOM($this->_strContent);
        $objInclude = $dom->getElementsByTagName('include')->item(0);
        $this->_clearComments($objInclude);
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
            $frag = $this->_convertHTMLinElements($dom, $content);
            $objInclude->parentNode->replaceChild($frag, $objInclude);
        } else {
            $objInclude->parentNode->removeChild($objInclude);
        }
        $dom->saveHTML();
        $phtml = $dom->getElementById($this->_randID);
        $this->_strContent = $this->_getHTML($phtml);
        // modificar esto
        $this->_compileCommentedPhtml();
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
        $dom       = $this->_getObjDOM($this->_strContent);
        $if        = $dom->getElementsByTagName('if')->item(0);
        $varIf     = $if->hasAttribute('var') && $if->getattribute('var') != '' ? $this->_importVar($if->getAttribute('var')) : null;
        $bolCondIf = $if->hasAttribute('cond') ? $this->_checkCond($varIf, $if->getAttribute('cond')) : ($this->_bolIsset ? isset($varIf) : false); // agregar cond por defecto
        $compare   = $if->hasAttribute('compare') ? strtolower(trim($if->getAttribute('compare'))) : 'and';
        $frag      = null;
        if ($if->hasAttribute('and') && !$if->hasAttribute('or')) {
            $bolPaseIf = $bolCondIf && $this->_checkCond($varIf, $if->getAttribute('and'));
        } else if ($if->hasAttribute('or') && !$if->hasAttribute('and')) {
            $bolPaseIf = $bolCondIf || $this->_checkCond($varIf, $if->getAttribute('or'));
        } else if ($if->hasAttribute('and') && $if->hasAttribute('or') && $compare == 'and') {
            $bolPaseIf = $bolCondIf && $this->_checkCond($varIf, $if->getAttribute('and')) || $this->_checkCond($varIf, $if->getAttribute('or'));
        } else if ($if->hasAttribute('and') && $if->hasAttribute('or') && $compare == 'or') {
            $bolPaseIf = $bolCondIf || $this->_checkCond($varIf, $if->getAttribute('or')) && $this->_checkCond($varIf, $if->getAttribute('and'));
        } else {
            $bolPaseIf = $bolCondIf;
        }
        if ($bolPaseIf) {
            $frag = $this->_getElements($dom, $if);
            while (strtolower(@$if->nextSibling->nodeName) == 'elseif' || strtolower(@$if->nextSibling->nodeName) == 'else' || @$if->nextSibling->nodeType == XML_COMMENT_NODE || (@$if->nextSibling->nodeType == XML_TEXT_NODE && ctype_space(@$if->nextSibling->textContent))) {
                $if->parentNode->removeChild($if->nextSibling);
            }
        } else {
            while (strtolower(@$if->nextSibling->nodeName) == 'elseif' || @$if->nextSibling->nodeType == XML_COMMENT_NODE || (@$if->nextSibling->nodeType == XML_TEXT_NODE && ctype_space(@$if->nextSibling->textContent))) {
                if (strtolower(@$if->nextSibling->nodeName) == 'elseif') {
                    $elseif = $if->nextSibling;
                    if (!$frag) {
                        $varElseIf     = $elseif->hasAttribute('var') && $elseif->getattribute('var') != '' ? $this->_importVar($elseif->getAttribute('var')) : null;
                        $bolCondElseIf = $elseif->hasAttribute('cond') ? $this->_checkCond($varElseIf, $elseif->getAttribute('cond')) : ($this->_bolIsset ? isset($varElseIf) : false);
                        $compareElseif = $elseif->hasAttribute('compare') ? strtolower(trim($elseif->getAttribute('compare'))) : 'and';
                        if ($elseif->hasAttribute('and') && !$elseif->hasAttribute('or')) {
                            $bolPaseElseIf = $bolCondElseIf && $this->_checkCond($varElseIf, $elseif->getAttribute('and'));
                        } else if ($elseif->hasAttribute('or') && !$elseif->hasAttribute('and')) {
                            $bolPaseElseIf = $bolCondElseIf || $this->_checkCond($varElseIf, $elseif->getAttribute('or'));
                        } else if ($elseif->hasAttribute('and') && $elseif->hasAttribute('or') && $compareElseif == 'and') {
                            $bolPaseElseIf = $bolCondElseIf && $this->_checkCond($varElseIf, $elseif->getAttribute('and')) || $this->_checkCond($varElseIf, $elseif->getAttribute('or'));
                        } else if ($elseif->hasAttribute('and') && $elseif->hasAttribute('or') && $compareElseif == 'or') {
                            $bolPaseElseIf = $bolCondElseIf || $this->_checkCond($varElseIf, $elseif->getAttribute('or')) && $this->_checkCond($varElseIf, $elseif->getAttribute('and'));
                        } else {
                            $bolPaseElseIf = $bolCondElseIf;
                        }
                        if ($bolPaseElseIf) {
                            $frag = $this->_getElements($dom, $elseif);
                        }
                    }
                }
                $if->parentNode->removeChild($if->nextSibling);
            }
            if (strtolower(@$if->nextSibling->nodeName) == 'else') {
                $else = $if->nextSibling;
                if (!$frag) {
                    $frag = $this->_getElements($dom, $else);
                }
                $if->parentNode->removeChild($else);
            }
        }
        $this->_clearComments($if);
        if (!$frag) {
            $if->parentNode->removeChild($if);
        } else {
            $if->parentNode->replaceChild($frag, $if);
        }
        $dom->saveHTML();
        $phtml = $dom->getElementById($this->_randID);
        $this->_strContent = $this->_getHTML($phtml);
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
        $dom    = $this->_getObjDOM($this->_strContent);
        $switch = $dom->getElementsByTagName('switch')->item(0);
        $var    = $switch->hasAttribute('var') && $switch->getattribute('var') != '' ? $this->_importVar($switch->getAttribute('var')) : null;
        $frag   = null;
        while (strtolower(@$switch->firstChild->nodeName) == 'case' || @$switch->firstChild->nodeType == XML_COMMENT_NODE || (@$switch->firstChild->nodeType == XML_TEXT_NODE && ctype_space(@$switch->firstChild->textContent))) {
            if (strtolower(@$switch->firstChild->nodeName) == 'case') {
                if (!$frag) {
                    $case = $switch->firstChild;
                    $bolCond  = $case->hasAttribute('cond') ? $this->_checkCond($var, $case->getAttribute('cond')) : isset($var);
                    $compare = $case->hasAttribute('compare') ? $case->getAttribute('compare') : 'and';
                    if ($case->hasAttribute('and') && !$case->hasAttribute('or')) {
                        $bolPase = $bolCond && $this->_checkCond($var, $case->getAttribute('and'));
                    } else if ($case->hasAttribute('or') && !$case->hasAttribute('and')) {
                        $bolPase = $bolCond || $this->_checkCond($var, $case->getAttribute('or'));
                    } else if ($case->hasAttribute('and') && $case->hasAttribute('or') && $compare == 'and') {
                        $bolPase = $bolCond && $this->_checkCond($var, $case->getAttribute('and')) || $this->_checkCond($var, $case->getAttribute('or'));
                    } else if ($case->hasAttribute('and') && $case->hasAttribute('or') && $compare == 'or') {
                        $bolPase = $bolCond || $this->_checkCond($var, $case->getAttribute('or')) && $this->_checkCond($var, $case->getAttribute('and'));
                    } else {
                        $bolPase = $bolCond;
                    }
                    if ($bolPase) {
                        $frag = $this->_getElements($dom, $case);
                    }
                }
            }
            $switch->removeChild($switch->firstChild);
        }
        if (strtolower(@$switch->firstChild->nodeName) == 'default') {
            if (!$frag) {
                $frag = $this->_getElements($dom, $switch->firstChild);
            }
            $switch->removeChild($switch->firstChild);
        }
        $this->_clearComments($switch);
        if (!$frag) {
            $switch->parentNode->removeChild($switch);
        } else {
            $switch->parentNode->replaceChild($frag, $switch);
        }
        $dom->saveHTML();
        $phtml = $dom->getElementById($this->_randID);
        $this->_strContent = $this->_getHTML($phtml);
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
        $dom       = $this->_getObjDOM($this->_strContent);
        $foreach   = $dom->getElementsByTagName('foreach')->item(0);
        $strVar    = $foreach->hasAttribute('var') && $foreach->getAttribute('var') != '' ? $foreach->getAttribute('var') : null;
        $strKey    = $foreach->hasAttribute('key') ? $foreach->getAttribute('key') : 'key';
        $strValue  = $foreach->hasAttribute('value') ? $foreach->getAttribute('value') : 'value';
        $id        = $foreach->hasAttribute('id') ? $foreach->getAttribute('id') . '.' : '';
        $content   = $this->_getHTML($foreach);
        $mixedVar  = $this->_importVar($strVar);
        $frag      = null;
        $processed = '';
        if (is_array($mixedVar) || is_object($mixedVar)) {
            foreach ($mixedVar as $key => $value) {
                $processed .= $content;
                $processed = $this->_replaceVar($id . $strVar . '.' . $strKey,  is_object($mixedVar) ? $mixedVar->$key : $mixedVar[$key], $processed);
                $processed = $this->_replaceVar($id . $strKey,  $key, $processed);
                $processed = $this->_replaceVar($id . $strValue,  $value, $processed);
                $processed = $this->_replaceVarEtc($id . $strVar . '.' . $strKey,  $id . $strVar . '.' . $key, $processed);
                $processed = $this->_replaceQuotes($id . $strKey, $key, $processed);
                $processed = $this->_replaceQuotes($id . $strValue, $value, $processed);
                $processed = $this->_replaceQuotes($id . $strVar . '.' . $strKey, $id . $strVar . '.' . $key, $processed);
                $processed = $this->_replaceQuotesEtc($id . $strKey, $id .  $strVar . '.' . $key, $processed);
                $processed = $this->_replaceQuotesEtc($id . $strVar . '.' . $strKey, $id . $strVar . '.' . $key, $processed);
            }
            $frag = $this->_convertHTMLinElements($dom, $processed);
        }
        $this->_clearComments($foreach);
        if (!$frag) {
            $foreach->parentNode->removeChild($foreach);
        } else {
            $foreach->parentNode->replaceChild($frag, $foreach);
        }
        $dom->saveHTML();
        $phtml = $dom->getElementById($this->_randID);
        $this->_strContent = $this->_getHTML($phtml);
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
        $dom       = $this->_getObjDOM($this->_strContent);
        $for       = $dom->getElementsByTagName('for')->item(0);
        $strVar    = $for->getAttribute('var');
        $mixedVar  = $this->_importVar($strVar);
        $id        = $for->hasAttribute('id') ? $for->getAttribute('id') . '.' : '';
        $offset    = $for->getAttribute('offset');
        $index     = $for->hasAttribute('index') ? $for->getAttribute('index')    : 'i';
        $init      = $for->getAttribute('init');
        $content   = $this->_getHTML($for);
        $frag      = null;
        $processed = '';
        $asc = $for->hasAttribute('order') && strtolower(trim($for->getAttribute('order'))) == 'desc' ? false : true;

        if ($init == '') {
            $arrIndex = explode('.', $index, 2);
            $index = $arrIndex[0];
            $init = isset($arrIndex[1]) ? $arrIndex[1] : 0;
        }

        if(is_array($mixedVar)) {
            $max = sizeof($mixedVar);
        }
        if(is_string($mixedVar)) {
            $max = strlen($mixedVar);
        }

        for ($asc ? $i = $init : $i = $max - 1; $asc ? $i < $max : $init <= $i; $asc ? $i++ : $i--) {
            $processed .= $content;
            $processed = @$this->_replaceVar(     $id . $strVar . '.' . $index,  $mixedVar[$i], $processed);
            $processed = $this->_replaceVarEtc(   $id . $strVar . '.' . $index, $id . $strVar . '.' . $i, $processed);
            $processed = $this->_replaceQuotes(   $id . $strVar . '.' . $index, $id . $strVar . '.' . $i, $processed);
            $processed = $this->_replaceQuotesEtc($id . $strVar . '.' . $index, $id . $strVar . '.' . $i, $processed);
            $processed = $this->_replaceVar(      $id . $index,  $offset != '' ? array_sum(array($i, $offset)) : $i, $processed);
            $processed = $this->_replaceQuotes(   $id . $index, $i, $processed);
        }
        $frag = $this->_convertHTMLinElements($dom, $processed);

        $this->_clearComments($for);
        if (!$frag) {
            $for->parentNode->removeChild($for);
        } else {
            $for->parentNode->replaceChild($frag, $for);
        }
        $dom->saveHTML();
        $phtml = $dom->getElementById($this->_randID);
        $this->_strContent = $this->_getHTML($phtml);
    }



    private function _compileCommentedPhtml()
    {
        $dom     = $this->_getObjDOM($this->_strContent);
        //$dom->preserveWhiteSpace = false;
        //$dom->formatOutput = true;
        $objXPath = new DOMXPath($dom);
        $comments = $objXPath->query('//comment()');
        foreach ($comments as $comment) {
            // (*) eliminar comentarios si commpress lo pide
            $pattern = '/<(if(?!.)*|elseif(?!.)*|else(?!.)*|switch(?!.)*|foreach(?!.)*|while(?!.)*|include(?!.)*|for(?!.)*)[\s]*.*?>(.*?)<\/\1>/is';
            if (preg_match($pattern, $comment->textContent)) {
                $comment->parentNode->removeChild($comment);
            }
        }
        $dom->saveHTML();
        $phtml = $dom->getElementById($this->_randID);
        $this->_strContent = $this->_getHTML($phtml);
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
        $this->_compileCommentedPhtml();
        $this->_compile_const();
        $this->_compile_var();
        $pattern = '/<(if(?!.)*|switch(?!.)*|foreach(?!.)*|while(?!.)*|include(?!.)*|for(?!.)*)[\s]*.*?>(.*?)<\/\1>/is';
        while (preg_match($pattern, $this->_strContent, $arrResult)) {
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
        $this->_strContent = isset($index) ? $this->_arrContent[$index] :  $this->_arrContent[$this->_randID];
        $this->_compile();
        /* if ($this->_bolCompress) {
            $this->_strContent = preg_replace('/(\\n|\\t|\\r|\\s+)/', ' ', $this->_strContent);
        } */
        return ($this->_strContent);
    }
}
