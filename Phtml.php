<?php

class Phtml
{

    /**
     * @var string $_cadContenido
     */
    private $_cadContenido = '';

    /**
     * @var array $_arrContenido
     */
    private $_arrContenido = array('PHTML' => '');

    /**
     * @var boolean $_bolCapturado
     */
    private $_bolCapturado = false;

    /**
     * @var string $_abreVariable
     */
    private $_abreVariable;

    /**
     * @var string $_cierraVariable
     */
    private $_cierraVariable;

    /**
     * @var string $_abreConstante
     */
    private $_abreConstante;

    /**
     * @var string $_cierraConstante
     */
    private $_cierraConstante;

    /**
     * @var boolean $_bolPermitir_GLOBALS
     */
    private $_bolPermitir_GLOBALS;

    /**
     * @var boolean $_bolPermitir_SERVER
     */
    private $_bolPermitir_SERVER;

    /**
     * @var boolean $_bolPermitir_GET
     */
    private $_bolPermitir_GET;

    /**
     * @var boolean $_bolPermitir_POST
     */
    private $_bolPermitir_POST;

    /**
     * @var boolean $_bolPermitir_FILES
     */
    private $_bolPermitir_FILES;

    /**
     * @var boolean $_bolPermitir_COOKIE
     */
    private $_bolPermitir_COOKIE;

    /**
     * @var boolean $_bolPermitir_SESSION
     */
    private $_bolPermitir_SESSION;

    /**
     * @var boolean $_bolPermitir_REQUEST
     */
    private $_bolPermitir_REQUEST;

    /**
     * @var boolean $_bolPermitir_ENV
     */
    private $_bolPermitir_ENV;

    /**
     * @var boolean $_bolEjecutarPhp
     */
    private $_bolEjecutarPhp;

    /**
     * @var boolean $_bolComprimir
     */
    private $_bolComprimir;

    /**
     * @var array $_arrVariables
     */
    private $_arrVariables = array();

    /**
     * @var boolean $_bolEjecutarMetodos
     */
    private $_bolEjecutarMetodos;

    /**
     * @var string $_idAleatorio
     */
    private $_idAleatorio;

    /**
     * @var boolean $_bolEliminarComentario
     */
    private $_bolEliminarComentario;


    /**
     * @var string $_cadEncoding
     */
    private $_cadEncoding;


    /**
     * @var array $_arrMetodos
     * (*) agregar un objeto que contenga metodos de formato
     */
    private $_arrMetodos = array(
        'upper', 'strtoupper', 'uppercase', 'lower',
        'strtolower', 'lowercase', 'ucwords', 'camelcase',
        'ucfirst', 'urlencode', 'urldecode',
        'trim', 'rtrim', 'ltrim', 'htmlentities',
        'html_entity_decode', 'addslashes', 'stripcslashes',
        'htmlspecialchars', 'strlen'
    );


    /**
     * Inicializar los componentes necesarios
     */
    public function __construct()
    {
        $this->_abreVariable          = defined('PHTML_ABRE_VARIABLE')       ? PHTML_ABRE_VARIABLE       : '{{';
        $this->_cierraVariable        = defined('PHTML_CIERRA_VARIABLE')     ? PHTML_CIERRA_VARIABLE     : '}}';
        $this->_abreConstante         = defined('PHTML_ABRE_CONSTANTE')      ? PHTML_ABRE_CONSTANTE      : '[[';
        $this->_cierraConstante       = defined('PHTML_CIERRA_CONSTANTE')    ? PHTML_CIERRA_CONSTANTE    : ']]';
        $this->_bolPermitir_GLOBALS   = defined('PHTML_PERMITIR_GLOBALS')    ? PHTML_PERMITIR_GLOBALS    : true;
        $this->_bolPermitir_SERVER    = defined('PHTML_PERMITIR_SERVER')     ? PHTML_PERMITIR_SERVER     : false;
        $this->_bolPermitir_GET       = defined('PHTML_PERMITIR_GET')        ? PHTML_PERMITIR_GET        : true;
        $this->_bolPermitir_POST      = defined('PHTML_PERMITIR_POST')       ? PHTML_PERMITIR_POST       : true;
        $this->_bolPermitir_FILES     = defined('PHTML_PERMITIR_FILES')      ? PHTML_PERMITIR_FILES      : false;
        $this->_bolPermitir_COOKIE    = defined('PHTML_PERMITIR_COOKIE')     ? PHTML_PERMITIR_COOKIE     : true;
        $this->_bolPermitir_SESSION   = defined('PHTML_PERMITIR_SESSION')    ? PHTML_PERMITIR_SESSION    : true;
        $this->_bolPermitir_REQUEST   = defined('PHTML_PERMITIR_REQUEST')    ? PHTML_PERMITIR_REQUEST    : true;
        $this->_bolPermitir_ENV       = defined('PHTML_PERMITIR_ENV')        ? PHTML_PERMITIR_ENV        : false;
        $this->_bolComprimir          = defined('PHTML_COMPRIMIR')           ? PHTML_COMPRIMIR           : false;
        $this->_bolEjecutarPhp        = defined('PHTML_EJECUTAR_PHP')        ? PHTML_EJECUTAR_PHP        : true;
        $this->_bolEjecutarMetodos    = defined('PHTML_EJECUTAR_METODO')     ? PHTML_EJECUTAR_METODO     : true;
        $this->_bolEliminarComentario = defined('PHTML_ELIMINAR_COMENTARIO') ? PHTML_ELIMINAR_COMENTARIO : true;
        $this->_cadEncoding           = defined('PHTML_ENCODING')            ? PHTML_ENCODING            : 'UTF-8';
        $cadClave                     = defined('PHTML_CADENA_CLAVE')        ? PHTML_CADENA_CLAVE        : 'phtml';
        $this->_idAleatorio = $this->_crearIdAleatorio($cadClave);
    }


    /**
     * Agrega contenido phtml al para futura compiladion
     * 
     * @param string $cadContenido
     * @param mixed $indice
     */
    public function agregarContenido($cadContenido = '', $indice = null)
    {
        if (isset($indice)) {
            if (isset($this->_arrContenido[$indice])) {
                $this->_arrContenido[$indice] .= $cadContenido;
            } else {
                $this->_arrContenido[$indice] = $cadContenido;
            }
        } else {
            $this->_arrContenido[$this->_idAleatorio] .= $cadContenido;
        }
    }



    /**
     * Recoge el contenido del archivo y lo agrega para compilar
     * 
     * @param string $cadRutaArchivo
     * @param mixed $indice
     */
    public function agregarArchivo($cadRutaArchivo, $indice = null)
    {
        if (file_exists($cadRutaArchivo)) {
            if ($this->_bolEjecutarPhp) {
                ob_start();
                include($cadRutaArchivo);
                $cadContenido = ob_get_contents();
                ob_end_clean();
            } else {
                $cadContenido = file_get_contents($cadRutaArchivo);
            }
            $this->agregarContenido($cadContenido, $indice);
        }
    }



    /**
     * Recoge el contenido que se esta ejecutando de una determinada parte del archivo
     * (*) Esta funcion ejecuta el codigo php aun que $_bolEjecutarPhp = false
     * (*) No permite multiples capturas
     * 
     * @param mixed $indice
     */
    public function captarContenido($indice = null)
    {
        if ($this->_bolCapturado) {
            $cadContenido = ob_get_contents();
            ob_end_clean();
            $this->_bolCapturado = false;
            $this->agregarContenido($cadContenido, $indice);
        } else {
            ob_start();
            $this->_bolCapturado = true;
        }
    }


    /**
     * Agrega una variable para su disponibilidad en el contenido
     * 
     * @param mixed $indice
     * @param mixed $valor
     */
    public function agregarVariable($indice, $valor = '')
    {
        if (isset($indice)) {
            $this->_arrVariables[$indice] = $valor;
        }
    }


    /**
     * Devuelve el valor de una variable
     * 
     * @param mixed $indice
     * @return mixed 
     */
    public function obtenerVariable($indice)
    {
        return (isset($indice) && isset($this->_arrVariables[$indice]) ? $this->_arrVariables[$indice] : null);
    }


    /**
     * Devuelve el objeto DOMDocument con el contenido html cargado
     * 
     * @return object
     */
    private function _obtenerObjDOM($cadContenido = '')
    {
        $objDom = new DOMDocument;
        libxml_use_internal_errors(true);
        $cadContenido = '<phtml id="' . $this->_idAleatorio . '">' . $cadContenido . '</phtml>';
        $cadContenidoEncodado = mb_convert_encoding($cadContenido, 'HTML-ENTITIES', $this->_cadEncoding);
        @$objDom->loadHTML($cadContenidoEncodado);
        libxml_clear_errors();
        return ($objDom);
    }



    /**
     * Convierte la cadena html en elementos DOMDocument
     * 
     * @param object $objDom
     * @param string $cadHtml
     * @return object
     */
    private function _convertirHTMLenElementos(DOMDocument $objDom, $cadHTML)
    {
        $esteObjDom = $this->_obtenerObjDOM($cadHTML);
        $objPhtml = $esteObjDom->getElementById($this->_idAleatorio);
        $objFrag = $objDom->createDocumentFragment();
        $esteElemento = $objPhtml->firstChild;
        while ($esteElemento) {
            $objFrag->appendChild($objDom->importNode($esteElemento, true));
            $esteElemento = $esteElemento->nextSibling;
        }
        return ($objFrag);
    }



    /**
     * Recoge todo los elementos del nodo
     * 
     * @param object DOMDocument $objDom
     * @param object DOMNode $objNodo
     * @return object DOMDocumentFragment 
     */
    private function _obtenerElementos(DOMDocument $objDom, DOMNode $objNodo)
    {
        $objFrag = $objDom->createDocumentFragment();
        while ($esteElemento = $objNodo->firstChild) {
            $objFrag->appendChild($objDom->importNode($esteElemento, true));
            $esteElemento = $esteElemento->nextSibling;
        }
        return ($objFrag);
    }


    /**
     * Crea una cadena con el contenido de un nodo
     *  
     * @param object DOMNode $objNodo el nodo del que se quere crear el contenido
     * @return string la cadena con el contenido del nodo especificado
     */
    private function _obtenerHTML(DOMNode $objNodo)
    {
        $cadHTML = '';
        $hijos  = $objNodo->childNodes;
        foreach ($hijos as $hijo) {
            $cadHTML .= $objNodo->ownerDocument->saveHTML($hijo);
        }
        return ($cadHTML);
    }


    /**
     * Elimina los comentarios antes del nodo especificado
     * 
     * @param object $objNodo el nodo del que se quere eliminar los comentarios
     */
    private function _eliminarComentarios(DOMNode $objNodo)
    {
        while (@$objNodo->previousSibling->nodeType == XML_COMMENT_NODE || (@$objNodo->previousSibling->nodeType == XML_TEXT_NODE && ctype_space(@$objNodo->previousSibling->textContent))) {
            $objNodo->parentNode->removeChild($objNodo->previousSibling);
        }
    }


    /**
     * Transforma las cadenas pasadas en la plantilla en variables
     * 
     * @param string $cadVariable captura del supuesto nombre de variable
     * 
     * @return mixed devuelve el valor que contiene la variable
     */
    private function _importarVariable($cadVariable)
    {
        if (is_null($cadVariable)) {
            return (null);
        }
        $varTemporal = null;
        if (preg_match('/[^0-9][a-zA-Z0-9_\.]+/', trim($cadVariable))) {
            $arrVar = explode('.', $cadVariable);
            $numParametros    = sizeof($arrVar);
            $varTemporal      = null;
            switch ($numParametros) {
                case 1:
                    if (isset($this->_arrVariables[$arrVar[0]])) {
                        $varTemporal = $this->_arrVariables[$arrVar[0]];
                    } else { // admitir valores anonimos
                        $varTemporal = null;
                    }
                    break;
                case 2:
                    switch ($arrVar[0]) { // obtener super globales
                        case 'GLOBALS':
                            if ($this->_bolPermitir_GLOBALS && isset($_GLOBALS[$arrVar[1]])) {
                                $varTemporal = $GLOBALS[$arrVar[1]];
                            }
                            break;
                        case '_SERVER':
                            if ($this->_bolPermitir_SERVER && isset($_SERVER[$arrVar[1]])) {
                                $varTemporal = $_SERVER[$arrVar[1]];
                            }
                            break;
                        case '_GET':
                            if ($this->_bolPermitir_GET && isset($_GET[$arrVar[1]])) {
                                $varTemporal = $_GET[$arrVar[1]];
                            }
                            break;
                        case '_POST':
                            if ($this->_bolPermitir_POST && isset($_POST[$arrVar[1]])) {
                                $varTemporal = $_POST[$arrVar[1]];
                            }
                            break;
                        case '_FILES':
                            if ($this->_bolPermitir_FILES && isset($_FILES[$arrVar[1]])) {
                                $varTemporal = $_FILES[$arrVar[1]];
                            }
                            break;
                        case '_COOKIE':
                            if ($this->_bolPermitir_COOKIE && isset($_COOKIE[$arrVar[1]])) {
                                $varTemporal = $_COOKIE[$arrVar[1]];
                            }
                            break;
                        case '_SESSION':
                            if ($this->_bolPermitir_SESSION && isset($_SESSION[$arrVar[1]])) {
                                $varTemporal = $_SESSION[$arrVar[1]];
                            }
                            break;
                        case '_REQUEST':
                            if ($this->_bolPermitir_REQUEST && isset($_REQUEST[$arrVar[1]])) {
                                $varTemporal = $_REQUEST[$arrVar[1]];
                            }
                            break;
                        case '_ENV':
                            if ($this->_bolPermitir_ENV && isset($_ENV[$arrVar[1]])) {
                                $varTemporal = $_ENV[$arrVar[1]];
                            }
                            break;
                        default:
                            if (isset($this->_arrVariables[$arrVar[0]]) && is_array($this->_arrVariables[$arrVar[0]])) {
                                if (isset($this->_arrVariables[$arrVar[0]][$arrVar[1]])) {
                                    $varTemporal = $this->_arrVariables[$arrVar[0]][$arrVar[1]]; // arreglo[]
                                }
                            } else if (isset($this->_arrVariables[$arrVar[0]]) && is_object($this->_arrVariables[$arrVar[0]])) {
                                if (property_exists($this->_arrVariables[$arrVar[0]], $arrVar[1])) {
                                    $varTemporal = $this->_arrVariables[$arrVar[0]]->{$arrVar[1]}; // objeto->propiedad
                                } else if ($this->_bolEjecutarMetodos && method_exists($this->_arrVariables[$arrVar[0]], $arrVar[1])) {
                                    $varTemporal = $this->_arrVariables[$arrVar[0]]->{$arrVar[1]}(); // objeto->metodo()
                                }
                            }
                            break;
                    }
                    break;
                case 3:
                    /** (*) agregar soporte variables globales multinivel */
                    if (isset($this->_arrVariables[$arrVar[0]][$arrVar[1]]) && is_array($this->_arrVariables[$arrVar[0]][$arrVar[1]])) {
                        if (isset($this->_arrVariables[$arrVar[0]][$arrVar[1]][$arrVar[2]])) {
                            $varTemporal = $this->_arrVariables[$arrVar[0]][$arrVar[1]][$arrVar[2]]; // arreglo[][]
                        }
                    } else if (isset($this->_arrVariables[$arrVar[0]]) && is_object($this->_arrVariables[$arrVar[0]])) {
                        if ($this->_bolEjecutarMetodos && method_exists($this->_arrVariables[$arrVar[0]], $arrVar[1])) {
                            $varTemporal = $this->_arrVariables[$arrVar[0]]->{$arrVar[1]}($arrVar[2]); // objeto metodo(param)
                        }
                    } else if (isset($this->_arrVariables[$arrVar[0]][$arrVar[1]]) && is_object($this->_arrVariables[$arrVar[0]][$arrVar[1]])) {
                        if (property_exists($this->_arrVariables[$arrVar[0]][$arrVar[1]], $arrVar[2])) {
                            $varTemporal = $this->_arrVariables[$arrVar[0]][$arrVar[1]]->{$arrVar[2]}; // objeto->propiedad
                        } else if ($this->_bolEjecutarMetodos && method_exists($this->_arrVariables[$arrVar[0]][$arrVar[1]], $arrVar[2])) {
                            $varTemporal = $this->_arrVariables[$arrVar[0]][$arrVar[1]]->{$arrVar[2]}(); // objeto->metodo()
                        }
                    }
                    break;
                    /* case 4:
                    // depurar variables mulinivel 2
                    break; */
            }
        } 
        return ($varTemporal);
    }



    /**
     * Crea un identificador aleatorio
     * 
     * @param string $cadClave la cadena que se quiere encriptar
     * @return string devuelve una cadena encriptada aleatoria
     */
    private function _crearIdAleatorio($cadClave)
    {
        $contexto = hash_init('sha256', HASH_HMAC, '/^P|-|T|V||_$dPm/' . rand(0, 1000));
        hash_update($contexto, $cadClave);
        return (hash_final($contexto));
    }


    /**
     * Escapa los metacaracteres usados por expresiones regulares
     * 
     * @param string $cadEntrada la cadena que contiene los caracteres
     * 
     * @return string la cadena con los caracteres escapados
     */
    private function _escaparMetaCaracteres($cadEntrada)
    {
        $arrCaracteresMeta = array('\\', '.',  '+', '*', '?', '[', '^', ']', '$', '(', ')', '{', '}', '=', '!', '<', '>', '|', ':', '-');
        $arrCaracteresEsc = array('\\\\', '\.', '\+', '\*', '\?', '\[', '\^', '\]', '\$', '\(', '\)', '\{', '\}', '\=', '\!', '\<', '\>', '\|', '\:', '\-');
        $cadEntrada = str_replace($arrCaracteresMeta, $arrCaracteresEsc, $cadEntrada);
        return ($cadEntrada);
    }



    /**
     * Comprueba que la variable cumpla una condicion
     * (*) agregar un objeto de condiciones
     * 
     * @param string $cadCondicion
     * @param mixed $mixedVar
     * @return boolean
     */
    private function _comprobarCondicion($mixedVar, $cadCondicion = '')
    {
        if ($cadCondicion != '') {
            switch ($cadCondicion) {
                case 'TRUE':
                case 'true':
                    return ($mixedVar == true);
                case 'FALSE':
                case 'false':
                    return ($mixedVar == false);
                case 'null':
                case 'NULL':
                case 'is_null':
                    return (is_null($mixedVar));
                case 'is_array':
                    return (is_array($mixedVar));
                case '!is_array':
                    return (!is_array($mixedVar));
                case 'is_object':
                    return (is_object($mixedVar));
                case '!is_object':
                    return (!is_object($mixedVar));
                case 'is_numeric':
                    return (is_numeric($mixedVar));
                case 'not_numeric':
                case '!is_numeric':
                    return (!is_numeric($mixedVar));
                case 'is_int':
                    return (is_int((int)$mixedVar));
                case 'not_int':
                case '!is_int':
                    return (!is_int((int)$mixedVar));
                case 'isset':
                    return (isset($mixedVar));
                case 'not_isset':
                case '!isset':
                    return (!isset($mixedVar));
                case 'empty':
                    return (empty($mixedVar));
                case 'not_empty':
                case '!empty':
                    return (!empty($mixedVar));
                case 'is_email':
                    return (!filter_var($mixedVar, FILTER_VALIDATE_EMAIL) ? false : true);
                default:
                    $arrCondicion = explode('.', $cadCondicion, 2);
                    if (sizeof($arrCondicion) == 2) {
                        $cadMetodo = $arrCondicion[0];
                        $cadOperador = $arrCondicion[1];
                        switch ($cadMetodo) {
                            case '==':
                            case 'equalto':
                                return ($mixedVar == $cadOperador);
                            case '!=':
                            case 'not_equalto':
                                return ($mixedVar != $cadOperador);
                            case '>':
                            case 'morethan':
                                return ($mixedVar > $cadOperador);
                            case '<':
                            case 'lessthan':
                                return ($mixedVar < $cadOperador);
                            case '>=':
                            case 'morethan_equalto':
                                return ($mixedVar >= $cadOperador);
                            case '<=':
                            case 'lessthan_equalto':
                                return ($mixedVar <= $cadOperador);
                            case 'permitted_regexp':
                                return (preg_match($cadOperador, $mixedVar));
                            case 'not_permitted_regexp':
                                return (!preg_match($cadOperador, $mixedVar));
                            case 'maxlength':
                                return (strlen($mixedVar) <= (int)$cadOperador);
                            case 'minlength':
                                return (strlen($mixedVar) >= (int)$cadOperador);
                            case 'length':
                                return (strlen($mixedVar) == (int)$cadOperador);
                            case 'permitted_characters':
                                $totalCaracteres = strlen($mixedVar);
                                for ($i = 0; $i < $totalCaracteres; $i++) {
                                    $esteCaracter = substr($mixedVar, $i, 1);
                                    if (strpos($cadOperador, $esteCaracter) === false) {
                                        return (false);
                                    }
                                }
                                return (true);
                            case 'not_permitted_characters':
                                $totalCaracteres = strlen($mixedVar);
                                for ($i = 0; $i < $totalCaracteres; $i++) {
                                    $esteCaracter = substr($mixedVar, $i, 1);
                                    if (strpos($cadOperador, $esteCaracter) !== false) {
                                        return (false);
                                    }
                                }
                                return (true);
                            default:
                                return ($mixedVar == $cadCondicion);
                        }
                    } else {
                        return ($mixedVar == $cadCondicion);
                    }
            }
        } else {
            return (false);
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
     * @param string $cadBuscar
     * @param string $cadReemplazar
     * @param string $cadContenido
     * 
     * @return string
     */
    private function _reemplazarComillasEtc($cadBuscar, $cadReemplazar, $cadContenido = '')
    {
        $patron = '/[v|V][a|A][r|R]\s*=\s*[\'|"]\s*' . str_replace('.', '\.', $cadBuscar) . '\.(.*?)\s*[\'|"]/';
        while (@preg_match($patron, $cadContenido, $arrResultado)) {
            $patronReemplazo = 'var="' .  $cadReemplazar . '.' . $arrResultado[1] . '"';
            $cadContenido = @preg_replace($patron, $patronReemplazo, $cadContenido);
        }
        return ($cadContenido);
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
     * @param string $cadBuscar
     * @param string $cadReemplazar
     * @param string $cadContenido
     * 
     * @return string
     */
    private function _reemplazarComillas($cadBuscar, $cadReemplazar, $cadContenido = '')
    {
        $patron = '/[v|V][a|A][r|R]\s*=\s*[\'|"]\s*' . str_replace('.', '\.', $cadBuscar) . '\s*[\'|"]/';
        while (@preg_match($patron, $cadContenido)) {
            $patronReemplazo = 'var="' .  $cadReemplazar . '"';
            $cadContenido = @preg_replace($patron, $patronReemplazo, $cadContenido);
        }
        return ($cadContenido);
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
     * @param string $cadBuscar
     * @param string $cadReemplazar
     * @param string $cadContenido
     * 
     * @return string
     */
    private function _reemplazarVariable($cadBuscar, $cadReemplazar, $cadContenido = '')
    {
        $patron = '/' . $this->_escaparMetaCaracteres($this->_abreVariable) . '\s*' . str_replace('.', '\.', $cadBuscar) . '\s*' . $this->_escaparMetaCaracteres($this->_cierraVariable)  . '/';
        while (@preg_match($patron, $cadContenido)) {
            $cadContenido = @preg_replace($patron, $cadReemplazar, $cadContenido);
        }
        return ($cadContenido);
    }



    /**
     * Reemplaza los las variables de los bucles for - foreach - while
     * 
     * {{var.key.XXX.etc}}
     * {{id.var.key.XXX.etc}}
     * {{id.var.i.XXX.etc}} 
     * {{var.i.XXX.etc}}
     * @param string $cadBuscar     antiguo valor
     * @param string $cadReemplazar nuevo valor
     * @param string $cadContenido el contenido que hay que modificar
     * 
     * @return string devuele el contenido con los valores reemplazados
     */
    private function _reemplazarVariableEtc($cadBuscar, $cadReemplazar, $cadContenido = '')
    {
        $patron = '/' . $this->_escaparMetaCaracteres($this->_abreVariable) . '\s*' . str_replace('.', '\.', $cadBuscar) . '\.(.*?)\s*' . $this->_escaparMetaCaracteres($this->_cierraVariable)  . '/';
        while (@preg_match($patron, $cadContenido, $arrResultado)) {
            $reemplazo = $this->_abreVariable . $cadReemplazar . '.' . $arrResultado[1] . $this->_cierraVariable;
            $cadContenido = @preg_replace($patron, $reemplazo, $cadContenido);
        }
        return ($cadContenido);
    }


    /**
     * Comprueba si un metodo de formatear es soportado
     * 
     * @param string $cadNombreMetodo
     */
    private function _existeMetodo($cadNombreMetodo)
    {
        return (in_array($cadNombreMetodo, $this->_arrMetodos));
    }


    /**
     * Formatear el contenido de salida de la variable
     * 
     * @param string  $cadNombreMetodo el nombre del metodo formateador
     * @param mixed   $mixedVar la variable que contiene el contenido a formatear
     * @return string devuelve el contenido formateado segun el metodo
     */
    private function _obtenerFormato($cadNombreMetodo, $mixedVar)
    {
        switch ($cadNombreMetodo) {
            case 'strtolower':
            case 'lowercase':
            case 'lower':
                $cadFormateada = strtolower($mixedVar);
                break;
            case 'strtoupper':
            case 'uppercase':
            case 'upper':
                $cadFormateada = strtoupper($mixedVar);
                break;
            case 'ucwords':
            case 'camelcase':
                $cadFormateada = ucwords($mixedVar);
                break;
            case 'ucfirst';
                $cadFormateada = ucfirst($mixedVar);
                break;
            case 'urlencode':
                $cadFormateada = urlencode($mixedVar);
                break;
            case 'urldecode':
                $cadFormateada = urldecode($mixedVar);
                break;
            case 'trim':
                $cadFormateada = trim($mixedVar);
                break;
            case 'ltrim':
                $cadFormateada = ltrim($mixedVar);
                break;
            case 'rtrim':
                $cadFormateada = rtrim($mixedVar);
                break;
            case 'htmlentities':
                $cadFormateada = htmlentities($mixedVar);
                break;
            case 'html_entity_decode':
                $cadFormateada = html_entity_decode($mixedVar);
                break;
            case 'addslashes':
                $cadFormateada = addslashes($mixedVar);
                break;
            case 'stripslashes':
                $cadFormateada = stripslashes($mixedVar);
                break;
            case 'htmlespecialschars':
                $cadFormateada = htmlspecialchars($mixedVar);
                break;
            case 'strlen':
                $cadFormateada = strlen($mixedVar);
                break;
            default:
                $cadFormateada = $mixedVar;
                break;
        }
        return ($cadFormateada);
    }



    /**
     * Imprime todas las variables
     * 
     * @param boolean $eliminarVariables elimina las coencidencias si esta en true
     */
    private function _compilar_var($eliminarVariables = false)
    {
        $patron = '/' . $this->_escaparMetaCaracteres($this->_abreVariable) . '\s*([^0-9][a-zA-Z0-9_\.]+)\s*' . $this->_escaparMetaCaracteres($this->_cierraVariable) .  '/';
        if (preg_match_all($patron, $this->_cadContenido, $arrResultado)) {
            print_pre($arrResultado);
            $totalCoincidencias = sizeof($arrResultado[0]);
            for ($i = 0; $i < $totalCoincidencias; $i++) {
                $arrVar = explode('.', $arrResultado[1][$i], 2);
                if (sizeof($arrVar) == 2 && $this->_existeMetodo($arrVar[0])) {
                    $mixedVar = $this->_importarVariable($arrVar[1]);
                    if (!empty($mixedVar)) {
                        $cadContenido = $this->_obtenerFormato($arrVar[0], $mixedVar);
                    } else {
                        if($eliminarVariables) {
                            $cadContenido = '';
                        }
                    }
                } else {
                    $mixedVar = $this->_importarVariable($arrResultado[1][$i]);
                    if (!empty($mixedVar)) {
                        $cadContenido = $mixedVar;
                    } else {
                        if($eliminarVariables) {
                            $cadContenido = '';
                        }
                    }
                }
                if(isset($cadContenido)) {
                    $this->_cadContenido = str_replace($arrResultado[0][$i], $cadContenido, $this->_cadContenido);
                }
            }
        }
    }



    /**
     * Imprime todas las constantes
     */
    private function _compilar_const()
    {
        $patron = '/' . $this->_escaparMetaCaracteres($this->_abreConstante) . '\s*([^0-9][A-Z0-9_]+)\s*' . $this->_escaparMetaCaracteres($this->_cierraConstante) .  '/';
        while (preg_match($patron, $this->_cadContenido, $arrResultado)) {
            if (defined($arrResultado[1])) {
                $cadContenido = constant($arrResultado[1]);
            } else {
                $cadContenido = '';
            }
            $this->_cadContenido = str_replace($arrResultado[0], $cadContenido, $this->_cadContenido);
        }
    }


    /**
     * Compila de TAG include
     * (*) Dentro del bloque include
     *      - comentarios SI
     *      - nodos NO
     *      - cadena ruta del archivo a incluir
     * 
     * <!-- La eliminacion de este comentario depende de $_bolEliminarComentario -->
     * <include>
     * <!-- Este comentario se eliminara -->
     * ruta/del/archivo.phtml
     * <!-- Este comentario se eliminara -->
     * </include>
     */
    private function _compilar_include()
    {
        $objDom = $this->_obtenerObjDOM($this->_cadContenido);
        $objInclude = $objDom->getElementsByTagName('include')->item(0);
        if ($this->_bolEliminarComentario) {
            $this->_eliminarComentarios($objInclude);
        }
        $cadRutaArchivo = trim(preg_replace('/(\\n|\\t|\\r)/s', '', $objInclude->textContent));
        if (file_exists($cadRutaArchivo)) {
            if ($this->_bolEjecutarPhp) {
                ob_start();
                include($cadRutaArchivo);
                $cadContenido = ob_get_contents();
                ob_end_clean();
            } else {
                $cadContenido = file_get_contents($cadRutaArchivo);
            }
            $objFrag = $this->_convertirHTMLenElementos($objDom, $cadContenido);
            $objInclude->parentNode->replaceChild($objFrag, $objInclude);
        } else {
            $objInclude->parentNode->removeChild($objInclude);
        }
        $objDom->saveHTML();
        $objPhtml = $objDom->getElementById($this->_idAleatorio);
        $this->_cadContenido = $this->_obtenerHTML($objPhtml);
    }



    /**
     * Compila los TAG if - elseif - else
     * <!-- La eliminacion de este comentario depende de $_bolEliminarComentario -->
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
    private function _compilar_if()
    {
        $objDom     = $this->_obtenerObjDOM($this->_cadContenido);
        $objIf      = $objDom->getElementsByTagName('if')->item(0);
        $varIf      = $objIf->hasAttribute('var') && $objIf->getattribute('var') != '' ? $this->_importarVariable($objIf->getAttribute('var')) : null;
        $bolCondIf  = $objIf->hasAttribute('cond') ? $this->_comprobarCondicion($varIf, $objIf->getAttribute('cond')) : isset($varIf);
        $cadCompare = $objIf->hasAttribute('compare') ? strtolower(trim($objIf->getAttribute('compare'))) : 'and';
        $objFrag    = null;
        if ($objIf->hasAttribute('and') && !$objIf->hasAttribute('or')) {
            $bolPaseIf = $bolCondIf && $this->_comprobarCondicion($varIf, $objIf->getAttribute('and'));
        } else if ($objIf->hasAttribute('or') && !$objIf->hasAttribute('and')) {
            $bolPaseIf = $bolCondIf || $this->_comprobarCondicion($varIf, $objIf->getAttribute('or'));
        } else if ($objIf->hasAttribute('and') && $objIf->hasAttribute('or') && $cadCompare == 'and') {
            $bolPaseIf = $bolCondIf && $this->_comprobarCondicion($varIf, $objIf->getAttribute('and')) || $this->_comprobarCondicion($varIf, $objIf->getAttribute('or'));
        } else if ($objIf->hasAttribute('and') && $objIf->hasAttribute('or') && $cadCompare == 'or') {
            $bolPaseIf = $bolCondIf || $this->_comprobarCondicion($varIf, $objIf->getAttribute('or')) && $this->_comprobarCondicion($varIf, $objIf->getAttribute('and'));
        } else {
            $bolPaseIf = $bolCondIf;
        }
        if ($bolPaseIf) {
            $objFrag = $this->_obtenerElementos($objDom, $objIf);
            while (strtolower(@$objIf->nextSibling->nodeName) == 'elseif' || strtolower(@$objIf->nextSibling->nodeName) == 'else' || @$objIf->nextSibling->nodeType == XML_COMMENT_NODE || (@$objIf->nextSibling->nodeType == XML_TEXT_NODE && ctype_space(@$objIf->nextSibling->textContent))) {
                $objIf->parentNode->removeChild($objIf->nextSibling);
            }
        } else {
            while (strtolower(@$objIf->nextSibling->nodeName) == 'elseif' || @$objIf->nextSibling->nodeType == XML_COMMENT_NODE || (@$objIf->nextSibling->nodeType == XML_TEXT_NODE && ctype_space(@$objIf->nextSibling->textContent))) {
                if (strtolower(@$objIf->nextSibling->nodeName) == 'elseif') {
                    $objElseif = $objIf->nextSibling;
                    if (!$objFrag) {
                        $varElseIf      = $objElseif->hasAttribute('var') && $objElseif->getattribute('var') != '' ? $this->_importarVariable($objElseif->getAttribute('var')) : null;
                        $bolCondElseIf  = $objElseif->hasAttribute('cond') ? $this->_comprobarCondicion($varElseIf, $objElseif->getAttribute('cond')) : isset($varElseIf);
                        $cadCompareElseIf = $objElseif->hasAttribute('compare') ? strtolower(trim($objElseif->getAttribute('compare'))) : 'and';
                        if ($objElseif->hasAttribute('and') && !$objElseif->hasAttribute('or')) {
                            $bolPaseElseIf = $bolCondElseIf && $this->_comprobarCondicion($varElseIf, $objElseif->getAttribute('and'));
                        } else if ($objElseif->hasAttribute('or') && !$objElseif->hasAttribute('and')) {
                            $bolPaseElseIf = $bolCondElseIf || $this->_comprobarCondicion($varElseIf, $objElseif->getAttribute('or'));
                        } else if ($objElseif->hasAttribute('and') && $objElseif->hasAttribute('or') && $cadCompareElseIf == 'and') {
                            $bolPaseElseIf = $bolCondElseIf && $this->_comprobarCondicion($varElseIf, $objElseif->getAttribute('and')) || $this->_comprobarCondicion($varElseIf, $objElseif->getAttribute('or'));
                        } else if ($objElseif->hasAttribute('and') && $objElseif->hasAttribute('or') && $cadCompareElseIf == 'or') {
                            $bolPaseElseIf = $bolCondElseIf || $this->_comprobarCondicion($varElseIf, $objElseif->getAttribute('or')) && $this->_comprobarCondicion($varElseIf, $objElseif->getAttribute('and'));
                        } else {
                            $bolPaseElseIf = $bolCondElseIf;
                        }
                        if ($bolPaseElseIf) {
                            $objFrag = $this->_obtenerElementos($objDom, $objElseif);
                        }
                    }
                }
                $objIf->parentNode->removeChild($objIf->nextSibling);
            }
            if (strtolower(@$objIf->nextSibling->nodeName) == 'else') {
                if (!$objFrag) {
                    $objFrag = $this->_obtenerElementos($objDom, $objIf->nextSibling);
                }
                $objIf->parentNode->removeChild($objIf->nextSibling);
            }
        }
        if ($this->_bolEliminarComentario) {
            $this->_eliminarComentarios($objIf);
        }
        if (!$objFrag) {
            $objIf->parentNode->removeChild($objIf);
        } else {
            $objIf->parentNode->replaceChild($objFrag, $objIf);
        }
        $objDom->saveHTML();
        $objPhtml = $objDom->getElementById($this->_idAleatorio);
        $this->_cadContenido = $this->_obtenerHTML($objPhtml);
    }



    /**
     * Compila los TAG switch - case - default
     * <!-- La eliminacion de este comentario depende de $_bolEliminarComentario -->
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
    private function _compilar_switch()
    {
        $objDom = $this->_obtenerObjDOM($this->_cadContenido);
        $objSwitch = $objDom->getElementsByTagName('switch')->item(0);
        $varSwitch      = $objSwitch->hasAttribute('var') && $objSwitch->getattribute('var') != '' ? $this->_importarVariable($objSwitch->getAttribute('var')) : null;
        $objFrag = null;
        while (strtolower(@$objSwitch->firstChild->nodeName) == 'case' || @$objSwitch->firstChild->nodeType == XML_COMMENT_NODE || (@$objSwitch->firstChild->nodeType == XML_TEXT_NODE && ctype_space(@$objSwitch->firstChild->textContent))) {
            if (strtolower(@$objSwitch->firstChild->nodeName) == 'case') {
                if (!$objFrag) {
                    $objCase = $objSwitch->firstChild;
                    $bolCond  = $objCase->hasAttribute('cond') ? $this->_comprobarCondicion($varSwitch, $objCase->getAttribute('cond')) : isset($varSwitch);
                    $cadCompare = $objCase->hasAttribute('compare') ? $objCase->getAttribute('compare') : 'and';
                    if ($objCase->hasAttribute('and') && !$objCase->hasAttribute('or')) {
                        $bolPase = $bolCond && $this->_comprobarCondicion($varSwitch, $objCase->getAttribute('and'));
                    } else if ($objCase->hasAttribute('or') && !$objCase->hasAttribute('and')) {
                        $bolPase = $bolCond || $this->_comprobarCondicion($varSwitch, $objCase->getAttribute('or'));
                    } else if ($objCase->hasAttribute('and') && $objCase->hasAttribute('or') && $cadCompare == 'and') {
                        $bolPase = $bolCond && $this->_comprobarCondicion($varSwitch, $objCase->getAttribute('and')) || $this->_comprobarCondicion($varSwitch, $objCase->getAttribute('or'));
                    } else if ($objCase->hasAttribute('and') && $objCase->hasAttribute('or') && $cadCompare == 'or') {
                        $bolPase = $bolCond || $this->_comprobarCondicion($varSwitch, $objCase->getAttribute('or')) && $this->_comprobarCondicion($varSwitch, $objCase->getAttribute('and'));
                    } else {
                        $bolPase = $bolCond;
                    }
                    if ($bolPase) {
                        $objFrag = $this->_obtenerElementos($objDom, $objCase);
                    }
                }
            }
            $objSwitch->removeChild($objSwitch->firstChild);
        }
        if (strtolower(@$objSwitch->firstChild->nodeName) == 'default') {
            if (!$objFrag) {
                $objFrag = $this->_obtenerElementos($objDom, $objSwitch->firstChild);
            }
            $objSwitch->removeChild($objSwitch->firstChild);
        }
        if ($this->_bolEliminarComentario) {
            $this->_eliminarComentarios($objSwitch);
        }
        if (!$objFrag) {
            $objSwitch->parentNode->removeChild($objSwitch);
        } else {
            $objSwitch->parentNode->replaceChild($objFrag, $objSwitch);
        }
        $objDom->saveHTML();
        $objPhtml = $objDom->getElementById($this->_idAleatorio);
        $this->_cadContenido = $this->_obtenerHTML($objPhtml);
    }



    /**
     * Compila el TAG foreach
     * (!) Testar arreglos multidimensionales
     * 
     * <!-- La eliminacion de este comentario depende de $_bolEliminarComentario -->
     * <foreach var="variable" key="key" value="value" id="id">
     * </forach>
     */
    private function _compilar_foreach()
    {
        $objDom         = $this->_obtenerObjDOM($this->_cadContenido);
        $objForeach     = $objDom->getElementsByTagName('foreach')->item(0);
        $cadVariable    = $objForeach->hasAttribute('var') && $objForeach->getAttribute('var') != '' ? $objForeach->getAttribute('var') : null;
        $cadClave       = $objForeach->hasAttribute('key') ? $objForeach->getAttribute('key') : 'key';
        $cadValor       = $objForeach->hasAttribute('value') ? $objForeach->getAttribute('value') : 'value';
        $id             = $objForeach->hasAttribute('id') ? $objForeach->getAttribute('id') . '.' : '';
        $cadContenido   = $this->_obtenerHTML($objForeach);
        $mixedVar       = $this->_importarVariable($cadVariable);
        $objFrag        = null;
        $cadProcesada   = '';
        if (is_array($mixedVar) || is_object($mixedVar)) {
            foreach ($mixedVar as $clave => $valor) {
                $cadProcesada .= $cadContenido;
                $cadProcesada = $this->_reemplazarVariable($id . $cadVariable . '.' . $cadClave,  is_object($mixedVar) ? $mixedVar->$clave : $mixedVar[$clave], $cadProcesada);
                $cadProcesada = $this->_reemplazarVariable($id . $cadClave,  $clave, $cadProcesada);
                $cadProcesada = $this->_reemplazarVariable($id . $cadValor,  $valor, $cadProcesada);
                $cadProcesada = $this->_reemplazarVariableEtc($id . $cadVariable . '.' . $cadClave,  $id . $cadVariable . '.' . $clave, $cadProcesada);
                $cadProcesada = $this->_reemplazarComillas($id . $cadClave, $clave, $cadProcesada);
                $cadProcesada = $this->_reemplazarComillas($id . $cadValor, $valor, $cadProcesada);
                $cadProcesada = $this->_reemplazarComillas($id . $cadVariable . '.' . $cadClave, $id . $cadVariable . '.' . $clave, $cadProcesada);
                $cadProcesada = $this->_reemplazarComillasEtc($id . $cadClave, $id .  $cadVariable . '.' . $clave, $cadProcesada);
                $cadProcesada = $this->_reemplazarComillasEtc($id . $cadVariable . '.' . $cadClave, $id . $cadVariable . '.' . $clave, $cadProcesada);
            }
            $objFrag = $this->_convertirHTMLenElementos($objDom, $cadProcesada);
        }
        if ($this->_bolEliminarComentario) {
            $this->_eliminarComentarios($objForeach);
        }
        if (!$objFrag) {
            $objForeach->parentNode->removeChild($objForeach);
        } else {
            $objForeach->parentNode->replaceChild($objFrag, $objForeach);
        }
        $objDom->saveHTML();
        $objPhtml = $objDom->getElementById($this->_idAleatorio);
        $this->_cadContenido = $this->_obtenerHTML($objPhtml);
    }



    /**
     * Compila el TAG for
     * (!) repasar las fechas pasadas por puntos
     * (!) crear una funcion el inicializar fechas para no repetir codigo
     * 
     * <!-- La eliminacion de este comentario depende de $_bolEliminarComentario --> 
     * <for index="i" var="variable" init="0" size="count" order="asc" id="id" offset="+1">
     * </for>
     */
    private function _compilar_for()
    {
        $objDom            = $this->_obtenerObjDOM($this->_cadContenido);
        $objFor            = $objDom->getElementsByTagName('for')->item(0);
        $cadVariable       = $objFor->hasAttribute('var') && $objFor->getAttribute('var') != '' ? $objFor->getAttribute('var') : null;
        $mixedVar          = $this->_importarVariable($cadVariable);
        $id                = $objFor->hasAttribute('id') ? $objFor->getAttribute('id') . '.' : '';
        $offset            = $objFor->getAttribute('offset');
        $cadIndice         = $objFor->hasAttribute('index') ? $objFor->getAttribute('index')    : 'i';
        $cadTotal          = $objFor->getAttribute('size');
        $init              = $objFor->getAttribute('init');
        $format            = $objFor->hasAttribute('format') ? $objFor->getAttribute('format') : 'd-m-Y';
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
        $cadContenido      = $this->_obtenerHTML($objFor);
        $cadProcesada      = '';
        $objFrag           = null;
        $esCadena          = false;
        $esFecha           = false;
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
            if (preg_match('/^[0-9\.]+?$/', $cadTotal)) { // numeros
                $max = (int)$cadTotal;
            } else {
                $patronFecha = '/^\s*?([0-9]{1,2})[\.\-\/\s]([0-9]{1,2})[\.\-\/\s]([0-9]{4})\s*?([0-9]{1,2}[:][0-9]{1,2})?([:][0-9]{1,2})?\s*?$/';
                if (preg_match($patronFecha, $init, $arrResultado)) {
                    $fecha = $arrResultado[1] . '-' .  $arrResultado[2] . '-' . $arrResultado[3] . (isset($arrResultado[4]) ? ' ' . $arrResultado[4]  . (isset($arrResultado[5]) ?  $arrResultado[5] : '') : '');
                    $init = strtotime($fecha);
                    if (preg_match($patronFecha, $cadTotal, $arrResultado)) { // fechas
                        $fecha = $arrResultado[1] . '-' .  $arrResultado[2] . '-' . $arrResultado[3] . (isset($arrResultado[4]) ? ' ' . $arrResultado[4]  . (isset($arrResultado[5]) ?  $arrResultado[5] : '') : '');
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
                        $max = strlen($max) > 3 ? substr($max, 0, 3) : $max;
                        $init = strlen($init) > 3 ? substr($init, 0, 3) : $init;
                        $max++;
                        $esCadena = true;
                    }
                }
            }
        }
        for ($esCadena || $asc ? $i = $init : $i = $max - 1; $esCadena ? ($i != $max) : ($asc ? $i < $max : $init <= $i); $esFecha ? ($asc ? $i += 86400 : $i -= 86400) : ($esCadena || $asc ? $i++ : $i--)) {
            $cadProcesada .= $cadContenido;
            if ($cadVariable != '') {
                if (@is_array($mixedVar) || @is_scalar($mixedVar[$i])) {
                    $cadProcesada = @$this->_reemplazarVariable($id . $cadVariable . '.' . $cadIndice,  $mixedVar[$i], $cadProcesada);
                }
                $cadProcesada = $this->_reemplazarVariableEtc($id . $cadVariable . '.' . $cadIndice, $id . $cadVariable . '.' . $i, $cadProcesada);
                $cadProcesada = $this->_reemplazarComillas($id . $cadVariable . '.' . $cadIndice, $id . $cadVariable . '.' . $i, $cadProcesada);
                $cadProcesada = $this->_reemplazarComillasEtc($id . $cadVariable . '.' . $cadIndice, $id . $cadVariable . '.' . $i, $cadProcesada);
            }
            if ($esFecha) {
                $cadProcesada = $this->_reemplazarVariable($id . $cadIndice,  date($format, $i), $cadProcesada);
                $cadProcesada = $this->_reemplazarComillas($id . $cadIndice, date($format, $i), $cadProcesada);
            } else {
                if ($offset != '') {
                    eval('$o=' . $i . $offset . ';');
                }
                $cadProcesada = $this->_reemplazarVariable($id . $cadIndice,  isset($o) ? $o : $i, $cadProcesada);
                $cadProcesada = $this->_reemplazarComillas($id . $cadIndice, $i, $cadProcesada);
            }
        }
        $objFrag = $this->_convertirHTMLenElementos($objDom, $cadProcesada);
        if ($this->_bolEliminarComentario) {
            $this->_eliminarComentarios($objFor);
        }
        if (!$objFrag) {
            $objFor->parentNode->removeChild($objFor);
        } else {
            $objFor->parentNode->replaceChild($objFrag, $objFor);
        }
        $objDom->saveHTML();
        $objPhtml = $objDom->getElementById($this->_idAleatorio);
        $this->_cadContenido = $this->_obtenerHTML($objPhtml);
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
    private function _compilar_while()
    {
        return (true);
    }



    /**
     * Se encarga de compilar todo el contenido phtml
     */
    private function _compilar()
    {
        $this->_compilar_const();
        $this->_compilar_var();
        $cadPatron = '/<(if|switch|foreach|for[\s]|while|include)[\s]*.*?>(.*?)<\/(if|switch|foreach|for|while|include)>/is';
        while (preg_match($cadPatron, $this->_cadContenido, $arrResultado)) {
            $nombreTag = strtolower($arrResultado[1]);
            $this->{'_compilar_' . $nombreTag}();
            if ($nombreTag == 'include') {
                $this->_compilar_const();
                $this->_compilar_var();
            }
        }
       // $this->_compilar_var(true);
    }


    /**
     * Devuelve el contenido compilado
     * 
     * @param mixed $indice
     * @return string
     */
    public function obtenerContenido($indice = null)
    {
        if (isset($indice)) {
            $this->_cadContenido = $this->_arrContenido[$indice];
        } else {
            $this->_cadContenido = $this->_arrContenido['PHTML'];
        }
        $this->_compilar();
        if ($this->_bolComprimir) {
            $this->_cadContenido = preg_replace('/(\\n|\\t|\\r|\\s+)/', ' ', $this->_cadContenido);
        }
        return ($this->_cadContenido);
    }
}
