<?php
function print_pre($cadContenido)
{
    echo '<pre>';
    print_r($cadContenido);
    echo '</pre><br>';
}

include('Phtml.php');

$objPhtml = new Phtml();

/* // agregar contenido manual
$objPhtml->agregarContenido('<p>Hola Mundo desde <b>agregarContenido</b></p>', 'manual');
echo $objPhtml->obtenerContenido('manual') . '<br>';
 */
/* // agregar contenido desde un archivo 
$objPhtml->agregarArchivo('plantillas/hola.phtml', 'archivo');
echo $objPhtml->obtenerContenido('archivo') . '<br>';
 */
/* // agregar contenido en ejecucion
$objPhtml->captarContenido('captar');
 echo "<p>Hola Mundo desde <b>captarContenido</b></p>";
$objPhtml->captarContenido('captar');
echo $objPhtml->obtenerContenido('captar') . '<br>';
 */
// ejemplo de incluir un archivo con el tag include
/* $cadInclude = <<<EOL
<!-- Comentario antes del include -->        
<include>plantillas/include.phtml</include>
<!-- comentario despues del include -->
<p>Contenido despues de <b>include</b></p>
EOL;
$objPhtml->agregarContenido($cadInclude, 'include');
echo $objPhtml->obtenerContenido('include') . '<br>'; */

// variables standard
$objPhtml->agregarVariable('edad', 35);
$objPhtml->agregarVariable('nombre', 'Dragos');

// variable arreglo simple
$arrUsuario[] = 'Dragos';
$arrUsuario[] = 'Petrica';
$arrUsuario[] = 'Macovei';
$arrUsuario[] = 35;
$objPhtml->agregarVariable('arrUsuario', $arrUsuario);

// variable arreglo multidimensional 
$arrDias[] = array('lunes', 'martes', 'miercoles', 'jueves', 'viernes');
$arrDias[] = array('domingo', 'sabado');
$objPhtml->agregarVariable('arrDias', $arrDias);

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
$objPhtml->agregarVariable('objUsuario', $objUsuario);


/* // ejemplo de depurar un tag if-elseif-else
$objPhtml->agregarArchivo('plantillas/if-elseif-else.phtml', 'if');
echo $objPhtml->obtenerContenido('if'); */

/* // ejemplo de depurar un tag switch-case-default
$objPhtml->agregarArchivo('plantillas/switch-case-default.phtml', 'switch');
echo $objPhtml->obtenerContenido('switch'); */

/* // ejemplo de depurar un tag foreach
$objPhtml->agregarArchivo('plantillas/foreach.phtml', 'foreach');
echo $objPhtml->obtenerContenido('foreach'); */

// ejemplo de depurar un tag for
$objPhtml->agregarArchivo('plantillas/for.phtml', 'for');
echo $objPhtml->obtenerContenido('for');

/* // ejemplo de depurar un tag while
$objPhtml->agregarArchivo('plantillas/do-while.phtml', 'while');
echo $objPhtml->obtenerContenido('while') . '<br>';
 */

/* for($i = 0; $i < count($arrDias); $i++) {
     echo '<ul>';
     for($j = 0; $j < count($arrDias[$i]); $j++) {
         echo '<li>' . $arrDias[$i][$j] . '</li>';
     }
     echo '</ul>';
 } */

/* $id = 'blah.';
 $cadNombreVariable = 'arrDias';
 $cadIndice = 'i';
 $i = '1';
 $cadContenidoProcesado = "var='blah.arrDias.i'";
 $patronVarIndice = '/[v|V][a|A][r|R]\s*=\s*[\'|"]{1}\s*' . str_replace('.', '\.', $id . $cadNombreVariable . '.' . $cadIndice) . '\s*[\'|"]{1}/';
 $patronReemplazoVarIndice = 'var="' .  $id . $cadNombreVariable . '.' . $i . '"';
 $cadContenidoProcesado = preg_replace($patronVarIndice, $patronReemplazoVarIndice, $cadContenidoProcesado);
 print_pre($cadContenidoProcesado); */

/* $a = '{{';
$c = '}}';
$id = 'blah.';
$cadNombreVariable = 'arrDias';
$cadIndice = 'i';
$i = '1';
$cadContenidoProcesado = "{{blah.arrDias.i.j}}";
$patronPrintVar = '/' . $a . str_replace('.', '\.', $id . $cadNombreVariable . '.' . $cadIndice) . '\.(.*?)' . $c  . '/';
while (preg_match($patronPrintVar, $cadContenidoProcesado, $arrResultado)) {
    $patronReemplazoPrintVar = $a . $id . $cadNombreVariable . '.' . $i . '.' . $arrResultado[1] . $c;
    $cadContenidoProcesado = preg_replace($patronPrintVar, $patronReemplazoPrintVar, $cadContenidoProcesado);
}
print_pre($cadContenidoProcesado); */

/* $id = 'blah.';
$cadNombreVariable = 'arrDias';
$cadIndice = 'i';
$i = '1';
$cadContenidoProcesado = "var='blah.arrDias.i.j'";

$patronVarIndiceJ = '/[v|V][a|A][r|R]\s*=\s*[\'|"]{1}\s*' . str_replace('.', '\.', $id . $cadNombreVariable . '.' . $cadIndice) . '(.*?)\s*[\'|"]{1}/';
while (preg_match($patronVarIndiceJ, $cadContenidoProcesado, $arrResultado)) {
    $patronReemplazoInciceJ = 'var="' .  $id . $cadNombreVariable . '.' . $i . $arrResultado[1] . '"';
    $cadContenidoProcesado = preg_replace($patronVarIndiceJ, $patronReemplazoInciceJ, $cadContenidoProcesado);
}
print_pre($cadContenidoProcesado);
 */
