<?php

class clsCart {

    private int $total;
    private int $num_items;
    private $xml;
    private $xml_file;
    private $catalog;

    function __construct($catalogo, $id_user) {
        // echo "Constructor";
        $this->xml_file='xmldb/'. $id_user. '_carrito.xml';
        $this->Load();
        // $this->Analyze();
        $this->catalog = $catalogo;

    }

    function Save() {
        $this->xml->asXML($this->xml_file);
    }

    function Load () {
        if (file_exists($this->xml_file)) {
            $this->xml = simplexml_load_file($this->xml_file);
            // echo "Funciona";
        } else {
            $this->xml = new SimpleXMLElement('<carrito></carrito>');
            $this->Save();
        };
    }

    function Add($id_producto, $cantidad) {
        if ($this->catalog->ExistProduct($id_producto)){
            if (!$this->ExistProduct($id_producto)) {
                if ($cantidad <= $this->catalog->CheckStock($id_producto)) {
                    $producto = $this->catalog->GetProduct($id_producto);
                    $newItem = $this->xml->addChild("producto");
                    $newItem->addAttribute("id_producto", $id_producto);
                    $newItem->addChild("nombre", $producto->nombre);
                    $newItem->addChild('precio_item', "");
                    $precios = $newItem->precio_item;
                    foreach ($producto->precio_item->children() as $precio) {
                        $precios->addChild($precio->getName(), $precio);
                    };
                    $precios->addChild("cantidad", $cantidad);
                    $precios->addChild("total", (($producto->precio_item->precio)*$cantidad));
                    $this->catalog->Substract($id_producto, $cantidad);
                    $this->Save();
                    echo "Producto agregado. <br>";
                } else {
                    echo "No hay suficitente stock del producto indicado. <br>";
                }
            } else {
                echo "El producto ya está agregado al carrito. <br>";
            }
            // $item = $this->xml->addChild("producto");
            // $item->addAttribute("id_producto", $id_producto);
            // $item->addChild("precio_item");
            // $item->precio_item->addChild("cantidad", $cantidad);
            // $this->xml->asXML($this->xml_file);
            // echo "Producto agregado";
        } else {
            echo "Producto no existe. <br>";
        }
    }

    function Show() {
        // echo $this->xml->asXML();
        echo "<br><br>";
        echo '<table border = "1">';
        echo '<caption><strong>Carrito actual<strong></caption>';
        echo "<tr>";
            echo "<td><strong>ID_PRODUCTO</strong></td>";
            echo "<td><strong>PRODUCTO</strong></td>";
            echo "<td><strong>PRECIO</strong>(iva incluido)</td>";
            echo "<td><strong>MONEDA</strong></td>";
            echo "<td><strong>CANTIDAD</strong></td>";
            echo "<td><strong>TOTAL</strong></td>";
        echo "</tr>";
        $total = 0;
        foreach ($this->xml->producto as $producto) {
            echo "<tr>";
                echo "<td><strong>" . $producto['id_producto'] . "</strong></td>";
                echo "<td>" . $producto->nombre . "</td>";
                echo "<td>" . $producto->precio_item->precio . "</td>";
                echo "<td>" . $producto->precio_item->moneda . "</td>";
                echo "<td>" . $producto->precio_item->cantidad . "</td>";
                echo "<td>" . $producto->precio_item->total . "</td>";
                $total = $total + $producto->precio_item->total;
            echo "</tr>";
        }
        $this->total = $total;
        echo "<tr></tr>";
        echo "<tr>";
            echo "<td colspan='5'><strong>IMPORTE TOTAL (iva incluido)</strong></td>";
            echo "<td><strong>" . $this->total . "</strong></td>";
        echo "</tr>";
        echo '</table>';
    }

    function Remove ($id_producto) {
        if ($this->ExistProduct($id_producto)) {
            $cantidad = $this->xml->xpath("/carrito/producto[@id_producto='$id_producto']/precio_item/cantidad");
            $this->catalog->AddStock($id_producto, $cantidad[0][0]);
            $ruta2 = $this->xml->xpath("/carrito/producto[@id_producto='$id_producto']");
            unset($ruta2[0][0]);
            echo "Producto eliminado satisfactoriamente. <br>";
            $this->Save();
        } else {
            echo "El producto no existe en el carrito. <br>";
        }
    }

    function Modify($id_producto, $cantidad, $realizar) {
        if ($this->ExistProduct($id_producto)) {
            $stock = $this->catalog->CheckStock($id_producto);
            $productoGet = $this->xml->xpath("/carrito/producto[@id_producto='$id_producto']/precio_item/cantidad");
            $producto = $productoGet[0][0];
            switch ($realizar) {
                case "sumar":
                    $nueva_cantidad = $producto + $cantidad;
                    $stock_restante = $stock - $cantidad;
                    break;
                case "restar":
                    $nueva_cantidad = $producto - $cantidad;
                    $stock_restante = $stock + $cantidad;
                    break;
                case "indicar":
                    $nueva_cantidad = $cantidad;
                    if ($cantidad > $producto) {
                        $diferencia = $cantidad - $producto;
                        $stock_restante = $stock - $diferencia;
                        break;
                    } else if ($cantidad < $producto){
                        $diferencia = $producto - $cantidad;
                        $stock_restante = $stock + $diferencia;
                        break;
                    } else {
                        $stock_restante = $stock;
                        break;
                    }
    
                default:
                    echo "ERROR: Opción invalida. <br>";
                    return;
                        
            }

            if ($nueva_cantidad > $stock) {
                echo "La cantidad deseada excede el stock disponible. <br>";
                return;
            }

            $this->catalog->ModifyStock($id_producto, $stock_restante);

            if ($nueva_cantidad <= 0) {
                $ruta = $this->xml->xpath("/carrito/producto[@id_producto='$id_producto']");
                unset($ruta[0][0]);
                $this->Save();
            } else {
                $ruta1 = $this->xml->xpath("/carrito/producto[@id_producto='$id_producto']/precio_item/cantidad");
                $ruta2 = $this->xml->xpath("/carrito/producto[@id_producto='$id_producto']/precio_item/total");
                $precio_item = $this->catalog->GetPrice($id_producto);
                $ruta1[0][0] = $nueva_cantidad;
                $ruta2[0][0] = $precio_item*$nueva_cantidad;
                $this->xml->asXML($this->xml_file);
            }
        } else {
            echo "El producto no esta añadido al carrito. <br>";
        }
    }

    function Analyze() {
        $this->num_items = count($this->xml->xpath("/carrito/producto"));
        echo $this->num_items;
    }

    function ExistProduct ($id_producto) {
        foreach ($this->xml->xpath("/carrito/producto") as $producto) {
            if ($producto['id_producto'] == $id_producto) {
                return true;
            }
        }
        return false;
    }

}