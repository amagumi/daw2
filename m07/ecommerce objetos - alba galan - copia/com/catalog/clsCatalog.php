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


