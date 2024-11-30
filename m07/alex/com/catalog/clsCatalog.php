<?php
class Catalog {
    private $products;
///////////////////////////////////////////////////////////////////////////////////////
    public function __construct() {
        $this->products = $this->loadCatalog();
    }
///////////////////////////////////////////////////////////////////////////////////////
    private function loadCatalog() {
        $file = 'xmldb/catalog.xml';
        if (file_exists($file)) {
            return simplexml_load_file($file);
        } else {
            $xml = new SimpleXMLElement('<catalog></catalog>');
            $xml->asXML($file); // Crear el archivo vacío
            return $xml;
        }
    }
///////////////////////////////////////////////////////////////////////////////////////    
    public function addProduct(int $id_prod, string $name, float $price, int $stock) {
        foreach ($this->products->product_item as $item) {
            if ((int)$item->id_product == $id_prod) {
                return; // Si el producto ya está en el catálogo, no lo agregamos
            }
        }
    
        $item = $this->products->addChild('product_item');
        $item->addChild('id_product', $id_prod);
    
        $itemInfo = $item->addChild('info_item');
        $itemInfo->addChild('name', $name);
        $itemInfo->addChild('price', $price);
        $itemInfo->addChild('stock', $stock);
    
        $this->saveCatalog();
    }
///////////////////////////////////////////////////////////////////////////////////////    
    private function saveCatalog() {
        $this->products->asXML('xmldb/catalog.xml');
    }
///////////////////////////////////////////////////////////////////////////////////////
    public function showCatalog() {
        if ($this->products->product_item->count() == 0) {
            echo "<br><strong>No hay productos en el catálogo.</strong><br>";
        } else {
            echo "<br><strong>LO QUE TENEMOS A LA VENTA:</strong><br>";
            foreach ($this->products->product_item as $item) {
                echo "ID Producto: " . $item->id_product . "<br>";
                echo "Nombre: " . $item->info_item->name . "<br>";
                echo "Stock: " . $item->info_item->stock . "<br>";
                echo "Precio: " . $item->info_item->price . "<br>";
                echo "<br>";
            }
        }
    }
///////////////////////////////////////////////////////////////////////////////////////
    public function checkStock(int $id_prod, int $quantity) {
        foreach ($this->products->product_item as $item) {
            if ((int)$item->id_product === $id_prod) {
                $stock = (int)$item->info_item->stock;
                return $quantity <= $stock ? true : $stock;
            }
        }
        return false; //No se encuentra el producto
    }
///////////////////////////////////////////////////////////////////////////////////////    
    //Actualizar carrito para controlar el stock
    public function updateStock(int $id_prod, int $quantity) {
        foreach ($this->products->product_item as $item) {
            if ((int)$item->id_product === $id_prod) {
                $currentStock = (int)$item->info_item->stock;
                $newStock = $currentStock - $quantity; //resta stock
                $item->info_item->stock = $newStock;
                $this->saveCatalog();
                return true;
            }
        }
        return false;
    }
}
?>