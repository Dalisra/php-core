<?php
/**
 * User: vytautas
 * Date: 18.02.14
 * Time: 21:35
 */

class APP_Basket {

   function APP_Basket(){
       if(!isset($_SESSION['basket'])){
           $this->createBasket();
       }
   }

    function getBasket(){
        return $_SESSION['basket'];
    }

    function createBasket(){
        $_SESSION['basket'] = array();
        $_SESSION['basket']['total'] = 0.0;
        $_SESSION['basket']['count'] = 0;
        $_SESSION['basket']['items'] = array();
    }

    function addToBasket($item){
        $id = $item['id'];
        if(isset($item['variant']) && $item['variant'] > 0 && $item['variant'] <= 3){
            $id = $item['id'] . ":" . $item['variant_id'];
        }
        if(isset($_SESSION['basket']['items'][$id])){
            $quantity = $_SESSION['basket']['items'][$id]['quantity'];
            $_SESSION['basket']['items'][$id] = $item;
            $_SESSION['basket']['items'][$id]['quantity'] += $quantity;
        }else{
            $_SESSION['basket']['items'][$id] = $item;
        }
        $_SESSION['basket']['total'] += $item['quantity'] * $item['endPrice'];
        $_SESSION['basket']['count'] += $item['quantity'];
    }

    function removeFromBasket($id){
        if(isset($_SESSION['basket']['items'][$id])){
            $_SESSION['basket']['total'] -= $_SESSION['basket']['items'][$id]['quantity'] * $_SESSION['basket']['items'][$id]['price'];
            $_SESSION['basket']['count'] -= $_SESSION['basket']['items'][$id]['quantity'];
            unset($_SESSION['basket']['items'][$id]);
        }
    }

    function clearBasket(){
        $this->createBasket();
    }
} 