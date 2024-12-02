<?php

class clsCart {

    private $productos = [];
    private int $cartTotal;
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

    public function Save() {
        $guardar = new SimpleXMLElement('<carrito></carrito>');
        foreach ($this->productos as $producto) {
            $item = $guardar->addChild('producto');
            $item->addAttribute('id_producto', $producto->GetIdProducto());
            $item->addChild('nombre', $producto->GetNombre());
            $item->addChild('precio_item');
            $item->precio_item->addChild('precio', $producto->GetPrecio());
            $item->precio_item->addChild('moneda', $producto->GetMoneda());
            $item->precio_item->addChild('cantidad', $producto->GetCantidad());
            $item->precio_item->addChild('total', $producto->GetTotal());
        }
        $guardar->asXML($this->xml_file);
    }

    public function Load () {
        if (file_exists($this->xml_file)) {
            $this->xml = simplexml_load_file($this->xml_file);
            // echo "Funciona";
        } else {
            $this->xml = new SimpleXMLElement('<carrito></carrito>');
            $this->xml->asXML($this->xml_file);
        };
        $this->productos = [];
        foreach ($this->xml->xpath("/carrito/producto") as $producto) {
            $item = new clsProducto($producto, false);
            array_push($this->productos, $item);
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
        foreach ($this->productos as $producto) {
            echo "<tr>";
                echo "<td><strong>" . $producto->GetIdProducto() . "</strong></td>";
                echo "<td>" . $producto->GetNombre() . "</td>";
                echo "<td>" . $producto->GetPrecio() . "</td>";
                echo "<td>" . $producto->GetMoneda() . "</td>";
                echo "<td>" . $producto->GetCantidad() . "</td>";
                echo "<td>" . $producto->GetTotal() . "</td>";
            echo "</tr>";
        }
        $this->CalculateCartTotal();
        echo "<tr></tr>";
        echo "<tr>";
            echo "<td colspan='5'><strong>IMPORTE TOTAL (iva incluido)</strong></td>";
            echo "<td><strong>" . $this->cartTotal . "</strong></td>";
        echo "</tr>";
        echo '</table>';
    }

    function Add($id_producto, $cantidad) {
        if ($this->catalog->ExistProduct($id_producto)){
            if (!$this->ExistProduct($id_producto)) {
                if ($cantidad <= $this->catalog->CheckStock($id_producto)) {
                    $producto = $this->catalog->CatalogToCart($id_producto, $cantidad);
                    array_push($this->productos, $producto);
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



    function Remove ($id_producto) {
        if ($this->ExistProduct($id_producto)) {
            $producto = $this->GetProduct($id_producto);
            $cantidad = $producto->GetCantidad();
            $this->catalog->AddStock($id_producto, $cantidad);
            $indice = $this->GetProductIndex($id_producto);
            unset($this->productos[$indice]);
            echo "Producto eliminado satisfactoriamente. <br>";
            $this->Save();
        } else {
            echo "El producto no existe en el carrito. <br>";
        }
    }

    function Modify($id_producto, $cantidad, $realizar) {
        if ($this->ExistProduct($id_producto)) {
            $stock = $this->catalog->CheckStock($id_producto);
            $producto = $this->GetProduct($id_producto);
            switch ($realizar) {
                case "sumar":
                    $nueva_cantidad = $producto->GetCantidad() + $cantidad;
                    $stock_restante = $stock - $cantidad;
                    break;
                case "restar":
                    $nueva_cantidad = $producto->GetCantidad() - $cantidad;
                    $stock_restante = $stock + $cantidad;
                    break;
                case "indicar":
                    $nueva_cantidad = $cantidad;
                    if ($cantidad > $producto->GetCantidad()) {
                        $diferencia = $cantidad - $producto->GetCantidad();
                        $stock_restante = $stock - $diferencia;
                        break;
                    } else if ($cantidad < $producto->GetCantidad()){
                        $diferencia = $producto->GetCantidad() - $cantidad;
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
                $this->Remove($id_producto);
                $this->Save();
            } else {
                $indice = $this->GetProductIndex($id_producto);
                $precio_item = $this->catalog->GetPrice($id_producto);
                $this->productos[$indice]->SetCantidad($nueva_cantidad);
                $this->productos[$indice]->SetTotal($precio_item*$nueva_cantidad);
                $this->Save();
            }
        } else {
            echo "El producto no esta añadido al carrito. <br>";
        }
    }

    public function CalculateCartTotal () {
        $total = 0;
        foreach ($this->productos as $producto) {
            $total = $total + $producto->GetTotal();
        }
        $this->cartTotal = $total;
    }

    public function GetProduct($id_producto): mixed{
        foreach ($this->productos as $producto) {
            if ($producto->GetIdProducto() == $id_producto) {
                return $producto;
            }
        }
        return false;
    }

    public function GetProductIndex($id_producto): mixed{
        foreach ($this->productos as $index => $producto) {
            if ($producto->GetIdProducto() == $id_producto) {
                return $index;
            }
        }
        return false;
    }
    public function Analyze() {
        $this->num_items = count($this->xml->xpath("/carrito/producto"));
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