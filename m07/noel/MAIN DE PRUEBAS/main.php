<?php

// CARRITO BY NOEL MONTOZA SANCHEZ SIN CHATGPT PORQUE SOY PRO

include_once("com/cart/cart.php");
//include_once("com/utils/register.php"); // AL FINAL NO HE USADO ESTE ARCHIVO
include_once("com/catalog/catalog.php");
include_once("com/utils/users.php");

if (isset($_GET['action'])) {
    $action = $_GET['action'];

    switch ($action) {
        case 'login':
            // USERNAME & PASSWORD
            echo "<h1>LOGIN</h1> <br>";
            if (isset($_GET['username']) && isset($_GET['password'])) {
                loginUser($_GET['username'], $_GET['password']);
            } else {
                echo "Por favor, indique un username y contraseña.";
            }
            break;

        case 'register':
            // USERNAME & PASSWORD & USER_ID
            echo "<h1>REGISTRAR NUEVO USUARIO</h1> <br>";
            if (isset($_GET['username']) && isset($_GET['password']) && isset($_GET['user_id'])) {
                registerNewUser($_GET['username'], $_GET['password'], $_GET['user_id']);
            } else {
                echo "Por favor, indique un username, contraseña y user ID.";
            }
            break;

        case 'add_to_cart':
            // USERNAME & PASSWORD & ID_PRODUCTO & CANTIDAD
            echo "<h1>AÑADIR AL CARRITO</h1> <br>";
            if (isset($_GET['username']) && isset($_GET['password'])) {
                list($login, $user_id) = loginUser($_GET['username'], $_GET['password']);
                if ($login) {
                    if (isset($user_id, $_GET['id_producto'], $_GET['cantidad'])) {
                        AddToCart($user_id,$_GET['id_producto'], $_GET['cantidad']);
                    } else {
                        echo "No se ha indicado un item ID o cantidad.";
                    }
                }
            } else {
                echo "Por favor, indique un username y contraseña.";
            }
            break;

        case 'remove_from_cart':
            // USERNAME & PASSWORD & ID_PRODUCTO
            echo "<h1>ELIMINAR DEL CARRITO</h1> <br>";
            if (isset($_GET['username']) && isset($_GET['password'])) {
                list($login, $user_id) = loginUser($_GET['username'], $_GET['password']);
                if ($login) {
                    if (isset($user_id, $_GET['id_producto'])) {
                        RemoveFromCart($user_id,$_GET['id_producto']);
                    } else {
                        echo "No se ha indicado un item ID.";
                    }
                }
            } else {
                echo "Por favor, indique un username y contraseña.";
            }
            break;

        case 'modify_from_cart':
            // USERNAME & PASSWORD & ID_PRODUCTO & CANTIDAD & REALIZAR (SUMAR, RESTAR, INDICAR)
            echo "<h1>ELIMINAR DEL CARRITO</h1> <br>";
            if (isset($_GET['username']) && isset($_GET['password'])) {
                list($login, $user_id) = loginUser($_GET['username'], $_GET['password']);
                if ($login) {
                    if (isset($user_id, $_GET['id_producto'], $_GET['cantidad'], $_GET['realizar'])) {
                        ModifFromCart($user_id,$_GET['id_producto'], $_GET['cantidad'], $_GET['realizar']);
                    } else {
                        echo "No se ha indicado un item ID.";
                    }
                }
            } else {
                echo "Por favor, indique un username y contraseña.";
            }
            break;

        case 'view_cart':
            // USERNAME & PASSWORD
            echo "<h1>VER CARRITO</h1> <br>";
            if (isset($_GET['username']) && isset($_GET['password'])) {
                list($login, $user_id) = loginUser($_GET['username'], $_GET['password']);
                if ($login) {
                    if (isset($user_id)) {
                        viewCart($user_id);
                    } else {
                        echo "No se ha encontrado el carrito del usuario.";
                    }
                }
            } else {
                echo "Por favor, indique un username y contraseña.";
            }
            break;

        
        case 'add_to_catalog':
            // ID_PRODUCTO & NOMBRE & PRECIO & MONEDA & STOCK
            echo "<h1>AÑADIR AL CATALOGO</h1> <br>";
            if (isset($_GET['id_producto'], $_GET['nombre'], $_GET['precio'], $_GET['moneda'], $_GET['stock'])) {
                AddToCatalog($_GET['id_producto'], $_GET['nombre'], $_GET['precio'], $_GET['moneda'], $_GET['stock']);
            } else {
                echo "No se ha indicado algun dato necesario para agregar al catalogo.";
            }
            break;
    
        case 'remove_from_catalog':
            // ID_PRODUCTO & RESTAR
            echo "<h1>ELIMINAR DEL CATALOGO</h1> <br>";
            if (isset($_GET['id_producto'], $_GET['restar'])) {
                SubstractCatalog($_GET['id_producto'], $_GET['restar']);
            } else {
                echo "No se ha indicado un item ID o la cantidad a eliminar.";
            }
            break;
    
        case 'modify_from_catalog':
            // ID_PRODUCTO & STOCK
            echo "<h1>MODIFICAR DEL CATALOGO</h1> <br>";
            if (isset($_GET['id_producto'], $_GET['stock'])) {
                ModifStockFromCatalog($_GET['id_producto'], $_GET['stock']);
            } else {
                echo "No se ha indicado un item ID.";
            }
            break;
    
        case 'view_catalog':
            // NADA
            echo "<h1>VER CATALOGO</h1> <br>";
            viewCatalog();
            break;

        default:
            echo "Accion invalida!";
    }
} else {
    echo "Acción no especificada.";
}
?>