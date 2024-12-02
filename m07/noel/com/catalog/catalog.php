<?php

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function viewCatalog () {
    $catalogo = GetCatalog('xmldb/catalogo.xml');
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
    foreach ($catalogo->producto as $producto) {
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

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function AddToCatalog($id_producto, $nombre, $precio, $moneda, $stock) {
    // echo "AddToCatalog <br>";
    // echo $id_producto . $nombre . $precio . $moneda . $stock;
    $catalog_file = 'xmldb/catalogo.xml';
    $catalogo = GetCatalog($catalog_file);
    
    if (!ExistProduct($catalogo, $id_producto)){
        _ExecuteAddToCatalog($catalogo, $catalog_file, $id_producto, $nombre, $precio, $moneda, $stock);
    } else {
        echo "Ya existe un producto con el ID indicado.";
    };
    
};

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function _ExecuteAddToCatalog ($catalogo, $catalog_file, $id_producto, $nombre, $precio, $moneda, $stock) {    
    $agregar = $catalogo->addChild('producto');
        
    $agregar->addAttribute('id_producto', $id_producto);
    $agregar->addChild('nombre', $nombre);
    $agregar->addChild('stock', $stock);
        
    $precios = $agregar->addChild('precio_item');
    $precios->addChild('precio', $precio * 1.21);
    $precios->addChild('moneda', $moneda);
        
    echo "Producto añadido al catalogo exitosamente.";
    $catalogo->asXML($catalog_file);
    
};

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function SubstractCatalog($id_producto, $restar) {
    $catalogo = GetCatalog('xmldb/catalog.xml');
    $ruta = $catalogo->xpath("/catalogo/producto[@id_producto='$id_producto']");  // Corregido el XPath
    if (!empty($ruta)) {
        $producto = GetProductCart($catalogo, $id_producto);
        $nueva_cantidad = $producto->stock - $restar;
        if ($nueva_cantidad < 0) {
            $nueva_cantidad = 0;
        };
        //echo $nueva_cantidad;
        $ruta1 = $producto->xpath("/catalogo/producto[@id_producto='$id_producto']/stock");
        $ruta1[0][0] = $nueva_cantidad;
        // unset($ruta1[0][0]);
        // $producto->addChild("stock", $nueva_cantidad);

    };

    $catalogo->asXML('xmldb/catalogo.xml');

};

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function ModifStockFromCatalog($id_producto, $stock) {
    $catalogo = GetCatalog('xmldb/catalog.xml');
    $ruta = $catalogo->xpath("/catalogo/producto[@id_producto='$id_producto']");  // Corregido el XPath
    if (!empty($ruta)) {
        $producto = GetProductCart($catalogo, $id_producto);
        if ($stock < 0) {
            $stock = 0;
        };
        //echo $nueva_cantidad;
        $ruta1 = $producto->xpath("/catalogo/producto[@id_producto='$id_producto']/stock");
        $ruta1[0][0] = $stock;
        // unset($ruta1[0][0]);
        // $producto->addChild("stock", $stock);

    };

    $catalogo->asXML('xmldb/catalogo.xml');

};
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function ExistProduct($catalogo, $id_producto) {
    $existe = false;
    foreach ($catalogo->producto as $producto){
        if ($producto['id_producto'] == $id_producto) {
            $existe = true;
            // echo $producto->asXML();
            break;
        };
    };

    return $existe;
};

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function GetCatalog ($catalog_file) {
    if (file_exists($catalog_file)) {
        $catalogo = simplexml_load_file($catalog_file);
        // echo "El fichero existe";
    } else {
        $catalogo = new SimpleXMLElement('<catalogo></catalogo>');
    };
    $catalogo->asXML($catalog_file);
    return $catalogo;
};

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function GetProduct ($id_producto) {
    $catalogo = GetCatalog("xmldb/catalogo.xml");
    foreach ($catalogo->producto as $producto){
        if ($producto['id_producto'] == $id_producto) {
            // echo $producto->asXML();
            return $producto;
        };
    };
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function CheckStock ($id_producto) {
    $stock = 0;
    $producto = GetProduct($id_producto);
    $stock = $producto->stock;
    //echo $stock->asXML();

    return $stock;
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function ModifStock ($id_producto, $nuevo_stock) {
    $verificacion = false;
    $catalogo = GetCatalog("xmldb/catalogo.xml");
    $ruta = $catalogo->xpath("/catalogo/producto[@id_producto='$id_producto']/stock");
    
    $ruta[0][0] = $nuevo_stock;
    if ($ruta[0][0] == $nuevo_stock) {
        echo "Correcto";
        $verificacion = true;

    }
    //echo $stock->asXML();
    $catalogo->asXML("xmldb/catalogo.xml");
    return $verificacion;
}
?>