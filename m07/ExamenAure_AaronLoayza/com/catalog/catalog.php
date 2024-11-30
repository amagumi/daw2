<?php
function _ExistProductInCatalog($id_prod, $quantity = '1'){
    $xml = simplexml_load_file('xmldb/catalog.xml');

    foreach($xml->children() as $child){
        if($child->id_product == $id_prod){
            //echo 'producto encontrado<br>';
            if($child->quantity >= $quantity){
                //echo 'cantidad adecuada<br>';
                return true;
            }else{
                //echo'no esta en stock<br>';
                return false;
            }
        }
    }
    //echo 'Producto no encontrado<br>';
    return false;
}
?>