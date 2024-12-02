<?php

class clsProducto {
    private $id_producto;
    private $nombre;
    private $stock;
    private $precio;
    private $moneda;
    private $cantidad;
    private $total;

    public function __construct($producto, $isCatalogo = true) {
        if ($isCatalogo) {
            $this->id_producto = $producto['id_producto'];
            $this->nombre = $producto->nombre;
            $this->stock = $producto->stock;
            $this->precio = $producto->precio_item->precio;
            $this->moneda = $producto->precio_item->moneda;
        } else {
            $this->id_producto = $producto['id_producto'];
            $this->nombre = $producto->nombre;
            $this->precio = $producto->precio_item->precio;
            $this->moneda = $producto->precio_item->moneda;
            $this->cantidad = $producto->precio_item->cantidad;
            $this->total = $producto->precio_item->total;
        }
    }

    public function CatalogToCart ($cantidad) {
        $newItem = new SimpleXMLElement('<producto></producto>');
        $newItem->addAttribute("id_producto", $this->id_producto);
        $newItem->addChild("nombre", $this->nombre);
        $newItem->addChild("precio_item", "");
        $newItem->precio_item->addChild("precio", $this->precio);
        $newItem->precio_item->addChild("moneda", $this->moneda);
        $newItem->precio_item->addChild("cantidad", $cantidad);
        $newItem->precio_item->addChild("total", $this->precio * $cantidad);

        $producto = new clsProducto($newItem, false);

        return $producto;
    }
    
    ///////////////////////////////////////////
    /////////// GETTERS
    ///////////////////////////////////////////
    public function GetIdProducto(){
        return $this->id_producto;
    }

    public function GetNombre(){
        return $this->nombre;
    }

    public function GetStock(){
        return $this->stock;
    }

    public function GetPrecio(){
        return $this->precio;
    }

    public function GetMoneda(){
        return $this->moneda;
    }


    public function GetCantidad(){
        return $this->cantidad;
    }

    public function GetTotal(){
        return $this->total;
    }

    ///////////////////////////////////////////
    /////////// SETTERS
    ///////////////////////////////////////////
    public function SetIdProducto($id_producto): self{
        $this->id_producto = $id_producto;

        return $this;
    }

    public function SetNombre($nombre): self{
        $this->nombre = $nombre;

        return $this;
    }

    public function SetStock($stock): self{
        if ($stock < 0) {
            $stock = 0;
        }
        $this->stock = $stock;

        return $this;
    }

    public function SetPrecio($precio): self{
        $this->precio = $precio;

        return $this;
    }

    public function SetMoneda($moneda): self
    {
        $this->moneda = $moneda;

        return $this;
    }

    public function SetCantidad($cantidad): self{
        $this->cantidad = $cantidad;

        return $this;
    }

    public function SetTotal($total): self{
        $this->total = $total;

        return $this;
    }
}

?>