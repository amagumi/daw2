<?php

class clsCatalog
{
    //propiedad de catalog que almacena productos, por eso es un array
    private $productsArr = [];

    public function __construct()
    {
        $this->productsArr = [];
        $this->_loadFromXML('xmlDB/catalog.xml');
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // esta funcion lo que hace es convertir a objeto cada producto ya existente en el 
    // xml de catalogo

    private function _loadFromXML($file)
    {
        if (file_exists($file)) {
            $catalogXML = simplexml_load_file($file);
            foreach ($catalogXML->product as $product) {
                //recorre el xml y por cada nodo product lo convierte en objeto
                $createProduct = new clsProduct(
                    (int)$product->idProd,
                    (string)$product->prodName,
                    (int)$product->qty,
                    (float)$product->price
                );
                $this->registerProduct($createProduct);
            }
        }
    }


    // agregar los objetos producto al catálogo
    public function registerProduct(clsProduct $product)
    {
        $this->productsArr[$product->getIdProduct()] = $product;
    }


    //obtener un producto por ID
    public function getProductById($id)
    {
        return $this->productsArr[$id] ?? null;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // mostrar todos los productos del catalogo xml

    public function showCatalog()
    {
        $file = 'xmlDB/catalog.xml';  // Ruta al archivo XML de tu catálogo

        if (file_exists($file)) {
            // Cargar y mostrar el XML en su formato original
            header("Content-Type: application/xml");
            readfile($file);
        } else {
            echo "El catálogo no está disponible.";
        }
    }


    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // funcion que obtiene un objeto proucto del array de objetos 

    function getProduct($idProd)
    {
        foreach ($this->productsArr as $product) {
            if ($idProd == $product->getIdProduct()) {
                return $product;
            }
        }
        return null;
    }
}



// 
// 
// 
// 
// 
// 
// 
// 
// 
// 

// la funcion productExists abre el archivo xml de los productos 
// y recorre cada nodo buscando el producto deseado (bucle) 


// function productExists($idProd, $qty)
// {
//     $catalog = 'xmlDB/catalog.xml';

//     // carga el archivo catalog.xml
//     $productFinder = simplexml_load_file($catalog);

//     // verifica si hay al menos un producto
//     if (count($productFinder->product) > 0) {
//         foreach ($productFinder->product as $product) {
//             // echo $product->prodName . " = " . $product->qty . " ud." . "<br>";
//             if ($idProd == $product->idProd) {

//                 if ($qty < $product->qty) {

//                     return true; // el producto existe y hay stock

//                 }
//             }
//         }
//     }

//     return false; // no hay productos en el catalogo / no existe el id / no existe la cantidad
// }


// //function crear catalogo
// ////////////////////////////////////////////////////////////////////////

// function getCatalog()
// {

//     $file = 'xmlDB/catalog.xml';

//     if (file_exists($file)) {
//         echo "hola";
//         $catalog = simplexml_load_file($file);
//     } else {
//         echo "adios";
//         $catalog = new SimpleXMLElement('<catalog></catalog>');
//     }

//     return $catalog;
// }



// //function crear producto
// ////////////////////////////////////////////////////////////////////////

// function productRegister($idProd, $prodName, $qty, $price)
// {

//     echo "product register <br>";
//     echo $idProd;
//     echo $prodName;
//     echo $qty;
//     echo $price;

//     $catalog = getCatalog();

//     $product = $catalog->addChild('product');
//     $product->addChild('idProd', $idProd);
//     $product->addChild('prodName', $prodName);
//     $product->addChild('qty', $qty);
//     $product->addChild('price', $price);

//     $catalog->asXML('xmlDB/catalog.xml');

//     echo $catalog;
// }



// //function ver catalogo
// ////////////////////////////////////////////////////////////////////////

// function viewCatalog()
// {
//     $catalog = 'xmlDB/catalog.xml';

//     // Cargar el archivo catalog.xml
//     $productFinder = simplexml_load_file($catalog);

//     // Verificar si hay productos en el catálogo
//     if (count($productFinder->product) > 0) {
//         foreach ($productFinder->product as $product) {
//             // Mostrar los detalles de cada producto
//             echo "ID: " . $product->idProd . "<br>";
//             echo "Nombre: " . $product->prodName . "<br>";
//             echo "Precio: " . $product->price . "€<br>";
//             echo "Stock: " . $product->qty . " ud.<br>";
//             echo "<hr>"; // Separador entre productos
//         }
//     } else {
//         echo "No hay productos en el catálogo.";
//     }
// }
