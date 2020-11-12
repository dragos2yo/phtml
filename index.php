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


$objPhtml->userFormat(new myFormat);
$objPhtml->addVar('enlace', 'Categoria de Prueba');

// agregar contenido manual
/* $objPhtml->addContent('<p>Hola Mundo desde <b>addContent</b></p>'); */
//echo $objPhtml->obtenerContenido('manual') . '<br>';

// agregar contenido desde un archivo 
/* $objPhtml->addFile('plantillas/hola.phtml'); */
//echo $objPhtml->obtenerContenido('archivo') . '<br>';


// agregar contenido en ejecucion
/* $objPhtml->catchContent();
 echo "<p>Hola Mundo desde <b>catchContent</b></p>";
$objPhtml->catchContent(); */
//echo $objPhtml->obtenerContenido('captar') . '<br>';


// ejemplo de incluir un archivo con el tag include
$cadInclude = <<<EOL
<!-- Comentario antes del include -->        
<include>plantillas/include.phtml</include>
<!-- comentario despues del include -->
<p>Contenido despues de <b>include</b></p>
EOL;
/* $objPhtml->addContent($cadInclude); */
//echo $objPhtml->obtenerContenido('include') . '<br>';

// variables standard
$objPhtml->addVar('edad', 35);
$objPhtml->addVar('nombre', 'Dragos');

// variable arreglo simple
$arrUsuario[] = 'Dragos';
$arrUsuario[] = 'Petrica';
$arrUsuario[] = 'Macovei';
//$arrUsuario[] = 35;
$objPhtml->addVar('arrUsuario', $arrUsuario);

// variable arreglo multidimensional 
$arrDias[] = array('lunes', 'martes', 'miercoles', 'jueves', 'viernes');
$arrDias[] = array('domingo', 'sabado');
$objPhtml->addVar('arrDias', $arrDias);

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
$objPhtml->addVar('objUsuario', $objUsuario);


// ejemplo de depurar un tag if-elseif-else
//$objPhtml->addFile('plantillas/if-elseif-else.phtml');

// ejemplo de depurar un tag switch-case-default
// $objPhtml->addFile('plantillas/switch-case-default.phtml');

// ejemplo de depurar un tag foreach
// $objPhtml->addFile('plantillas/foreach.phtml'); 

// ejemplo de depurar un tag for
$objPhtml->addFile('plantillas/for.phtml');

// ejemplo de depurar un tag while
//$objPhtml->addFile('plantillas/do-while.phtml');

// ejemplo de imprimir variables
//$objPhtml->addFile('plantillas/var.phtml');

// ejemplo de imprimir variables
// $objPhtml->addFile('plantillas/const.phtml');

$inicial = microtime(true);
echo $objPhtml->output();

$final = microtime(true);
print_pre($final - $inicial);

