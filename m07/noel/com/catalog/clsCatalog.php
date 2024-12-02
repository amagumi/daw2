<?php

class clsCatalog {

    public $objetos = [];
    private int $total;
    private int $num_items;
    private $xml;
    private $xml_file='xmldb/catalogo.xml';
    
    function __construct() {
        // echo "Constructor";
        $this->Load();
        // $this->Analyze();
    }

    function Save() {
        $this->xml->asXML($this->xml_file);
    }
    function Load () {
        if (file_exists($this->xml_file)) {
            $this->xml = simplexml_load_file($this->xml_file);
            // echo "Funciona";
        } else {
            $this->xml = new SimpleXMLElement('<catalogo></catalogo>');
            $this->xml->asXML($this->xml_file);
        };
    }

    function Show() {
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
        foreach ($this->xml->producto as $producto) {
            echo "<tr>";
                echo "<td><strong>" . $producto['id_producto'] . "</strong></td>";
                echo "<td>" . $producto->nombre . "</td>";
                echo "<td>" . $producto->precio_item->precio . "</td>";
                echo "<td>" . $producto->precio_item->moneda . "</td>";
                echo "<td>" . $producto->stock . "</td>";
            echo "</tr>";
        }
        echo '</table>';
    }

    function Add($id_producto, $nombre, $precio, $moneda, $stock) {
        if (!$this->ExistProduct($id_producto)) {
            $producto = $this->xml->addChild('producto');
            
            $producto->addAttribute('id_producto', $id_producto);
            $producto->addChild('nombre', $nombre);
            $producto->addChild('stock', $stock);
            $producto->addChild('precio_item');
            $producto->precio_item->addChild('precio', $precio * 1.21);
            $producto->precio_item->addChild('moneda', $moneda);
            
            echo "Producto añadido al catalogo exitosamente (incluyendo IVA). <br>";
            $this->Save();
        } else {
            echo "El producto ya existe en el catalogo. <br>";
        }
    }

    function AddStock($id_producto, $sumar) {
        if ($this->ExistProduct($id_producto)) {
            $producto = $this->xml->xpath("/catalogo/producto[@id_producto='$id_producto']/stock");
            $producto[0][0] = $producto[0][0] + $sumar;
            $this->Save();
            echo "Stock aumentado exitosamente. <br>";
        } else {
            echo "No se ha podido encontrar el producto o no se ha podido modificar. <br>";
        }
    }

    function Substract($id_producto, $restar) {
        if ($this->ExistProduct($id_producto)) {
            $producto = $this->xml->xpath("/catalogo/producto[@id_producto='$id_producto']/stock");
            $producto[0][0] = $producto[0][0] - $restar;
            if ($producto[0][0] < 0) {
                $producto[0][0] = 0;
            }
            $this->Save();
            echo "Stock restado exitosamente. <br>";
        } else {
            echo "No se ha podido encontrar el producto o no se ha podido modificar. <br>";
        }
    }

    function ModifyStock($id_producto, $stock) {
        if ($this->ExistProduct($id_producto)) {
            $producto = $this->xml->xpath("/catalogo/producto[@id_producto='$id_producto']/stock");
            $producto[0][0] = $stock;
            $this->Save();
            echo "Stock modificado exitosamente. <br>";
        } else {
            echo "No se ha podido encontrar el producto o no se ha podido modificar. <br>";
        }
    }
    
    function CheckStock ($id_producto) {
        $stock = 0;
        $producto = $this->GetProduct($id_producto);
        $stock = $producto->stock;
        return $stock;
    }

    function GetPrice ($id_producto) {
        $producto = $this->GetProduct($id_producto);
        $precio = $producto->precio_item->precio;
        return $precio;
    }

    function GetProduct($id_producto) {
        foreach ($this->xml->xpath("/catalogo/producto") as $producto) {
            if ($producto['id_producto'] == $id_producto) {
                return $producto;
            }
        }
    }

    function Analyze() {
        $this->num_items = count($this->xml->xpath("/catalogo/producto"));
        echo $this->num_items;
    }
    function ExistProduct($id_producto) {
        foreach ($this->xml->xpath("/catalogo/producto") as $producto) {
            if ($producto['id_producto'] == $id_producto) {
                return true;
            }
        }
        return false;
    }

}