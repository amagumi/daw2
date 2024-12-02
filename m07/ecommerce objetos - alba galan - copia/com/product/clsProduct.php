<?php
class clsProduct
{
    private $idProduct;
    private $prodName;
    private $quantity;
    private $price;


    public function __construct($idProduct, $prodName, $quantity, $price)
    {
        $this->idProduct = $idProduct;
        $this->prodName = $prodName;
        $this->quantity = $quantity;
        $this->price = $price;
    }


    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // getters 

    public function getIdProduct()
    {
        return $this->idProduct;
    }

    public function getProdName()
    {
        return $this->prodName;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function getPrice()
    {
        return $this->price;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // setters 

    public function setIdProduct($idProduct)
    {
        $this->idProduct = $idProduct;
    }

    public function setProdName($prodName)
    {
        $this->prodName = $prodName;
    }

    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    public function setPrice($price)
    {
        if ($price < 0) {
            throw new InvalidArgumentException("El precio no puede ser negativo.");
        }
        $this->price = $price;
    }
}
