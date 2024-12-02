<?php

// CARRITO BY NOEL MONTOZA SANCHEZ SIN CHATGPT PORQUE SOY PRO

include("com/cart/clsCart.php");
include("com/catalog/clsCatalog.php");
include("com/utils/clsUser.php");
include("com/utils/clsConnection.php");

function login($user_id, $username, $password, $registrar = false) {
    global $catalogo;
    $usuario = new clsUser($user_id, $username, $password, $registrar);
    if ($usuario->GetLogin()) {
        $conexion = new clsConnection($user_id, $username);
        $carrito = new clsCart($catalogo, $user_id);
        return [$conexion, $carrito];
    } else {
        exit;
    }
}

if (isset($_GET['action'])) {
    $catalogo = new clsCatalog();

    switch ($_GET['action']) {
        case 'login':
            // USER_ID & USERNAME & PASSWORD
            echo "<h1>LOGIN</h1> <br>";
            if (isset($_GET['user_id']) && isset($_GET['username']) && isset($_GET['password'])) {
                login($_GET['user_id'], $_GET['username'], $_GET['password']);
            } else {
                echo "Por favor, indique un username y contraseña.";
                exit;
            }
            break;

        case 'register':
            // USER_ID & USERNAME & PASSWORD
            echo "<h1>REGISTRAR NUEVO USUARIO</h1> <br>";
            if (isset($_GET['user_id']) && isset($_GET['username']) && isset($_GET['password'])) {
                login($_GET['user_id'], $_GET['username'], $_GET['password'], true);
            } else {
                echo "Por favor, indique un user ID, username y contraseña.";
                exit;
            }
            break;

        case 'add_to_cart':
            // USER_ID & USERNAME & PASSWORD & ID_PRODUCTO & CANTIDAD
            echo "<h1>AÑADIR AL CARRITO</h1> <br>";
            if (isset($_GET['user_id']) && isset($_GET['username']) && isset($_GET['password'])) {
                [$conexion, $carrito] = login($_GET['user_id'], $_GET['username'],  $_GET['password']);
                if ($conexion->GetConectado()) {
                    if (isset($_GET['id_producto'], $_GET['cantidad'])) {
                        $carrito->Add($_GET['id_producto'], $_GET['cantidad']);
                    } else {
                        echo "No se ha indicado un item ID o cantidad.";
                        exit;
                    }
                }
            } else {
                echo "Por favor, indique un username y contraseña.";
                exit;
            }
            break;

        case 'remove_from_cart':
            // USERNAME & PASSWORD & ID_PRODUCTO
            echo "<h1>ELIMINAR DEL CARRITO</h1> <br>";
            if (isset($_GET['user_id']) && isset($_GET['username']) && isset($_GET['password'])) {
                [$conexion, $carrito] = login($_GET['user_id'], $_GET['username'],  $_GET['password']);

                if ($conexion->GetConectado()) {
                    if (isset($_GET['id_producto'])) {
                        $carrito->Remove($_GET['id_producto']);
                    } else {
                        echo "No se ha indicado un item ID.";
                        exit;
                    }
                }
            } else {
                echo "Por favor, indique un username y contraseña.";
                exit;
            }
            break;

        case 'modify_from_cart':
            // USERNAME & PASSWORD & ID_PRODUCTO & CANTIDAD & REALIZAR (SUMAR, RESTAR, INDICAR)
            echo "<h1>ELIMINAR DEL CARRITO</h1> <br>";
            if (isset($_GET['user_id']) && isset($_GET['username']) && isset($_GET['password'])) {
                [$conexion, $carrito] = login($_GET['user_id'], $_GET['username'],  $_GET['password']);

                if ($conexion->GetConectado()) {
                    if (isset($_GET['id_producto'], $_GET['cantidad'], $_GET['realizar'])) {
                        $carrito->Modify($_GET['id_producto'], $_GET['cantidad'], $_GET['realizar']);
                    } else {
                        echo "No se ha indicado un item ID.";
                        exit;
                    }
                }
            } else {
                echo "Por favor, indique un username y contraseña.";
                exit;
            }
            break;

        case 'view_cart':
            // USERNAME & PASSWORD
            echo "<h1>VER CARRITO</h1> <br>";
            if (isset($_GET['user_id']) && isset($_GET['username']) && isset($_GET['password'])) {
                [$conexion, $carrito] = login($_GET['user_id'], $_GET['username'],  $_GET['password']);

                if ($conexion->GetConectado()) {
                    $carrito->Show();
                }
            } else {
                echo "Por favor, indique un username y contraseña.";
                exit;
            }
            break;

        
        case 'add_to_catalog':
            // ID_PRODUCTO & NOMBRE & PRECIO & MONEDA & STOCK
            echo "<h1>AÑADIR AL CATALOGO</h1> <br>";
            if (isset($_GET['id_producto'], $_GET['nombre'], $_GET['precio'], $_GET['moneda'], $_GET['stock'])) {
                $catalogo->Add($_GET['id_producto'], $_GET['nombre'], $_GET['precio'], $_GET['moneda'], $_GET['stock']);
            } else {
                echo "No se ha indicado algun dato necesario para agregar al catalogo.";
                exit;
            }
            break;
    
        case 'remove_from_catalog':
            // ID_PRODUCTO & RESTAR
            echo "<h1>ELIMINAR DEL CATALOGO</h1> <br>";
            if (isset($_GET['id_producto'], $_GET['restar'])) {
                $catalogo->Substract($_GET['id_producto'], $_GET['restar']);
            } else {
                echo "No se ha indicado un item ID o la cantidad a eliminar.";
                exit;
            }
            break;
    
        case 'modify_from_catalog':
            // ID_PRODUCTO & STOCK
            echo "<h1>MODIFICAR DEL CATALOGO</h1> <br>";
            if (isset($_GET['id_producto'], $_GET['stock'])) {
                $catalogo->ModifyStock($_GET['id_producto'], $_GET['stock']);
            } else {
                echo "No se ha indicado un item ID.";
                exit;
            }
            break;
    
        case 'view_catalog':
            // NADA
            echo "<h1>VER CATALOGO</h1> <br>";
            $catalogo->Show();
            break;

        default:
            echo "Accion invalida!";
    }
} else {
    echo "Acción no especificada.";
}
?>