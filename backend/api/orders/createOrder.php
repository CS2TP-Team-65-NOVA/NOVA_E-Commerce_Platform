<?php

session_start();

  if(!isset($_SESSION['login_id'])) {
    header("Location: login.php");
    exit();
  } 

  require_once('connectdb.php');
  $logged_id=$_SESSION['login_id'];

try {

    $db->beginTransaction();

    $query = $db->prepare("SELECT basket_id FROM basket WHERE user_id = ?");
    $query->execute([$logged_id]);

    $user_basket = $query->fetch(PDO::FETCH_ASSOC);
   
    if(!$user_basket) {

      throw new Exception("Basket Unknown");

    }

    $basket_contents = $db -> prepare ("SELECT basket_items.size_id, basket_items.quantity, basket_items.unit_price, product_versions.discount_percentage
                                        FROM basket_items JOIN product_versions ON basket_items.size_id = product_versions.size_id
                                        WHERE basket_items.basket_id = ? ");

    $basket_contents->execute([$user_basket['basket_id']]);
    $b_items = $basket_contents->fetchAll(PDO::FETCH_ASSOC); 

    if (empty($b_items)) {
      
      throw new Exception("Your Basket Is Empty");

    }

    foreach ($b_items as $im) {

      if(!is_numeric($im['quantity'])) {

        throw new Exception("Incompatible Quantity Format");

      }

      if($im['quantity'] <= 0) {

        throw new Exception("Incompatible Quantity Detected");

      }

      if(!is_numeric($im['unit_price'])) {

        throw new Exception("Incompatible Price Format");

      }

      if($im['unit_price'] <= 0) {

        throw new Exception("Incompatible Price Detected");

      }

      if(!is_numeric($im['size_id']) || $im['size_id'] <= 0) {

        throw new Exception("Incompatible Product Detected");

      }
    }

      foreach ($b_items as $im) {

        $stockquery =$db->prepare("SELECT stock_qty FROM inventory WHERE size_id = ?");

        $stockquery->execute([$im['size_id']]);
        $available_stock = $stockquery->fetch(PDO::FETCH_ASSOC); 

        if ($available_stock === false) {

          throw new Exception("Invalid Product");

        }

        $as = $available_stock['stock_qty'];

        if(!is_numeric($as)) {

        throw new Exception("Invalid Stock Format");

        }

        if ((int) $im['quantity'] > (int)$as) {

          throw new Exception("Not Enough Stock");

        }
      }

      $item_total = 0;
      $discount_total = 0;

      foreach ($b_items as $im) {

        $item_total += $im['unit_price'] * $im['quantity'];

        if(!empty($im['discount_percentage']) && is_numeric($im['discount_percentage'])) {
        
          if($im['discount_percentage'] > 0 && $im['discount_percentage'] < 80)  {

            $discount_each_item = $im['unit_price'] * $im['discount_percentage'] / 100;
            $discount_total += $discount_each_item * $im['quantity'];
          
          } else {
              throw new Exception("Unsupported Discount Amount");
          }
      }
    }
            
      $shipping_total = 0;

      if ($item_total < 50 ) {

          $shipping_total += 5.00;

      }

        $final_total = $item_total + $shipping_total - $discount_total;

        if($final_total < 0) {

          throw new Exception("Total Amount Must Be A Positive Number");

        }


    $create_order = $db->prepare("INSERT INTO orders (user_id, order_number, payment_status, delivery_status, currency, total_amount, shipping_amount, shipping_address, discount_amount, notes) 
    VALUES (?, ?, ?, ? , ?, ?, ?, ?, ?, ?)"); 
    
     if(empty($_POST['shipping_address'])) {

          throw new Exception("Enter A Shipping Address");

     }

    if(empty($_POST['notes'])) {

      $order_notes = null;

    } else {

       $order_notes = $_POST['notes'];

    }

    $order_num = 'ON' . bin2hex(random_bytes(8));
    $currency_type = "GBP";
    $ship_address = $_POST['shipping_address'];
    $pay_status = 'pending';
    $deliver_status = 'processing';

    $create_order->execute(array($logged_id, $order_num, $pay_status, $deliver_status, $currency_type, $final_total, $shipping_total, $ship_address, $discount_total, $order_notes));
    $id_order = $db -> lastInsertId(); 

    $create_order_items = $db->prepare("INSERT INTO order_items (order_id, size_id, quantity, price, line_total)
    VALUES (?, ?, ?, ?, ?)");

    $line_item_total = 0;
   
      foreach ($b_items as $im) {

        $version_id = $im['size_id'];
        $qty = $im['quantity'];
        $cost = $im['unit_price'];
        $line_item_total = $qty * $cost;
        
        $create_order_items->execute(array($id_order, $version_id, $qty, $cost, $line_item_total));

    }

    $payment_pending_status = $db->prepare("INSERT INTO payments (order_id, payment_method, currency, total_amount, transaction_reference, payment_status)
    VALUES (?, ?, ?, ?, ?, ?)");

    $pay_method = "Card";
    $transaction_ref =  null;

    $payment_pending_status->execute(array($id_order, $pay_method, $currency_type, $final_total, $transaction_ref, 'pending'));

    $remove_user_basket_items = $db->prepare("DELETE FROM basket_items WHERE basket_id =?");

    $basket_to_remove = $user_basket['basket_id'];

    $remove_user_basket_items->execute(array($basket_to_remove));
    $remove_user_basket = $db->prepare("DELETE FROM basket WHERE basket_id =?");
    $remove_user_basket->execute(array($basket_to_remove));

    $db->commit();

    header("Location: pay.php?order_id=" . htmlspecialchars($id_order));
    exit();
    
  } catch(Exception $problem) {
    $prnt = $problem  -> getMessage();

    echo htmlspecialchars($prnt); ////
    $db->rollback();

  }

 ?> 