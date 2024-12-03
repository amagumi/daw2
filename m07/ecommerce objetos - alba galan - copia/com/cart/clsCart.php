<?php
class clsCart
{
    private string $username;
    private $catalog;
    private object $product;

    public function __construct($username, $catalog)
    {
        $this->username = $username;
        $this->catalog = $catalog;
        $this->product = $this->loadCart();
    }


    private function loadCart()
    {
        $cartFile = 'xmlDB/' . $this->username . 'Cart.xml';
        if (file_exists($cartFile)) {
            return simplexml_load_file($cartFile);
        } else {
            return new SimpleXMLElement('<cart></cart>');
        }
    }



    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // añadir producto al cart xml

    public function addProduct(int $idProd, int $qty)
    {
        //$p es un producto
        $p = $this->catalog->getProduct($idProd); // getProduct retorna un objeto de clase product
        $qtyCatalog = $p->getQuantity();
        $newQtyCatalog = $qtyCatalog - $qty;
        $priceCatalog = $p->getPrice();
        $totalPriceProduct = $qty * $priceCatalog; // multiplica el precio ud * qty para reflejarlo en el carrito

        if ($qtyCatalog > $qty) {
            echo "has añadido " . $p->getProdName() . " al carrito" . "<br>";

            $cartItem = $this->product->addChild('productItem');

            $cartItem->addChild('idProd', $p->getIdProduct());
            $cartItem->addChild('prodName', $p->getProdName());
            $cartItem->addChild('qty', $qty);
            $cartItem->addChild('price', $totalPriceProduct);

            $this->_saveCart();

            // se settea el nuevo stock en el objeto producto en el productsArr
            $p->setQuantity($newQtyCatalog);
            $this->_updateCatalog($idProd, $newQtyCatalog);
        } else {
            echo "no hay suficiente stock" . "<br>";
        }
    }


    // guarda lo que hayas añadido al carrito dado un usuario
    private function _saveCart()
    {
        $this->product->asXML('xmlDB/' . $this->username . 'Cart.xml');
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // mostrar el carrito en formato xml 

    public function showCart()
    {
        $cartFile = 'xmlDB/' . $this->username . 'Cart.xml';

        if (file_exists($cartFile)) {
            header("Content-Type: application/xml");
            readfile($cartFile);
        } else {
            echo "el carrito no existe";
        }
    }


    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // actualiza el stock en el xml de catalogo

    private function _updateCatalog($idProd, $newQtyCatalog)
    {
        $catalog = simplexml_load_file('xmlDB/catalog.xml');

        foreach ($catalog->xpath("/catalog/product[idProd=$idProd]") as $qtyProd) {
            $qtyProd[0]->qty = $newQtyCatalog; //actualiza el nodo cuando lo encuentra con la variable $qty cuyo valor se le pasa en la llamada
            $catalog->asXML('xmlDB/catalog.xml'); // sobreescribe los resultados
        }
    }


    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // eliminar un producto del carrito

    public function removeFromCart($username, $idProd)
    {
        $xml = simplexml_load_file('xmlDB/' . $username . 'Cart.xml');
        // ruta del xml de dentro, se quiere buscar el idprod 
        foreach ($xml->xpath("/cart/productItem[idProd=$idProd]") as $idProd) {
            // echo 'encontrado' . "<br>";
            unset($idProd[0]); //unsettea el nodo cuando lo encuentra
            echo 'borrado';
        }
        // echo 'borrado';
        $xml->asXML('xmlDB/' . $username . 'Cart.xml'); // sobreescribe los resultados
    }
}
