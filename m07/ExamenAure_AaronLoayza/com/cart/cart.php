<?php
include_once("com/catalog/catalog.php");
include_once("com/user/users.php");
////////////////////////////////////////////////////////////////////////////////////////////////////
function AddToCart($id_prod, $quantity, $dni, $password){
    echo "AddToCart<br>";
    if(UserExist($dni,$password)){
        if(_ExistProductInCatalog($id_prod, $quantity) && $quantity){
            _ExecuteAddToCart($id_prod, $quantity,$dni);
        }else{
            echo 'No hay suficiente producto o el id no existe';
        }
    }else{
        echo 'El usuario no existe';
    }
}
////////////////////////////////////////////////////////////////////////////////////////////////////
function RemoveFromCart($id_prod,$dni,$password){
    if(UserExist($dni,$password)){
        if(_ExistProductInCart($id_prod,$dni)){
            _ExecuteReturnCar($id_prod,$dni);
            _ExecuteRemoveFromCart($id_prod,$dni);
        }else{
            echo 'No existe el producto en el carrito';
        }
    }else{
        echo 'El usuario no existe';
    }
}
////////////////////////////////////////////////////////////////////////////////////////////////////
function ViewCart($dni,$password,$discount = '0'){
    if(UserExist($dni,$password)){
        $xml = _GetCart($dni);
        $xml->asXML('xmldb/cart'.$dni.'.xml');
        $counter = 1;
        $product_number = 1;
        $subtotal_cost = 0;
        $total_cost = 0;
        $tax = 0;
        $shipment = 0;
        echo '<h1>Cart Products</h1>';
        echo '-----------------------------------------------------------------------------------------------';
        echo '<br>';

        foreach($xml->children() as $child){
            if($child->dni_user == $dni){
                $subtotal_cost = $child->quantity * $child->price_item->price;
                $total_cost = $total_cost + $subtotal_cost;
                echo '<strong>Product ',$product_number,'<br></strong>';
                echo '<strong>Name:</strong>',$child->name,'&nbsp;&nbsp;&nbsp;';
                echo '<strong>Quantity:</strong>',$child->quantity,'&nbsp;&nbsp;&nbsp;';
                echo '<strong>Price:</strong>€',$child->price_item->price,'&nbsp;&nbsp;&nbsp;';
                echo '<strong>Currency:</strong>',$child->price_item->currency,'&nbsp;&nbsp;&nbsp;';
                echo '<strong>Subtotal Cost:</strong>€',$subtotal_cost,'&nbsp;&nbsp;&nbsp;';
                echo '<br>';
                echo '-----------------------------------------------------------------------------------------------';
                echo '<br>';
                $product_number = $product_number + 1;
            }
            $counter = $counter + 1;
        }

        if ($total_cost != 0){
            $tax = $total_cost * 0.21;
            $shipment = $total_cost * 0.05;
            echo '<strong>The total cost is:</strong>€', $total_cost,'<br>';
            echo '<strong>The total cost with IVA and shipping payment is:</strong>€', $total_cost + $tax + $shipment,'<br>';
            if($discount != '0'){
                if($discount == 'discount15'){
                    $total_cost = $total_cost * 0.85;
                    $tax = $total_cost * 0.21;
                    $shipment = $total_cost * 0.05;
                    echo '<strong>The total cost with IVA, shipping included and with discount is:</strong>€', $total_cost + $tax + $shipment,'<br>';
                }
                if($discount == 'discount20'){
                    $total_cost = $total_cost * 0.80;
                    $tax = $total_cost * 0.21;
                    $shipment = $total_cost * 0.05;
                    echo '<strong>The total cost with IVA, shipping included and with discount is:</strong>€', $total_cost + $tax + $shipment,'<br>';
                } 
                if($discount != 'discount15' && $discount != 'discount20'){
                    echo '<strong>Your discount code is not valid</strong>';
                }
            }
        }
    }else{
        echo 'El usuario no existe';
    }
}
////////////////////////////////////////////////////////////////////////////////////////////////////
function UpdateCart($id_prod,$new_quantity,$dni,$password){
    if(UserExist($dni,$password)){
        if(_ExistProductInCart($id_prod,$dni)){
            $difference = _CalculateDifference($new_quantity,$id_prod,$dni);
            if(_ExistProductInCatalog($id_prod,$difference)){
                _ExecuteUpdateCart($id_prod,$new_quantity,$dni);
            }else{
                echo 'No hay suficiente cantidad en stock';
            }
        }else{
            echo 'El producto no esta en el carro';
        }
    }else{
        echo 'El usuario no existe';
    }
}
////////////////////////////////////////////////////////////////////////////////////////////////////
function _ExecuteAddToCart($id_prod, $quantity, $dni){
    $xml = simplexml_load_file('xmldb/catalog.xml');
    $cart = _GetCart($dni);
    if(!_ExistProductInCart($id_prod,$dni)){
        foreach($xml->children() as $child){
            if($child->id_product == $id_prod){
                $item = $cart->addChild('product_item');
                $item->addChild('dni_user', $dni);
                $item->addChild('id_product', $id_prod);
                $item->addChild('name', $child->name);
                $item->addChild('quantity', $quantity);
                $item_price = $item->addChild('price_item');
                $item_price->addChild('price', $child->price_item->price);
                $item_price->addChild('currency', $child->price_item->currency);
                $cart->asXML('xmldb/cart'.$dni.'.xml');
                $child->quantity = $child->quantity - $quantity;
                $xml->asXML('xmldb/catalog.xml');
                return;
            }
        }
    }else{
        foreach($cart->children() as $child){
            if($child->id_product == $id_prod && $child->dni_user == $dni){
                $child->quantity = $child->quantity + $quantity;
                $cart->asXML('xmldb/cart'.$dni.'.xml');
                break;
            }
        }
        
        foreach($xml->children() as $child2){
            if($child2->id_product == $id_prod){
                $child2->quantity = $child2->quantity - $quantity;
                $xml->asXML('xmldb/catalog.xml');
                break;
            }
        }
    }
}
////////////////////////////////////////////////////////////////////////////////////////////////////
function _GetCart($dni){
    if(file_exists('xmldb/cart'.$dni.'.xml')){
        $cart = simplexml_load_file('xmldb/cart'.$dni.'.xml');
    }else{
        $cart = new SimpleXMLElement('<cart></cart>');
    }
    return $cart;
}
////////////////////////////////////////////////////////////////////////////////////////////////////
function _ExecuteRemoveFromCart($id_prod,$dni){
    $counter = 0;
    $xml = _GetCart($dni);
    $quantity = 0;

    foreach($xml->children() as $child){
        if($child->id_product == $id_prod && $child->dni_user == $dni){
            unset($xml->product_item[$counter]);
            $xml->asXML('xmldb/cart'.$dni.'.xml');
            //echo 'Producto eliminado';
            break;
        }
        $counter = $counter + 1;
    }
}
////////////////////////////////////////////////////////////////////////////////////////////////////
function _ExecuteReturnCar($id_prod,$dni){
    $xml = _GetCart($dni);
    $xml2 = simplexml_load_file('xmldb/catalog.xml');

    foreach($xml->children() as $child){
        if($child->id_product == $id_prod && $child->dni_user == $dni){
            $quantity = $child->quantity;
            //echo 'Producto eliminado';
            break;
        }
    }

    foreach($xml2->children() as $child2){
        if($child2->id_product == $id_prod){
            $child2->quantity = $child2->quantity + $quantity;
            $xml2->asXML('xmldb/catalog.xml');
            //echo 'Producto eliminado';
            break;
        }
    }
}
////////////////////////////////////////////////////////////////////////////////////////////////////
function _ExistProductInCart($id_prod,$dni){
    $xml = _GetCart($dni);

    foreach($xml->children() as $child){
        if($child->id_product == $id_prod && $child->dni_user == $dni){
            return true;
        }
    }
    return false;
}
////////////////////////////////////////////////////////////////////////////////////////////////////
function _ExecuteUpdateCart($id_prod,$quantity,$dni){
    $xml = _GetCart($dni);
    $xml2 = simplexml_load_file('xmldb/catalog.xml');
    $difference = _CalculateDifference($quantity,$id_prod,$dni);
    if ($difference == 0){
        foreach($xml->children() as $child){
            if($child->id_product == $id_prod && $child->dni_user == $dni){
                $quantitycart = $child->quantity;
            }
        }

        $quantityreal = $quantitycart - $quantity;

        foreach($xml2->children() as $child){
            if($child->id_product == $id_prod){
                $child->quantity = $child->quantity + $quantityreal;
                $xml2->asXML('xmldb/catalog.xml');
            }
        }
    }else{
        foreach($xml2->children() as $child){
            if($child->id_product == $id_prod){
                $child->quantity = $child->quantity - $difference;
                $xml2->asXML('xmldb/catalog.xml');
            }
        }
    }

    foreach($xml->children() as $child){
        if($child->id_product == $id_prod && $child->dni_user == $dni){
            $child->quantity = $quantity;
            $xml->asXML('xmldb/cart'.$dni.'.xml');
            return;
        }
    }
}
////////////////////////////////////////////////////////////////////////////////////////////////////
function _CalculateDifference($quantity,$id_prod,$dni){
    $xml1 =_GetCart($dni);
    $xml2 = simplexml_load_file('xmldb/catalog.xml');
    $quantitycart = 0;
    $difference = 0;

    foreach($xml1->children() as $child){
        if($child->id_product == $id_prod && $child->dni_user == $dni){
            $quantitycart = $child->quantity;
        }
    }

    if($quantity > $quantitycart){
        $difference = $quantity - $quantitycart;
        return $difference;
    }

    return $difference;
}
?>