<?php

class clsCatalog {

    public $productos = [];
    private int $num_items;
    private $xml;
    private $xml_file='xmldb/catalogo.xml';
    
    public function __construct() {
        // echo "Constructor";
        $this->Load();
        // $this->Analyze();
    }

    public function Save() {
        $guardar = new SimpleXMLElement('<catalogo></catalogo>');
        foreach ($this->productos as $producto) {
            $item = $guardar->addChild('producto');
            $item->addAttribute('id_producto', $producto->GetIdProducto());
            $item->addChild('nombre', $producto->GetNombre());
            $item->addChild('stock', $producto->GetStock());
            $item->addChild('precio_item');
            $item->precio_item->addChild('precio', $producto->GetPrecio());
            $item->precio_item->addChild('moneda', $producto->GetMoneda());
        }
        $guardar->asXML($this->xml_file);
    }

    public function Load () {
        if (file_exists($this->xml_file)) {
            $this->xml = simplexml_load_file($this->xml_file);
            // echo "Funciona";
        } else {
            $this->xml = new SimpleXMLElement('<catalogo></catalogo>');
            $this->xml->asXML($this->xml_file);
        };
        $this->productos = [];
        foreach ($this->xml->xpath("/catalogo/producto") as $producto) {
            $item = new clsProducto($producto);
            array_push($this->productos, $item);
        }
    }

    public function Show() {
        // echo $this->xml->asXML();
        echo "<br><br>";
        echo '<table border = "1">';
        echo '<caption><strong>Catalogo actual<strong></caption>';
        echo "<tr>";
            echo "<td><strong>ID_PRODUCTO</strong></td>";
            echo "<td><strong>PRODUCTO</strong></td>";
            echo "<td><strong>PRECIO</strong>(iva incluido)</td>";
            echo "<td><strong>MONEDA</strong></td>";
            echo "<td><strong>STOCK</strong></td>";
        echo "</tr>";
        foreach ($this->productos as $producto) {
            echo "<tr>";
                echo "<td><strong>" . $producto->GetIdProducto() . "</strong></td>";
                echo "<td>" . $producto->GetNombre() . "</td>";
                echo "<td>" . $producto->GetPrecio() . "</td>";
                echo "<td>" . $producto->GetMoneda() . "</td>";
                echo "<td>" . $producto->GetStock() . "</td>";
            echo "</tr>";
        }
        echo '</table>';
    }

    public function Add($id_producto, $nombre, $precio, $moneda, $stock) {
        if (!$this->ExistProduct($id_producto)) {
            $producto = new SimpleXMLElement('<producto></producto>');            
            $producto->addAttribute('id_producto', $id_producto);
            $producto->addChild('nombre', $nombre);
            $producto->addChild('stock', $stock);
            $producto->addChild('precio_item');
            $producto->precio_item->addChild('precio', $precio * 1.21);
            $producto->precio_item->addChild('moneda', $moneda);

            array_push($this->productos, new clsProducto($producto));
            
            echo "Producto añadido al catalogo exitosamente (incluyendo IVA). <br>";
            $this->Save();
        } else {
            echo "El producto ya existe en el catalogo. <br>";
        }
    }

    public function AddStock($id_producto, $sumar) {
        if ($this->ExistProduct($id_producto)) {
            $producto = $this->GetProduct($id_producto);
            $producto->SetStock($producto->GetStock() + $sumar);
            echo "Stock aumentado exitosamente. <br>";
            $this->Save();
            return;
        } else {
            echo "No se ha podido encontrar el producto o no se ha podido modificar. <br>";
        }
    }

    public function Substract($id_producto, $restar) {
        if ($this->ExistProduct($id_producto)) {
            $producto = $this->GetProduct($id_producto);
            $producto->SetStock($producto->GetStock() - $restar);
            echo "Stock restado exitosamente. <br>";
            $this->Save();
            return;
        } else {
            echo "No se ha podido encontrar el producto o no se ha podido modificar. <br>";
        }
    }

    public function ModifyStock($id_producto, $stock) {
        if ($this->ExistProduct($id_producto)) {
            $producto = $this->GetProduct($id_producto);
            $producto->SetStock($stock);
            echo "Stock modificado exitosamente. <br>";
            $this->Save();
            return;
        } else {
            echo "No se ha podido encontrar el producto o no se ha podido modificar. <br>";
        }
    }
    
    public function CatalogToCart ($id_producto, $cantidad) {
        $producto = $this->GetProduct($id_producto);
        $newItem = $producto->CatalogToCart($cantidad);
        return $newItem;
    }

    public function CheckStock ($id_producto) {
        $producto = $this->GetProduct($id_producto);
        return $producto->GetStock();
    }

    public function GetPrice ($id_producto) {
        $producto = $this->GetProduct($id_producto);
        return $producto->GetPrecio();
    }

    public function GetProduct($id_producto): mixed{
        foreach ($this->productos as $producto) {
            if ($producto->GetIdProducto() == $id_producto) {
                return $producto;
            }
        }
        return false;
    }

    public function Analyze() {
        $this->num_items = count($this->xml->xpath("/catalogo/producto"));
        echo $this->num_items;
    }
    public function ExistProduct($id_producto) {
        foreach ($this->productos as $producto) {
            if ($producto->GetIdProducto() == $id_producto) {
                return true;
            }
        }
        return false;
    }

}