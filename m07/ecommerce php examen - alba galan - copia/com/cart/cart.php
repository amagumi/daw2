<?php

//function add to cart
////////////////////////////////////////////////////////////////////////

function addToCart($idProd, $qty)
{
    if (productExists($idProd, $qty)) {
        _executeAddToCart($idProd, $qty);
        updateCatalog($idProd, $qty);
    } else {
        echo 'no hay producto o no hay stock';
    }
}

// escribe los nodos en el archivo xml del carrito
function _executeAddToCart($idProd, $qty)
{

    $cart = getCart();
    $encontrado = false;

    foreach ($cart->productItem as $product) {
        if ($idProd == $product->idProd) {
            $encontrado = true;
        } else {
            $encontrado = false;
        }
    }
    if ($encontrado == false) {
        $item = $cart->addChild('productItem');
        //addattribute()
        $item->addChild('idProd', $idProd);
        $item->addChild('qty', $qty);

        //escritura de nodos en un archivo xml
        $cart->asXML('xmlDB/cart.xml');
        echo "added to cart <br>";
    } else {
        echo "el producto ya está en el carrito, no se añadirá." . "<br><br>";
    }
}


//function crear cart
////////////////////////////////////////////////////////////////////////
function getCart()
{

    $file = 'xmlDB/cart.xml';

    // Load existing connections or create a new XML document
    if (file_exists($file)) {
        $cart = simplexml_load_file($file);
    } else {
        $cart = new SimpleXMLElement('<cart></cart>');
    }

    return $cart;
}

//functions remove from cart
////////////////////////////////////////////////////////////////////////

function removeFromCart($id) //el valor de $id se asigna en la llamada
{
    // se recoge en la variable xml el contenido de la ruta
    $xml = simplexml_load_file('xmlDB/cart.xml');
    echo 'hola1' . "<br>";
    // ruta del xml de dentro, se quiere buscar el idprod 
    foreach ($xml->xpath("/cart/productItem[idProd=$id]") as $idProd) {
        echo 'encontrado' . "<br>";
        unset($idProd[0]); //unsettea el nodo cuando lo encuentra
    }
    echo 'boorrado';
    $xml->asXML('xmlDB/cart.xml'); // sobreescribe los resultados
}




//functions update cart
////////////////////////////////////////////////////////////////////////


function updateCart($id, $qty) //el valor de $id y de $qty se asigna en la llamada
{
    // se recoge en la variable xml el contenido de la ruta
    $cart = simplexml_load_file('xmlDB/cart.xml');
    $catalog = ('xmlDB/catalog.xml');
    $productFinder = simplexml_load_file($catalog);

    // ruta del xml de dentro, se quiere buscar el idprod 


    // control de stock
    if (count($productFinder->product) > 0) {
        foreach ($productFinder->product as $product) {
            if ($id == $product->idProd) {
                if ($qty < $product->qty) {
                    foreach ($cart->xpath("/cart/productItem[idProd=$id]") as $qtyProd) {
                        echo 'encontrado' . "<br>";
                        $qtyProd[0]->qty = $qty; //actualiza el nodo cuando lo encuentra con la variable $qty cuyo valor se le pasa en la llamada

                        echo 'actualizado';
                        $cart->asXML('xmlDB/cart.xml'); // sobreescribe los resultados
                    }
                } else {
                    echo "no hay stock suficiente";
                }
            }
        }
    } else {
        echo "no hay productos en el catalogo";
    }
}



//function view cart
////////////////////////////////////////////////////////////////////////
function viewCart()
{

    $cart = 'xmlDB/cart.xml';
    $catalog = 'xmlDB/catalog.xml';

    $showcart = simplexml_load_file($cart);
    $showcatalog = simplexml_load_file($catalog);

    $idCart = 0;
    $qtyCart = 0;
    $subtotal = 0;
    $total = 0;


    if (count($showcart->productItem) > 0) {
        foreach ($showcart->productItem as $productCart) {
            $idCart = $productCart->idProd;
            $qtyCart = $productCart->qty;
            if (count($showcatalog->product) > 0) {
                foreach ($showcatalog->product as $productCatalog) {
                    if ((int)$idCart == (int)$productCatalog->idProd) {

                        $subtotal = $qtyCart * $productCatalog->price;
                        $total = $subtotal + $total;
                        echo
                        "id: " . $idCart . ", " .
                            "name: " . $productCatalog->prodName . ", " .
                            "quantity: " . $qtyCart . " ud." . ", " .
                            "price: " . $productCatalog->price . "eu. " .
                            "subtotal: " . $subtotal . " eu. " .
                            "<br>";
                    }
                }
            }
        }


        echo "<br><br>" . "el total es: " . $total + ($total * 0.21) . " eu. (iva incluido)";
    }
}


//function total a pagar
////////////////////////////////////////////////////////////////////////
