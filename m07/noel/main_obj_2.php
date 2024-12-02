<?php

// CARRITO BY NOEL MONTOZA SANCHEZ SIN CHATGPT PORQUE SOY PRO

include("com/cart/clsCart_obj.php");
include("com/catalog/clsCatalog_obj.php");
include("com/utils/clsProducto.php");
include("com/utils/clsUser.php");
include("com/utils/clsConnection.php");

session_start();

function login($user_id = "", $username = "", $password = "", $registrar = false) {
    global $catalogo;
    if (isset($_SESSION["isLoged"])) {
        $user_id = $_SESSION["user_id"];
        $username = $_SESSION["username"];
        $password = $_SESSION["password"];
    }

    $usuario = new clsUser($user_id, $username, $password, $registrar);
    if ($usuario->GetLogin()) {

        if (!isset($_SESSION["isLoged"])) {
            $_SESSION["isLoged"] = true;
            $_SESSION["user_id"] = $user_id;
            $_SESSION["username"] = $username;
            $_SESSION["password"] = $password;
        }

        $conexion = new clsConnection($_SESSION["user_id"], $_SESSION["username"]);
        $carrito = new clsCart($catalogo, $_SESSION["user_id"]);
        return [$conexion, $carrito];
    } 
    //   else {
    //     exit;
    // }
}

if (isset($_POST['action'])) {
    $catalogo = new clsCatalog();

    switch ($_POST['action']) {
        case 'login':
            // USER_ID & USERNAME & PASSWORD
            echo "<h1>LOGIN</h1> <br>";
            if (isset($_POST['user_id']) && isset($_POST['username']) && isset($_POST['password'])) {
                login($_POST['user_id'], $_POST['username'], $_POST['password']);
            } else {
                echo "Por favor, indique un user ID, username y contraseña.";
            }
            break;

        case 'register':
            // USER_ID & USERNAME & PASSWORD
            echo "<h1>REGISTRAR NUEVO USUARIO</h1> <br>";
            if (isset($_POST['user_id']) && isset($_POST['username']) && isset($_POST['password'])) {
                login($_POST['user_id'], $_POST['username'], $_POST['password'], true);
            } else {
                echo "Por favor, indique un user ID, username y contraseña.";
            }
            break;

        case 'logout':
            if (isset($_SESSION["isLoged"])) {
                session_destroy();
                echo "Sesión cerrada.";
            } else {
                echo "No hay ningun usuario logeado.";
            }
            break;
    }
}

if (isset($_GET['action'])) {
    $catalogo = new clsCatalog();

    switch ($_GET['action']) {

        case 'add_to_cart':
            // USER_ID & USERNAME & PASSWORD & ID_PRODUCTO & CANTIDAD
            echo "<h1>AÑADIR AL CARRITO</h1> <br>";
            if (isset($_SESSION["isLoged"])) {
                [$conexion, $carrito] = login();
                if ($conexion->GetConectado()) {
                    if (isset($_GET['id_producto'], $_GET['cantidad'])) {
                        $carrito->Add($_GET['id_producto'], $_GET['cantidad']);
                    } else {
                        echo "No se ha indicado un item ID o cantidad.";
                    }
                }
            } else {
                echo "Por favor, inicie sesión primeramente.";
            }
            break;

        case 'remove_from_cart':
            // USERNAME & PASSWORD & ID_PRODUCTO
            echo "<h1>ELIMINAR DEL CARRITO</h1> <br>";
            if (isset($_SESSION["isLoged"])) {
                [$conexion, $carrito] = login();

                if ($conexion->GetConectado()) {
                    if (isset($_GET['id_producto'])) {
                        $carrito->Remove($_GET['id_producto']);
                    } else {
                        echo "No se ha indicado un item ID.";
                    }
                }
            } else {
                echo "Por favor, inicie sesión primeramente.";
            }
            break;

        case 'modify_from_cart':
            // USERNAME & PASSWORD & ID_PRODUCTO & CANTIDAD & REALIZAR (SUMAR, RESTAR, INDICAR)
            echo "<h1>ELIMINAR DEL CARRITO</h1> <br>";
            if (isset($_SESSION["isLoged"])) {
                [$conexion, $carrito] = login();

                if ($conexion->GetConectado()) {
                    if (isset($_GET['id_producto'], $_GET['cantidad'], $_GET['realizar'])) {
                        $carrito->Modify($_GET['id_producto'], $_GET['cantidad'], $_GET['realizar']);
                    } else {
                        echo "No se ha indicado un item ID.";
                    }
                }
            } else {
                echo "Por favor, inicie sesión primeramente.";
            }
            break;

        case 'view_cart':
            // USERNAME & PASSWORD
            echo "<h1>VER CARRITO</h1> <br>";
            if (isset($_SESSION["isLoged"])) {
                [$conexion, $carrito] = login();

                if ($conexion->GetConectado()) {
                    $carrito->Show();
                }
            } else {
                echo "Por favor, inicie sesión primeramente.";
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
    
        case 'add_stock_to_catalog':
            // ID_PRODUCTO & SUMAR
            echo "<h1>AÑADIR STOCK AL CATALOGO</h1> <br>";
            if (isset($_GET['id_producto'], $_GET['sumar'])) {
                $catalogo->AddStock($_GET['id_producto'], $_GET['sumar']);
            } else {
                echo "No se ha indicado un item ID o cantidad a sumar.";
                exit;
            }
            break;
        
        case 'modify_from_catalog':
            // ID_PRODUCTO & STOCK
            echo "<h1>MODIFICAR DEL CATALOGO</h1> <br>";
            if (isset($_GET['id_producto'], $_GET['stock'])) {
                $catalogo->ModifyStock($_GET['id_producto'], $_GET['stock']);
            } else {
                echo "No se ha indicado un item ID o stock a modificar.";
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
} 
// else {
//     echo "Acción no especificada.";
// }

echo "<br><a href='index.html'>Volver al menú principal</a> "
?>