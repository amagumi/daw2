<?php

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function GetUser () {
    if (file_exists('xmldb/user.xml')) {
        $carrito = simplexml_load_file('xmldb/user.xml');
        echo "Funciona";
    } else {
        $carrito = new SimpleXMLElement('<users></users>');
    };
    return $carrito;
};

foreach ($xml->carrito as $carrito){
    if ((string)$carrito->dni == $dni) {
        $existe = true;
        break;
    }
}
if (!$existe) {
    $agregar = $xml->addChild("carrito", "");
    $agregar->addAttribute("nombre", $usuario);
    $agregar->addChild("dni", $dni);
    $xml->asXML("../xml/carritos.xml");
    echo "Carrito creado correctamente";
    echo '<br> <a href="../carrito.php"> Volver al menú </a>';
} else {
    echo "Carrito ya existe.";
    echo '<br> <a href="../carrito.php"> Volver al menú </a>';
};
?>