  <?php

  session_start();

  if(!isset($_SESSION['login_id'])) {

    header("Location: login.php");

    exit();
  } 

  require_once('connectdb.php');

  $logged_id=$_SESSION['login_id'];

  if(!isset($_GET['order_id'])) {

      die('Order ID Not Found');
        
    }

    $logged_order = $_GET['order_id'];

    $query = $db->prepare("SELECT * FROM orders WHERE order_id = ? AND user_id = ? AND payment_status = ?");

    $current_pay_status = 'pending';

    $query->execute(array($logged_order, $logged_id, $current_pay_status));

    $pending_order = $query->fetch(PDO::FETCH_ASSOC);

    if($pending_order === false) {

        die('Order Invalid');

    }

  $pay_submitted = ($_SERVER['REQUEST_METHOD'] == 'POST');
  $issues= array();
  $card_number= trim($_POST['number'] ?? '');
  $card_holder= trim($_POST['holder'] ?? '');
  $cvv= trim($_POST['cv'] ?? '');
  $expiry_date= trim($_POST['expiry'] ?? '');

  if($pay_submitted) {

  if($card_number ==='') {
    $issues[] = "Enter Card Number";

   } elseif (!ctype_digit($card_number)) {
    $issues[] = "Enter A Valid Card Number Format";

  } else if (strlen($card_number)< 12 || strlen($card_number)> 19) {
    $issues[] = "Enter A Valid Card Number";

  }

  if($card_holder ==='') {
    $issues[] = "Enter Card Holder Name";

    } elseif (!preg_match('/^[a-zA-Z ]+$/', $card_holder)) {
    $issues[] = "Card Holder Name Must Contain Only Letters";

  }

  if($cvv ==='') {
    $issues[] = "Enter CVV";

    } elseif (!ctype_digit($cvv)) { 
    $issues[] = "Unsupported CVV Format";

  }elseif (strlen($cvv) <3 || strlen($cvv) > 4 ) { 
    $issues[] = "Enter Full CVV";

  }

  if($expiry_date ==='') {
    $issues[] = "Enter Expiry Date";

    } elseif (!preg_match('/^(0[1-9]|1[0-2])\/([0-9]{2})$/', $expiry_date)) {
    $issues[] = "Enter Valid Expiry Date Format";

    } else {

    $dates = explode('/', $expiry_date);
    $month= $dates[0];
    $year= $dates[1];

    $new_yr= (int)$year + 2000;
    $converted_date = DateTime::createFromFormat('Y-m-d', $new_yr . '-' . $month . '-01');
    $converted_date->modify('last day of this month');

    $today_date = new DateTime('today');

    if ($today_date > $converted_date) {
      $issues[] = "Card Expired";
    }

  }
    
}

if (!$pay_submitted || !empty($issues)) {

  if(count($issues) >0) {
    foreach($issues as $issue) {
        echo "<div class='alert alert-danger' role ='alert'> $issue</div>";
    }
  }
    //// THE FORM GOES HERE!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
  exit();
}

try {

  $db->beginTransaction();

  $find_order_items= $db->prepare("SELECT * FROM order_items WHERE order_id = ?");

  $find_order_items->execute(array($logged_order));

  $items=$find_order_items->fetchAll(PDO::FETCH_ASSOC); 
  
  $create_inv_logs = $db->prepare("INSERT INTO inventory_logs (size_id, change_type, qty_change, qty_before, qty_after, reference_type, reference_id)
  VALUES(?, ?, ?, ?, ?, ?, ?)");

  $reduce_stock_lvl = $db->prepare("UPDATE inventory SET stock_qty=stock_qty -? WHERE size_id =?");

  foreach ($items as $im) {

      $stockquery =$db->prepare("SELECT stock_qty FROM inventory WHERE size_id = ? FOR UPDATE");

      $stockquery->execute([$im['size_id']]);
      $available_stock = $stockquery->fetch(PDO::FETCH_ASSOC); 

      if ($available_stock === false) {

          throw new Exception("Product Can't Be Found");

      }

      $as = $available_stock['stock_qty'];

      if(!is_numeric($as)) {

        throw new Exception("There Was A Problem Finding Product Stock Levels");

      }

      if ((int) $im['quantity'] > (int)$as) {

          throw new Exception("Not Enough Stock For Your Order");

      }
    }

  $payment_success = $db->prepare("UPDATE payments SET transaction_reference =?, payment_status =?, payment_date =NOW() WHERE order_id = ? AND payment_status = ?");

  $transaction_ref =  "PAY-" . bin2hex(random_bytes(8));
  $order_success = 'success';
   
  $payment_success->execute(array($transaction_ref, $order_success, $logged_order, 'pending')); 

  if ($payment_success->rowCount() === 0) {

          throw new Exception("Payment Unable To Update");
  }

  $set_order_status= $db->prepare("UPDATE orders SET payment_status =? WHERE order_id = ?");
  $set_order_status->execute(array($order_success, $logged_order)); 

foreach ($items as $im) {

    $reduce_stock_by = $im['quantity'];
    $version_id = $im['size_id'];
      
    $find_old_qty = $db->prepare("SELECT stock_qty FROM inventory WHERE size_id =?");
    $find_old_qty->execute(array($version_id));
    $old_qty = $find_old_qty->fetch(PDO::FETCH_ASSOC);

    if($old_qty === false) {

        throw new Exception("Stock Quantity Missing");

    }

    $old = $old_qty['stock_qty'];

    if(!is_numeric($old)) {

      throw new Exception("Stock Quantity Corrupt");

    }

    $reduce_stock_lvl->execute(array($reduce_stock_by, $version_id));

    $find_new_qty = $db->prepare("SELECT stock_qty FROM inventory WHERE size_id =?");
    $find_new_qty->execute(array($version_id));
    $new_qty = $find_new_qty->fetch(PDO::FETCH_ASSOC);

    if($new_qty === false) {

      throw new Exception("Stock Quantity Missing");

    }

    $new = $new_qty['stock_qty'];

    if(!is_numeric($new)) {

        throw new Exception("Stock Quantity Corrupt");

    }

       
    $type_change = "sale";
    $type_reference = "order";
    $reduction = 0 - $reduce_stock_by;

    $create_inv_logs->execute(array($version_id, $type_change, $reduction, $old, $new, $type_reference, $logged_order));

    }

    $db->commit();
    header("Location: orders.php");
    exit();

  } catch(Exception $problem) {
    $prnt = $problem  -> getMessage();

    echo htmlspecialchars($prnt); 
    $db->rollback();
  }

     ?> 