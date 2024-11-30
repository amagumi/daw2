<?php
class Cart {
    private string $username;
    private SimpleXMLElement $products;
///////////////////////////////////////////////////////////////////////////////////////
    public function __construct($username) {
        $this->username = $username;
        $this->products = $this->loadCart();
    }
///////////////////////////////////////////////////////////////////////////////////////
    private function loadCart() {
        $file = 'xmldb/' . $this->username . '_cart.xml';
        if (file_exists($file)) {
            return simplexml_load_file($file);
        } else {
            return new SimpleXMLElement('<cart></cart>');
        }
    }
///////////////////////////////////////////////////////////////////////////////////////
    public function addProduct(int $id_prod, string $name, int $quantity, float $price) {
        $catalog = new Catalog();
        
        //Mira si hay suficiente stock
        $stockCheck = $catalog->checkStock($id_prod, $quantity);
        if ($stockCheck === true) {
            echo "Has añadido el producto con el ID $id_prod <br>";
            echo "<strong>TU CARRITO</strong>";
        
            //Añade el producto al carrito
            $item = $this->products->addChild('product_item');
            $item->addChild('id_product', $id_prod);
            $item->addChild('name', $name);
            $item->addChild('quantity', $quantity);
            $item->addChild('price', $price);
        
            $this->saveCart();
            
            //Para ataulizar el carrito 
            $catalog->updateStock($id_prod, $quantity);
        } elseif ($stockCheck === false) {
            echo "El producto no existe.<br>";
        } else {
            echo "No hay suficiente stock.<br>";
        }
    }
///////////////////////////////////////////////////////////////////////////////////////    
    private function saveCart() {
        $this->products->asXML('xmldb/' . $this->username . '_cart.xml');
    }
///////////////////////////////////////////////////////////////////////////////////////
    public function showCart(string $discountCode = '') {
        $totalPrice = 0;
        $discount = 0;
    
        if ($discountCode == 'DESC10') {
            $discount = 0.10;
        } elseif ($discountCode == 'DESC20') {
            $discount = 0.20;
        }
    
        if ($this->products->product_item->count() == 0) {
            echo "<br><strong>El carrito está vacío.</strong><br>";
        } else {
            echo "<table border='1' cellpadding='10' cellspacing='0'>";
            echo "<tr>
                    <th>ID Producto</th>
                    <th>Nombre</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                  </tr>";
    
            foreach ($this->products->product_item as $item) {
                $productTotal = (float)$item->price * (int)$item->quantity;
                $totalPrice += $productTotal;
    
                echo "<tr>";
                echo "<td>" . $item->id_product . "</td>";
                echo "<td>" . $item->name . "</td>";
                echo "<td>" . $item->quantity . "</td>";
                echo "<td>" . $item->price . " €</td>";
                echo "</tr>";
            }
    
            echo "</table>";
            echo "<br><strong>Total de la compra: " . $totalPrice . " €</strong><br>";
    
            if ($discount > 0) {
                $totalWithDiscount = $totalPrice - ($totalPrice * $discount);
                echo "<strong>Descuento aplicado (" . ($discount * 100) . "%): -" . ($totalPrice * $discount) . " €</strong><br>";
                echo "<strong>Total con descuento: " . $totalWithDiscount . " €</strong><br>";
            }
        }
    }
///////////////////////////////////////////////////////////////////////////////////////    
    //Para que muestre en la web el archivo xml del carrito
    public function ShowXML() {
        header("Content-Type: text/xml");
        echo $this->products->asXML();
    }
}
?>
