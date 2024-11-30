<?php
session_start();

// Comprobar si hay productos en el carrito
if (empty($_SESSION['cart'])) {
    header('Location: catalogo.php'); // Redirigir a catálogo si el carrito está vacío
    exit();
}

// Cálculo de totales y subtotales
$subtotal = 0;
$impuesto = 0.10; // 10% de impuesto
$totalConImpuesto = 0;

foreach ($_SESSION['cart'] as $productoID => $producto) {
    $subtotal += $producto['precio'] * $producto['cantidad'];
}
$totalConImpuesto = $subtotal + ($subtotal * $impuesto);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagar</title>
</head>
<body>
    <h1>Resumen de Compra</h1>
    <table>
        <tr>
            <th>Producto</th>
            <th>Precio</th>
            <th>Cantidad</th>
            <th>Total</th>
        </tr>
        <?php foreach ($_SESSION['cart'] as $productoID => $producto): ?>
        <tr>
            <td><?php echo $producto['nombre']; ?></td>
            <td><?php echo $producto['precio']; ?></td>
            <td><?php echo $producto['cantidad']; ?></td>
            <td><?php echo $producto['precio'] * $producto['cantidad']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h3>Resumen del Carrito</h3>
    <p>Subtotal: $<?php echo $subtotal; ?></p>
    <p>Impuesto (10%): $<?php echo $subtotal * $impuesto; ?></p>
    <p>Total con Impuesto: $<?php echo $totalConImpuesto; ?></p>

    <h2>Información de Pago</h2>
    <form method="POST" action="procesar_pago.php">
        <div>
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required>
        </div>
        <div>
            <label for="direccion">Dirección:</label>
            <input type="text" id="direccion" name="direccion" required>
        </div>
        <div>
            <label for="metodo_pago">Método de Pago:</label>
            <select id="metodo_pago" name="metodo_pago" required>
                <option value="tarjeta_credito">Tarjeta de Crédito</option>
                <option value="paypal">PayPal</option>
                <option value="transferencia">Transferencia Bancaria</option>
            </select>
        </div>
        <button type="submit">Realizar Pago</button>
    </form>

    <h4><a href="cart.php">Volver al carrito</a></h4>
</body>
</html>
