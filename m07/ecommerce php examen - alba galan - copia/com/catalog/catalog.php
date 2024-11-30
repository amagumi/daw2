<?php

// la funcion productExists abre el archivo xml de los productos 
// y recorre cada nodo buscando el producto deseado (bucle) 


function productExists($idProd, $qty)
{
    $catalog = 'xmlDB/catalog.xml';

    // carga el archivo catalog.xml
    $productFinder = simplexml_load_file($catalog);

    // verifica si hay al menos un producto
    if (count($productFinder->product) > 0) {
        foreach ($productFinder->product as $product) {
            // echo $product->prodName . " = " . $product->qty . " ud." . "<br>";
            if ($idProd == $product->idProd) {

                if ($qty < $product->qty) {

                    return true; // el producto existe y hay stock

                }
            }
        }
    }

    return false; // no hay productos en el catalogo / no existe el id / no existe la cantidad
}



//function crear catalogo
////////////////////////////////////////////////////////////////////////

function getCatalog()
{

    $file = 'xmlDB/catalog.xml';

    if (file_exists($file)) {
        echo "hola";
        $catalog = simplexml_load_file($file);
    } else {
        echo "adios";
        $catalog = new SimpleXMLElement('<catalog></catalog>');
    }

    return $catalog;
}



//function crear producto
////////////////////////////////////////////////////////////////////////

function productRegister($idProd, $prodName, $qty, $price)
{

    echo "product register <br>";
    echo $idProd;
    echo $prodName;
    echo $qty;
    echo $price;

    $catalog = getCatalog();

    $product = $catalog->addChild('product');
    $product->addAttribute("id", "1");
    $product->addChild('idProd', $idProd);
    $product->addChild('prodName', $prodName);
    $product->addChild('qty', $qty);
    $product->addChild('price', $price);

    $prodCurrency = $product->addChild('prodCurrency');
    $prodCurrency->addChild('currency', 'EU');

    $catalog->asXML('xmlDB/catalog.xml');

    echo $catalog;
}



//function ver catalogo
////////////////////////////////////////////////////////////////////////

function viewCatalog()
{
    $catalog = 'xmlDB/catalog.xml';

    // Cargar el archivo catalog.xml
    $productFinder = simplexml_load_file($catalog);

    // Verificar si hay productos en el catálogo
    if (count($productFinder->product) > 0) {
        foreach ($productFinder->product as $product) {
            // Mostrar los detalles de cada producto
            echo "ID: " . $product->idProd . "<br>";
            echo "Nombre: " . $product->prodName . "<br>";
            echo "Precio: " . $product->price . "€<br>";
            echo "Stock: " . $product->qty . " ud.<br>";
            echo "<hr>"; // Separador entre productos
        }
    } else {
        echo "No hay productos en el catálogo.";
    }
}



//function update catalogo
////////////////////////////////////////////////////////////////////////

function updateCatalog($id, $qty)
{
    // Cargar el carrito y el catálogo
    $cart = simplexml_load_file('xmlDB/cart.xml');
    $catalog = simplexml_load_file('xmlDB/catalog.xml');

    // Control de stock en el catálogo
    if (count($catalog->product) > 0 && count($cart->productItem) > 0) {

        // Buscar el producto en el catálogo por ID
        foreach ($catalog->product as $product) {
            if ($id == (int)$product->idProd) {
                // Verificar si hay suficiente stock
                if ($qty <= (int)$product->qty) {
                    // Restar la cantidad comprada del stock disponible en el catálogo
                    $newStock = (int)$product->qty - $qty;
                    $product->qty = $newStock;

                    // Actualizar el archivo XML del catálogo
                    $catalog->asXML('xmlDB/catalog.xml');
                    echo "Stock actualizado para el producto con ID $id: quedan $newStock unidades.<br>";
                } else {
                    echo "Stock insuficiente para el producto con ID $id.<br>";
                }
            }
        }
    } else {
        echo "No hay productos en el catálogo o en el carrito.";
    }
}
