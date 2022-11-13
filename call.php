<?php
require_once('auth.php');
require_once('functions.php');

  $order   =  new ArrayObject($_POST,ArrayObject::ARRAY_AS_PROPS);
  if (isset($_POST['apiuser'])) {
  $savereq = saveOrder($login_result, $url, $order);
    return true;
    
  }  
  
  elseif (isset($_POST['ussd_sales'])) {
  $saveussdreq = saveUSSDOrder($login_result, $url, $order);
    return true;
    
  }  
  
  elseif (isset($_POST['ims_update'])) {
  $saveussdreq = IMSUpdate($login_result, $url, $order->id, $order->status);
    return true;
  }  
  
  elseif (isset($_POST['ussd_update'])) {
  $saveussdreq = saveUSSDUpdate($login_result, $url, $order->id, $order->status, $order->phone_number);
    return true;
    
  }
  elseif (isset($_GET['search'])) {
  $ref = $_GET['search'];
//   echo $ref; die();
  $req = search($login_result, $url, $ref);
    var_dump($req);die();
    return $req;
    
  }
  elseif (isset($_GET['out_of_office'])) {
  $number = $_GET['phone'];
  $req = saveMissedCalls($login_result, $url,  $number);
  return $req;
  
  }
  elseif (isset($_GET['pay_rubies'])) {
  $id = $_GET['pay_rubies'];
  
  $req = payRubies($login_result, $url,  $id);
//   var_dump($req);die();
  return $req;
  }
  
  elseif (isset($_GET['update'])) {
  $id = $_GET['update'];
  
    $savereq = UpdateOrder($login_result, $url, 'B6H9MB');
  var_dump($savereq);die();
  return $req;
  
  }
  elseif (isset($_GET['pay_providus'])) {
  $id = $_GET['pay_providus'];
  $amount = $_GET['amount'];
  $no = $_GET['no'];
  $req = ProvidusUpdate($login_result, $url,  $id, $no, $amount);
  return true;
  }
  
?>
