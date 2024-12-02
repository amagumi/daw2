<?php

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function viewCart ($user_id) {
    $carrito = GetCart('xmldb/'. $user_id. '_carrito.xml');
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
    foreach ($carrito->producto as $producto) {
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
    echo "<tr></tr>";
    echo "<tr>";
        echo "<td colspan='5'><strong>IMPORTE TOTAL (iva incluido)</strong></td>";
        echo "<td><strong>" . $total . "</strong></td>";
    echo "</tr>";
    echo '</table>';
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function AddToCart($user_id, $id_producto, $cantidad) {
    // echo "AddToCart <br>";
    // echo $user_id . $id_producto . $cantidad;
    $cart_file = 'xmldb/'. $user_id. '_carrito.xml';
    $catalog_file = 'xmldb/catalogo.xml';

    if (ExistProduct(GetCatalog($catalog_file), $id_producto)){
        _ExecuteAddToCart($cart_file, $id_producto, $cantidad);
    } else {
        echo "No hay suficiente producto";
    };

};

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function _ExecuteAddToCart ($cart_file, $id_producto, $cantidad) {
    $carrito = GetCart($cart_file);
    $producto = GetProduct($id_producto);
    if (GetProductCart($carrito, $id_producto)) {
        echo "El producto ya esta agregado en el carrito, puede modificar la cantidad.";
        exit;
    }
    //echo $producto->asXML();
    if ((int)$cantidad > (int)$producto->stock) {
        echo "No hay suficiente producto.";
    } else {
        $agregar = $carrito->addChild('producto');
        $agregar->addAttribute('id_producto', $id_producto);
        $agregar->addChild('nombre', $producto->nombre);
        $agregar->addChild('precio_item', "");
        // foreach ($producto->children() as $dato) {
        //     // echo $dato;+
        //     if ((string)$dato->getName() == "stock") {
        //         echo "";
        //     } else {
        //         $agregar->addChild($dato->getName(), $dato);
        //     };
        // };
        // $carrito->asXML('xmldb/prueba.xml');
        $precios = $agregar->precio_item;
        foreach ($producto->precio_item->children() as $precio) {
            $precios->addChild($precio->getName(), $precio);
        };
        $precios->addChild("cantidad", $cantidad);
        $precios->addChild("total", (($producto->precio_item->precio)*$cantidad));
        
        echo "El producto se ha agregado correctamente.";
    };

    // $agregar->addAttribute('id_producto', $id_producto);
    // $agregar->addChild('id_producto', $id_producto);
    // $agregar->addChild('cantidad', $cantidad);

    // $precios = $agregar->addChild('precio_item');
    // $precios->addChild('precio', $precio);
    // $precios->addChild('moneda', $moneda);

    $carrito->asXML($cart_file);
};

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function RemoveFromCart($user_id, $id_producto) {
    $cart_file = 'xmldb/'. $user_id. '_carrito.xml';
    $carrito = GetCart($cart_file);
    $ruta = $carrito->xpath("/carrito/producto[@id_producto='$id_producto']");  // Corregido el XPath
    if (!empty($ruta)) {
        unset($ruta[0][0]);
        echo "Producto eliminado satisfactoriamente.";
    } else {
        echo "El producto no existe en el carrito.";
    }

    $carrito->asXML($cart_file);

};

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function ModifFromCart($user_id, $id_producto, $cantidad, $realizar) {
    $cart_file = 'xmldb/'. $user_id. '_carrito.xml';
    $stock = CheckStock($id_producto);
    $carrito = GetCart($cart_file);
    $ruta = $carrito->xpath("/carrito/producto[@id_producto='$id_producto']");  // Corregido el XPath
    if (!empty($ruta)) {
        $producto = GetProductCart($carrito, $id_producto);
        switch ($realizar) {
            case "sumar":
                $nueva_cantidad = $producto->precio_item->cantidad + $cantidad;
                $stock_restante = $stock - $cantidad;
                break;
            case "restar":
                $nueva_cantidad = $producto->precio_item->cantidad - $cantidad;
                $stock_restante = $stock + $cantidad;
                break;
            case "indicar":
                $nueva_cantidad = $cantidad;
                if ($cantidad > $producto->precio_item->cantidad) {
                    $diferencia = $cantidad - $producto->precio_item->cantidad;
                    $stock_restante = $stock - $diferencia;
                    break;
                } else if ($cantidad < $producto->precio_item->cantidad){
                    $diferencia = $producto->precio_item->cantidad - $cantidad;
                    $stock_restante = $stock + $diferencia;
                    break;
                } else {
                    $stock_restante = $stock;
                    break;
                }

            default:
                echo "ERROR: No se ha podido realizar la nueva asignación.";
                return;
                    
            }
        if ($nueva_cantidad > $stock) {
            echo "La cantidad deseada excede el stock disponible";
            return;
        }
        ModifStock($id_producto, $stock_restante);
        //echo "entra 2";
        //echo $nueva_cantidad;
        if ($nueva_cantidad <= 0) {
            unset($ruta[0][0]);
        } else {
            $ruta1 = $carrito->xpath("/carrito/producto[@id_producto='$id_producto']/precio_item/cantidad");
            $ruta2 = $carrito->xpath("/carrito/producto[@id_producto='$id_producto']/precio_item/total");
            $ruta1[0][0] = $nueva_cantidad;
            $ruta2[0][0] = (($producto->precio_item->precio)*$nueva_cantidad);
        }
    } else {
        echo "El producto no esta añadido al carrito.";
    }

    $carrito->asXML($cart_file);

};
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function GetCart ($cart_file) {
    if (file_exists($cart_file)) {
        $carrito = simplexml_load_file($cart_file);
        // echo "Funciona";
    } else {
        $carrito = new SimpleXMLElement('<carrito></carrito>');
    };
    $carrito->asXML($cart_file);
    return $carrito;
};

function GetProductCart ($carrito, $id_producto) {
    foreach ($carrito->producto as $producto){
        if ($producto['id_producto'] == $id_producto) {
            // echo $producto->asXML();
            return $producto;
        };
    };
    return false;
}
?>