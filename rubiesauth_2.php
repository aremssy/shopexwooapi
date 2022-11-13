<?php
date_default_timezone_set('Africa/Lagos');
    // Database Credential
    $db_user = 'aremssy_root';
    $db_password = 'aremssy0859';
    $db_name = 'aremssy_unilogix_crm_db';
    $db_host = 'localhost';
    
    $conn = new PDO("mysql:host=" . $db_host . ";dbname=" . $db_name, $db_user, $db_password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    function sql_fetchAll($conn, $sql){
        $q = $conn->prepare($sql);
        $flag = $q->execute();
        $result = $q->fetchAll();
        $conn=null;
        return $result;
    }
    function sql_fetchOne($conn, $sql){
        $q = $conn->prepare($sql);
        $flag = $q->execute();
        $result = $q->fetch();
        $conn=null;
        return $result;
    }
    function searchRef($conn, $key){
        //The name of the module
        $sql = "SELECT * from contacts WHERE account_number = '$key' OR id = '$key' OR ref_code = '$key'";
        $result = sql_fetchOne($conn, $sql);
        return $result;
    }
    function searchRubie($conn, $key){
        //The name of the module
        $sql = "SELECT * from contacts WHERE account_number = '$key' OR id = '$key' OR ref_code = '$key'";
        $result = sql_fetchOne($conn, $sql);
        return $result;
    }
    function updateRubie($conn, $key, $no, $sent_amount){
        //The name of the module updateRubie($conn, $ref, 1, 0);
        $date = date('Y-m-d H:m:s');
        if($no == 1){
            $sql = "UPDATE contacts SET payment_status = 'Paid',  actual_amount = '$sent_amount',  date_payed = '$date' WHERE id = '$key' ";
        }else{
            $sql = "UPDATE contacts SET payment_status = 'Partial',  actual_amount = '$sent_amount',  date_payed = '$date' WHERE id = '$key' ";
        }
        $q = $conn->prepare($sql);
        $flag = $q->execute();
        return $flag;
    }
    function updateInventory($conn, $key, $no, $pay_m){
        //The name of the module
        $date = date('Y-m-d H:m:s');
        
        $record = searchRef($conn, $key);
        $state  = $record['del_state'];
        if($state == 'Lagos'){
            $days = 4;
        }else{
            $days = 5;
        }
        
        $expected_date = date('Y-m-d H:m:s', strtotime($date. ' + '.$days.' days'));
        
        if($no == 2){
            if($pay_m == "Bank Transfer"){
                $sql = "UPDATE contacts SET dispatch_status = 'Delivered' WHERE id = '$key' ";
                $sql = "UPDATE stv_dispatch SET dispatch_status = 'Delivered', date_delivered = '$date' WHERE id = '$key' ";
            }else{
                $sql = "UPDATE contacts SET payment_status = 'Paid',  dispatch_status = 'Delivered',  date_payed = '$date'  WHERE id = '$key' ";
            }
        }elseif($no == 1){
            $sql = "UPDATE contacts SET dispatch_status = 'Dispatched',  date_dispatched = '$date' WHERE id = '$key' ";
            $sql = "UPDATE stv_dispatch SET dispatch_status = 'Dispatched', date_dispatched = '$date', date_expected = '$expected_date'  WHERE id = '$key' ";
        }elseif($no == 4){
            $sql = "UPDATE contacts SET dispatch_status = 'Returned'  WHERE id = '$key' ";
        }elseif($no == 3){
            $sql = "UPDATE contacts SET dispatch_status = 'canceled'  WHERE id = '$key' ";
        }
        $q = $conn->prepare($sql);
        $flag = $q->execute();
        return $flag;
    }
    
    // SMS Method
        function sendSMS($data, $no){
        if($no == 1){
        $msg = "Dear ".$data['last_name'].", 
                \nYour payment has been confirmed successfully. 
                \nYou will be notified once the item has been dispatched for shipping
                \nThanks for Contacting Shopex TV";
        }else{
        $msg = "Dear ".$data['last_name'].", 
                \nWe have received your payment. However, we noticed you have made a partial payment. 
                \nKindly check our previous SMS or email for information on the complete amount to be paid.
                \nThanks for Contacting Shopex TV";
        }
        $urldata  = array(
		  'dnd' => '3', 
		  'api_token' => 'U7w6dRATp9Pyrgmhv2FHQbzpCwh7XQv6kY86YqUHFl9weAhZlIOERZzn2d4f',
		  'body' => $msg,
		  'to' => $data['phone_mobile'], 
		  'from' => 'Shopex TV',
		  );
        $string = http_build_query($urldata);
        
        $ch = curl_init();
        $url = 'https://www.bulksmsnigeria.com/api/v1/sms/create?'.$string;

        //  var_dump($data);die();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

         $output = curl_exec($ch);
         return $output;
        curl_close($ch);
        // die();
        }
    
    // die(var_dump($rubies));
    
    if (in_array($_SERVER['REQUEST_METHOD'],array("GET","POST","DELETE"))) {

    $raw_payload = file_get_contents('php://input');
    $payload = json_decode($raw_payload, true);
    
    $req_dump = print_r( $raw_payload, true );
    $fp = file_put_contents( 'request.log', $req_dump );
    
    if(isset($_POST['inv_status'])){
        $status = $_POST['inv_status'];
        $ref = $_POST['ref_code'];
        $pay_m = $_POST['pay_method'];

            $rubies_update = updateInventory($conn, $ref, $status, $pay_m);
            // sendSMS($inventory, 1);
            echo json_encode($id);
    }

    if(isset($payload['amountPaid'])){
     
    //  This is reference for the Reserved account
        $ref = $payload['product']['reference'];
        
    //  From Providus Payload using $payload testing over URL with $_GET
    
    
    //  This is reference for the Invoice account
        // $ref = $payload['paymentReference'];
        
        $sent_amount    = $payload['amountPaid'];

        // From Unilogix DB
        $providus = searchRef($conn, $ref);
        $expected_amount = $providus['total_amount'];
        $paid_amount     = $providus['actual_amount'];
        
        $new_amount = $paid_amount + $sent_amount;
        // Condition to call API
        if($new_amount >= $expected_amount){
            $no  = 1;
        }else{
            $no  = 0;
        }
        $urldata  = array(
		  'pay_providus' => $ref, 
		  'amount' => $new_amount,
		  'no' => $no,
		  );
        $string = http_build_query($urldata);
        
        $ch = curl_init();
        $url = 'https://www.unilogix.com.ng/wooapi/call.php?'.$string;

        //  var_dump($data);die();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

         $output = curl_exec($ch);
         return $output;
         sendSMS($providus, $no);
         curl_close($ch);
        //  var_dump($output);die();
        echo json_encode($providus['total_amount']);
       
    }

    }else{
        echo '401 Unauthorized';
    }
?>