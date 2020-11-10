<?php

define('URL', 'http://localhost/phtml');
define('URL_DESCRIPCION', 'Phtml');

function print_pre($cadContenido)
{
    echo '<pre>';
    print_r($cadContenido);
    echo '</pre><br>';
}

include('Phtml.php');
$objPhtml = new Phtml();

class myFormat extends formatPhtml
{

    public function phtml_crear_enlace($mixedVar)
    {
        $mixedVar = strtolower($mixedVar);
        $mixedVar = str_replace(' ', '-', $mixedVar);
        $mixedVar = urlencode($mixedVar);
        return ($mixedVar);
    }
}


$objPhtml->setUserFormat(new myFormat);
$objPhtml->setVar('enlace', 'Categoria de Prueba');

// agregar contenido manual
$objPhtml->addContent('<p>Hola Mundo desde <b>addContent</b></p>', 'manual');
echo $objPhtml->obtenerContenido('manual') . '<br>';

// agregar contenido desde un archivo 
$objPhtml->addFile('plantillas/hola.phtml', 'archivo');
echo $objPhtml->obtenerContenido('archivo') . '<br>';


// agregar contenido en ejecucion
$objPhtml->catchContent('captar');
 echo "<p>Hola Mundo desde <b>catchContent</b></p>";
$objPhtml->catchContent('captar');
echo $objPhtml->obtenerContenido('captar') . '<br>';


// ejemplo de incluir un archivo con el tag include
$cadInclude = <<<EOL
<!-- Comentario antes del include -->        
<include>plantillas/include.phtml</include>
<!-- comentario despues del include -->
<p>Contenido despues de <b>include</b></p>
EOL;
$objPhtml->addContent($cadInclude, 'include');
echo $objPhtml->obtenerContenido('include') . '<br>';

// variables standard
$objPhtml->setVar('edad', 35);
$objPhtml->setVar('nombre', 'Dragos');

// variable arreglo simple
$arrUsuario[] = 'Dragos';
$arrUsuario[] = 'Petrica';
$arrUsuario[] = 'Macovei';
//$arrUsuario[] = 35;
$objPhtml->setVar('arrUsuario', $arrUsuario);

// variable arreglo multidimensional 
$arrDias[] = array('lunes', 'martes', 'miercoles', 'jueves', 'viernes');
$arrDias[] = array('domingo', 'sabado');
$objPhtml->setVar('arrDias', $arrDias);

// variable objeto
class usuario
{
    public $nombre = 'Dragos';
    public $edad   = 35;
    public function obtenerNombre()
    {
        return ($this->nombre);
    }
    public function obtenerEdad()
    {
        return ($this->edad);
    }
}
$objUsuario = new usuario();
$objPhtml->setVar('objUsuario', $objUsuario);


// ejemplo de depurar un tag if-elseif-else
$objPhtml->addFile('plantillas/if-elseif-else.phtml', 'if');
echo $objPhtml->obtenerContenido('if');

// ejemplo de depurar un tag switch-case-default
$objPhtml->addFile('plantillas/switch-case-default.phtml', 'switch');
echo $objPhtml->obtenerContenido('switch');

// ejemplo de depurar un tag foreach
$objPhtml->addFile('plantillas/foreach.phtml', 'foreach');
echo $objPhtml->obtenerContenido('foreach');

// ejemplo de depurar un tag for
$objPhtml->addFile('plantillas/for.phtml', 'for');
echo $objPhtml->obtenerContenido('for');

/* // ejemplo de depurar un tag while
$objPhtml->addFile('plantillas/do-while.phtml', 'while');
echo $objPhtml->obtenerContenido('while') . '<br>';
 */

// ejemplo de imprimir variables
$objPhtml->addFile('plantillas/var.phtml', 'var');
echo $objPhtml->obtenerContenido('var');


// ejemplo de imprimir variables
$objPhtml->addFile('plantillas/const.phtml', 'const');
echo $objPhtml->obtenerContenido('const');
