<?php
session_start();

// Definición de stock de ejemplo
$productosDisponibles = array(
    1 => array('nombre' => 'Producto 1', 'precio' => 10, 'stock' => 5),
    2 => array('nombre' => 'Producto 2', 'precio' => 20, 'stock' => 3),
    // Puedes agregar más productos aquí
);

// Inicializar el carrito si no está ya creado
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// Función para agregar productos al carrito
if (isset($_POST['addCart'])) {
    $productoID = $_POST['producto_id'];
    $cantidad = $_POST['cantidad'];

    // Validar si el producto existe y hay suficiente stock
    if (isset($productosDisponibles[$productoID])) {
        $producto = $productosDisponibles[$productoID];
        if ($producto['stock'] >= $cantidad) {
            // Verificar si el producto ya está en el carrito
            if (isset($_SESSION['cart'][$productoID])) {
                $_SESSION['cart'][$productoID]['cantidad'] += $cantidad; // Sumar a la cantidad existente
            } else {
                // Agregar un nuevo producto al carrito
                $_SESSION['cart'][$productoID] = array(
                    'nombre' => $producto['nombre'],
                    'precio' => $producto['precio'],
                    'cantidad' => $cantidad
                );
            }
            echo "Producto agregado al carrito!";
        } else {
            echo "No hay suficiente stock disponible para el producto.";
        }
    } else {
        echo "Producto no válido.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>catálogo de productos</title>
</head>
<body>
    <h1>catálogo de productos</h1>
   
    <form method="POST" action="catalogo.php">
        <div>
            <p><?php echo $productosDisponibles[1]['nombre']; ?> - $<?php echo $productosDisponibles[1]['precio']; ?></p>
            <input type="hidden" name="producto_id" value="1">
            <input type="number" name="cantidad" value="1" min="1">
            <button type="submit" name="addCart">Agregar al carrito</button>
        </div>
    </form>

    <form method="POST" action="catalogo.php">
        <div>
            <p><?php echo $productosDisponibles[2]['nombre']; ?> - $<?php echo $productosDisponibles[2]['precio']; ?></p>
            <input type="hidden" name="producto_id" value="2">
            <input type="number" name="cantidad" value="1" min="1">
            <button type="submit" name="addCart">Agregar al carrito</button>
        </div>
    </form>

    <!-- Enlace para ir al carrito -->
    <h4><a href="cart.php">Ver carrito de compras</a></h4>
    <!-- Enlace para ir al apartado de pago -->
<h4><a href="pago.php">Proceder al Pago</a></h4>

</body>
</html>
