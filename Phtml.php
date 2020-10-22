<?php
include('config.php');

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
            $this->_arrContenido['PHTML'] .= $cadContenido;
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
        $objDom->loadHTML('<phtml id="' . $this->_idAleatorio . '">' . $cadContenido . '</phtml>');
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
     * Crea una cadena a partir de un TAG
     * 
     * @param object DOMNode $objNodo
     * @return string
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
     * Elimina los comentarios del nodo especificado
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
     * @param string $cadCapuraVariable
     * 
     * @return mixed
     */
    private function _importarVariable($cadVariable)
    {
        $arrVar = explode('.', $cadVariable);
        $numParametros = sizeof($arrVar);
        $varTemporal = null;
        switch ($numParametros) {
            case 1:
                if (isset($this->_arrVariables[$arrVar[0]])) {
                    $varTemporal = $this->_arrVariables[$arrVar[0]];
                }
                break;
            case 2:
                switch ($arrVar[0]) {
                    case 'GLOBALS':
                        if ($this->_bolPermitir_GLOBALS == true && isset($_GLOBALS[$arrVar[1]])) {
                            $varTemporal = $GLOBALS[$arrVar[1]];
                        }
                        break;
                    case '_SERVER':
                        if ($this->_bolPermitir_SERVER == true && isset($_SERVER[$arrVar[1]])) {
                            $varTemporal = $_SERVER[$arrVar[1]];
                        }
                        break;
                    case '_GET':
                        if ($this->_bolPermitir_GET == true && isset($_GET[$arrVar[1]])) {
                            $varTemporal = $_GET[$arrVar[1]];
                        }
                        break;
                    case '_POST':
                        if ($this->_bolPermitir_POST == true && isset($_POST[$arrVar[1]])) {
                            $varTemporal = $_POST[$arrVar[1]];
                        }
                        break;
                    case '_FILES':
                        if ($this->_bolPermitir_FILES == true && isset($_FILES[$arrVar[1]])) {
                            $varTemporal = $_FILES[$arrVar[1]];
                        }
                        break;
                    case '_COOKIE':
                        if ($this->_bolPermitir_COOKIE == true && isset($_COOKIE[$arrVar[1]])) {
                            $varTemporal = $_COOKIE[$arrVar[1]];
                        }
                        break;
                    case '_SESSION':
                        if ($this->_bolPermitir_SESSION == true && isset($_SESSION[$arrVar[1]])) {
                            $varTemporal = $_SESSION[$arrVar[1]];
                        }
                        break;
                    case '_REQUEST':
                        if ($this->_bolPermitir_REQUEST == true && isset($_REQUEST[$arrVar[1]])) {
                            $varTemporal = $_REQUEST[$arrVar[1]];
                        }
                        break;
                    case '_ENV':
                        if ($this->_bolPermitir_ENV == true && isset($_ENV[$arrVar[1]])) {
                            $varTemporal = $_ENV[$arrVar[1]];
                        }
                        break;
                    default:
                        if (isset($this->_arrVariables[$arrVar[0]]) && is_array($this->_arrVariables[$arrVar[0]])) {
                            if (isset($this->_arrVariables[$arrVar[0]][$arrVar[1]])) {
                                $varTemporal = $this->_arrVariables[$arrVar[0]][$arrVar[1]];
                            }
                        } else if (isset($this->_arrVariables[$arrVar[0]]) && is_object($this->_arrVariables[$arrVar[0]])) {
                            if (property_exists($this->_arrVariables[$arrVar[0]], $arrVar[1])) {
                                $varTemporal = $this->_arrVariables[$arrVar[0]]->{$arrVar[1]};
                            } else if ($this->_bolEjecutarMetodos == true && method_exists($this->_arrVariables[$arrVar[0]], $arrVar[1])) {
                                $varTemporal = $this->_arrVariables[$arrVar[0]]->{$arrVar[1]}();
                            }
                        }
                        break;
                }
                break;
            case 3:
                if (isset($this->_arrVariables[$arrVar[0]][$arrVar[1]]) && is_array($this->_arrVariables[$arrVar[0]][$arrVar[1]])) {
                    if (isset($this->_arrVariables[$arrVar[0]][$arrVar[1]][$arrVar[2]])) {
                        $varTemporal = $this->_arrVariables[$arrVar[0]][$arrVar[1]][$arrVar[2]];
                    }
                } else if (isset($this->_arrVariables[$arrVar[0]]) && is_object($this->_arrVariables[$arrVar[0]])) {
                    if ($this->_bolEjecutarMetodos == true && method_exists($this->_arrVariables[$arrVar[0]], $arrVar[1])) {
                        $varTemporal = $this->_arrVariables[$arrVar[0]]->{$arrVar[1]}($arrVar[2]);
                    }
                } else if (isset($this->_arrVariables[$arrVar[0]][$arrVar[1]]) && is_object($this->_arrVariables[$arrVar[0]][$arrVar[1]])) {
                    if (property_exists($this->_arrVariables[$arrVar[0]][$arrVar[1]], $arrVar[2])) {
                        $varTemporal = $this->_arrVariables[$arrVar[0]][$arrVar[1]]->{$arrVar[2]};
                    } else if ($this->_bolEjecutarMetodos == true && method_exists($this->_arrVariables[$arrVar[0]][$arrVar[1]], $arrVar[2])) {
                        $varTemporal = $this->_arrVariables[$arrVar[0]][$arrVar[1]]->{$arrVar[2]}();
                    }
                }
                break;
            case 4:
                break;
        }
        return ($varTemporal);
    }



    /**
     * Crea un identificador aleatorio
     * 
     * @param string $cadClave
     * @return string 
     */
    private function _crearIdAleatorio($cadClave)
    {
        $contexto = hash_init('sha256', HASH_HMAC, '/^P|-|T|V||_$/' . rand(0, 1000));
        hash_update($contexto, $cadClave);
        return (hash_final($contexto));
    }



    /**
     * Comprueba que la variable cumpla una condicion
     * 
     * @param string $cadCondicion
     * @param mixed $mixedVar
     * @return boolean
     */
    private function _comprobarCondicion($mixedVar = null, $cadCondicion = '')
    {
        if ($cadCondicion == '') {
            return (false);
        } else {
            return (true);
        }
    }


    /**
     * Imprime todas las variables
     */
    private function _compilar_var()
    {
        return (true);
    }



    /**
     * Imprime todas las constantes
     */
    private function _compilar_const()
    {
        return (true);
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
     * <if var="variable" cond="condicion">
     *      contenido si pasa la condicion
     * </if>
     * <!-- Este comentario se eliminara -->
     * <elseif var="variable" cond="condicion">
     *      contenido si pasa la condicion
     * </elseif>
     * <!-- Este comentario se eliminara -->
     * <else>
     *      contenido si han fallado las condiciones
     * </else>
     */
    private function _compilar_if()
    {
        $objDom = $this->_obtenerObjDOM($this->_cadContenido);
        $objIf = $objDom->getElementsByTagName('if')->item(0);
        $variableIf = $this->_importarVariable($objIf->getAttribute('var'));
        $cadCondIf = $objIf->getAttribute('cond');
        $objFrag = null;
        if ($this->_comprobarCondicion($variableIf, $cadCondIf)) {
            $objFrag = $this->_obtenerElementos($objDom, $objIf);
            while (strtolower(@$objIf->nextSibling->nodeName) == 'elseif' || strtolower(@$objIf->nextSibling->nodeName) == 'else' || @$objIf->nextSibling->nodeType == XML_COMMENT_NODE || (@$objIf->nextSibling->nodeType == XML_TEXT_NODE && ctype_space(@$objIf->nextSibling->textContent))) {
                $objIf->parentNode->removeChild($objIf->nextSibling);
            }
        } else {
            while (strtolower(@$objIf->nextSibling->nodeName) == 'elseif' || @$objIf->nextSibling->nodeType == XML_COMMENT_NODE || (@$objIf->nextSibling->nodeType == XML_TEXT_NODE && ctype_space(@$objIf->nextSibling->textContent))) {
                if (strtolower(@$objIf->nextSibling->nodeName) == 'elseif') {
                    if (!$objFrag) {
                        $variableElseif = $objIf->nextSibling->getAttribute('var');
                        $cadCondicionElseif = $objIf->nextSibling->getAttribute('cond');
                        if ($this->_comprobarCondicion($variableElseif, $cadCondicionElseif)) {
                            $objFrag = $this->_obtenerElementos($objDom, $objIf->nextSibling);
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
        $variable = $this->_importarVariable($objSwitch->getAttribute('var'));
        $objFrag = null;
        while (strtolower(@$objSwitch->firstChild->nodeName) == 'case' || @$objSwitch->firstChild->nodeType == XML_COMMENT_NODE || (@$objSwitch->firstChild->nodeType == XML_TEXT_NODE && ctype_space(@$objSwitch->firstChild->textContent))) {
            if (strtolower(@$objSwitch->firstChild->nodeName) == 'case') {
                if (!$objFrag) {
                    $cadCondicion = $objSwitch->firstChild->getAttribute('cond');
                    if ($this->_comprobarCondicion($variable, $cadCondicion)) {
                        $objFrag = $this->_obtenerElementos($objDom, $objSwitch->firstChild);
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
     * <!-- La eliminacion de este comentario depende de $_bolEliminarComentario -->
     * <foreach var="variable" key="key" value="value" id="id">
     *      {{variable.key}} OR {{id.variable.key}}
     *      {{key}}          OR {{id.key}}
     *      {{value}}        OR {{id.value}}
     * </forach>
     */
    private function _compilar_foreach()
    {
        $objDom = $this->_obtenerObjDOM($this->_cadContenido);
        $objForeach = $objDom->getElementsByTagName('foreach')->item(0);
        $cadNombreVariable = $objForeach->getAttribute('var');
        $cadNombreClave = $objForeach->getAttribute('key')   != '' ? $objForeach->getAttribute('key') : 'key';
        $cadNombreValor = $objForeach->getAttribute('value') != '' ? $objForeach->getAttribute('value') : 'value';
        $cadIdenticador = $objForeach->getAttribute('id')    != '' ? $objForeach->getAttribute('id') . '.' : '';
        $cadContenido = $this->_obtenerHTML($objForeach);
        $mixedVar = $this->_importarVariable($cadNombreVariable);
        $objFrag = null;
        $cadContenidoProcesado = '';
        if (is_array($mixedVar) || is_object($mixedVar)) {
            foreach ($mixedVar as $clave => $valor) {
                $varclave = is_object($mixedVar) ? $mixedVar->$clave : $mixedVar[$clave];
                $cadContenidoProcesado .= str_replace($this->_abreVariable . $cadIdenticador . $cadNombreVariable . '.' . $cadNombreClave . $this->_cierraVariable, $varclave, $cadContenido);
                $cadContenidoProcesado  = str_replace($this->_abreVariable . $cadIdenticador . $cadNombreClave . $this->_cierraVariable, $clave, $cadContenidoProcesado);
                $cadContenidoProcesado  = str_replace($this->_abreVariable . $cadIdenticador . $cadNombreValor . $this->_cierraVariable, $valor, $cadContenidoProcesado);
            }
            $objFrag = $this->_convertirHTMLenElementos($objDom, $cadContenidoProcesado);
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
     * <!-- La eliminacion de este comentario depende de $_bolEliminarComentario --> 
     * <for index="i" var="variable" init="0" fin="count" order="asc" id="id" offset="">
     * </for>
     */
    private function _compilar_for()
    {
        $objDom            = $this->_obtenerObjDOM($this->_cadContenido);
        $objFor            = $objDom->getElementsByTagName('for')->item(0);
        $cadNombreVariable = $objFor->getAttribute('var');
        $mixedVar          = $this->_importarVariable($cadNombreVariable);
        $id                = $objFor->getAttribute('id') != '' ? $objFor->getAttribute('id') . '.' : '';
        $offset            = $objFor->getAttribute('offset');
        $cadContenido      = $this->_obtenerHTML($objFor);
        $objFrag           = null;
        $cadIndice         = $objFor->getAttribute('index') != '' ? $objFor->getAttribute('index')    : 'i';
        $asc               = strtolower($objFor->getAttribute('order')) == 'desc' ? 0 : 1;
        $offset            = $objFor->getAttribute('offset');
        $init              = 0;
        if ($objFor->getAttribute('init') == '') {
            $arrIndice = explode('.', $cadIndice);
            $cadIndice = $arrIndice[0];
            if(!$arrIndice[1]) {
                $init = 0;
            } else {
                $init = $arrIndice[1];
            }
        } else {
            $init = $objFor->getAttribute('init');
        }
        $cadTotal = $objFor->getAttribute('size');
        switch ($cadTotal) {
            case 'length':
                $max = strlen($mixedVar);
                break;
            case 'sizeof':
            case 'count':
            case '':
                $max = sizeof($mixedVar);
                break;
            default:
                $max = $cadTotal;
                break;
        }
        $a = $this->_abreVariable;  //visibilidad para leer en depuracion
        $c = $this->_cierraVariable; //visibilidad para leer en depuracion
        $cadContenidoProcesado = '';
        for ($asc == 1 ? $i = $init : $i = $max - 1; $asc == 1 ? $i < $max : $init <= $i; $asc == 1 ? $i++ : $i--) {
            $cadContenidoProcesado .= $cadContenido;
            // var="id.var.i"
            // var="var.i"
            // var='id.var.i'
            // var='var.i'
            $patronVarIndice = '/[v|V][a|A][r|R]\s*=\s*[\'|"]{1}\s*' . str_replace('.', '\.', $id . $cadNombreVariable . '.' . $cadIndice) . '\s*[\'|"]{1}/';
            $patronReemplazoVarIndice = 'var="' .  $id . $cadNombreVariable . '.' . $i . '"';
            $cadContenidoProcesado = preg_replace($patronVarIndice, $patronReemplazoVarIndice, $cadContenidoProcesado);
            // var='id.i'
            // var='i'
            // var="id.i"
            // var="i"
            $patronIndice = '/[v|V][a|A][r|R]\s*=\s*[\'|"]{1}\s*' . str_replace('.', '\.', $id . $cadIndice) . '\s*[\'|"]{1}/';
            $patronReemplazoIndice = 'var="' . $i . '"';
            $cadContenidoProcesado = preg_replace($patronIndice, $patronReemplazoIndice, $cadContenidoProcesado);
            // var="id.var.i.XXX.etc"
            // var='id.var.i.XXX.etc'
            // var="var.i.XXX.etc"
            // var='var.i.XXX.etc'
            $patronVarIndiceJ = '/[v|V][a|A][r|R]\s*=\s*[\'|"]{1}\s*' . str_replace('.', '\.', $id . $cadNombreVariable . '.' . $cadIndice) . '(.*?)\s*[\'|"]{1}/';
            while (preg_match($patronVarIndiceJ, $cadContenidoProcesado, $arrResultado)) {
                $patronReemplazoInciceJ = 'var="' .  $id . $cadNombreVariable . '.' . $i . $arrResultado[1] . '"';
                $cadContenidoProcesado = preg_replace($patronVarIndiceJ, $patronReemplazoInciceJ, $cadContenidoProcesado);
            }
            // {{id.var.i.XXX.etc}} 
            // {{var.i.XXX.etc}}
            $patronPrintVar = '/' . $a . str_replace('.', '\.', $id . $cadNombreVariable . '.' . $cadIndice) . '\.(.*?)' . $c  . '/';
            while (preg_match($patronPrintVar, $cadContenidoProcesado, $arrResultado)) {
                $patronReemplazoPrintVar = $a . $id . $cadNombreVariable . '.' . $i . '.' . $arrResultado[1] . $c;
                $cadContenidoProcesado = preg_replace($patronPrintVar, $patronReemplazoPrintVar, $cadContenidoProcesado);
            }
            // {{id.var.i}}
            // {{var.i}}
            $cadContenidoProcesado = str_replace($a . $id . $cadNombreVariable . '.' . $cadIndice . $c, $mixedVar[$i], $cadContenidoProcesado);
            // {{id.i}}
            // {{i}}
            if ($offset != '') { // efecto visual
                $offsetCalculado = 0;
                eval('$offsetCalculado=' . $i . $offset . ';');
                $cadContenidoProcesado  = str_replace($a . $id . $cadIndice . $c, $offsetCalculado, $cadContenidoProcesado);
            } else {
                $cadContenidoProcesado  = str_replace($a . $id . $cadIndice . $c, $i, $cadContenidoProcesado);
            }
        }
        $objFrag = $this->_convertirHTMLenElementos($objDom, $cadContenidoProcesado);
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
        $cadPatron = '/<(if|switch|foreach|for|while|include)[\s]*.*?>(.*?)<\/(if|switch|foreach|for|while|include)>/is';
        while (preg_match($cadPatron, $this->_cadContenido, $arrResultado)) {
            $nombreTag = strtolower($arrResultado[1]);
            $this->{'_compilar_' . $nombreTag}();
            if (defined('PHTML_DEPURANDO') && PHTML_DEPURANDO == true) {
                break;
            }
        }
        //$this->_compilar_const(true);
        //$this->_compilar_var(true);
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
