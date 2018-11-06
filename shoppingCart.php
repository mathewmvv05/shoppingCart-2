<?php

class ShoppingCart
{
    protected $cartId;

    protected $useCookie = false;

    private $items = [];

    public function __construct($options = [])
    {
        if (!session_id()) {
            session_start();
        }

        if (isset($options['useCookie']) && $options['useCookie']) {
            $this->useCookie = true;
        }

        $this->cartId = md5((isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : 'SimpleCart') . '_cart';

        $this->read();
    }

    public function getItems()
    {
        return $this->items;
    }

    public function isEmpty()
    {
        return empty(array_filter($this->items));
    }


    public function getAttributeTotal()
    {
        $total = 0;

        foreach ($this->items as $items) {
            foreach ($items as $item) {
                if (isset($item['price'])) {
                    $total += $item['price'] * $item['quantity'];
                }
            }
        }

        return $total;
    }

    public function clear()
    {
        $this->items = [];
        $this->write();
    }

    public function add($id, $quantity, $price)
    {

        if (isset($this->items[$id])) {
            foreach ($this->items[$id] as $index => $item) {
                $this->items[$id][$index]['quantity'] += $quantity;
                $this->items[$id][$index]['quantity'] = $this->items[$id][$index]['quantity'];

                $this->write();

                return true;
            }
        }

        $this->items[$id][] = [
            'id'         => $id,
            'quantity'   => $quantity,
            'price'      => $price
        ];

        $this->write();

        return true;
    }

    public function remove($id)
    {
        if (!isset($this->items[$id])) {
            return false;
        }

        foreach ($this->items[$id] as $index => $item) {
                if ($this->items[$id][$index]['quantity'] == 1) {
                    unset($this->items[$id][$index]);
                }
                else {
                    $this->items[$id][$index]['quantity'] = $this->items[$id][$index]['quantity'] --;
                }


                $this->write();

                return true;
            }

        return false;
    }

    private function read()
    {
        $this->items = ($this->useCookie) ? json_decode((isset($_COOKIE[$this->cartId])) ? $_COOKIE[$this->cartId] : '[]', true) : json_decode((isset($_SESSION[$this->cartId])) ? $_SESSION[$this->cartId] : '[]', true);
    }

    private function write()
    {
        if ($this->useCookie) {
            setcookie($this->cartId, json_encode(array_filter($this->items)), time() + 604800);
        } else {
            $_SESSION[$this->cartId] = json_encode(array_filter($this->items));
        }
    }
}