<?php
session_start();

// Definición de stock de ejemplo para referencia
$productosDisponibles = array(
    1 => array('nombre' => 'Producto 1', 'precio' => 10, 'stock' => 5),
    2 => array('nombre' => 'Producto 2', 'precio' => 20, 'stock' => 3),
    // Puedes agregar más productos aquí
);

// Función para eliminar un producto del carrito
if (isset($_POST['eliminar'])) {
    $productoID = $_POST['producto_id'];
    unset($_SESSION['cart'][$productoID]); // Eliminar producto del carrito
    echo "Producto eliminado del carrito.";
}

// Función para actualizar las cantidades de los productos en el carrito
if (isset($_POST['actualizar'])) {
    $productoID = $_POST['producto_id'];
    $nuevaCantidad = $_POST['nueva_cantidad'];
    
    // Validar si la nueva cantidad es válida
    if ($nuevaCantidad > 0) {
        // Validar si hay suficiente stock
        if ($productosDisponibles[$productoID]['stock'] >= $nuevaCantidad) {
            $_SESSION['cart'][$productoID]['cantidad'] = $nuevaCantidad;
            echo "Cantidad actualizada.";
        } else {
            echo "No hay suficiente stock para actualizar.";
        }
    } else {
        unset($_SESSION['cart'][$productoID]); // Si la cantidad es 0, eliminar el producto
        echo "Producto eliminado del carrito porque la cantidad era 0.";
    }
}

// Función para vaciar el carrito
if (isset($_POST['vaciar_cart'])) {
    $_SESSION['cart'] = array(); // Vaciar todo el carrito
    echo "Carrito vaciado.";
}

// Cálculo de totales y subtotales
$subtotal = 0;
$impuesto = 0.10; // 10% de impuesto
$totalConImpuesto = 0;

if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $productoID => $producto) {
        $subtotal += $producto['precio'] * $producto['cantidad'];
    }
    $totalConImpuesto = $subtotal + ($subtotal * $impuesto);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>
</head>
<body>
    <h1>Carrito de Compras</h1>
    <?php if (!empty($_SESSION['cart'])): ?>
        <table border="1">
            <tr>
                <th>Producto</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Total</th>
                <th>Acciones</th>
            </tr>
            <?php foreach ($_SESSION['cart'] as $productoID => $producto): ?>
            <tr>
                <td><?php echo $producto['nombre']; ?></td>
                <td><?php echo $producto['precio']; ?></td>
                <td>
                    <form method="POST" action="cart.php">
                        <input type="hidden" name="producto_id" value="<?php echo $productoID; ?>">
                        <input type="number" name="nueva_cantidad" value="<?php echo $producto['cantidad']; ?>" min="1">
                        <button type="submit" name="actualizar">Actualizar</button>
                    </form>
                </td>
                <td><?php echo $producto['precio'] * $producto['cantidad']; ?></td>
                <td>
                    <form method="POST" action="cart.php">
                        <input type="hidden" name="producto_id" value="<?php echo $productoID; ?>">
                        <button type="submit" name="eliminar">Eliminar</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

        <h3>Resumen del Carrito</h3>
        <p>Subtotal: $<?php echo $subtotal; ?></p>
        <p>Impuesto (10%): $<?php echo $subtotal * $impuesto; ?></p>
        <p>Total con Impuesto: $<?php echo $totalConImpuesto; ?></p>

        <form method="POST" action="cart.php">
            <button type="submit" name="vaciar_cart">Vaciar Carrito</button>
        </form>

        <!-- Enlace para volver al catálogo -->
        <h4><a href="catalogo.php">Volver al catálogo de productos</a></h4>
    <?php else: ?>
        <p>El carrito está vacío.</p>
        <h4><a href="catalogo.php">Volver al catálogo de productos</a></h4>
    <?php endif; ?>
    <!-- Enlace para ir al apartado de pago -->
<h4><a href="pago.php">Proceder al Pago</a></h4>

</body>
</html>