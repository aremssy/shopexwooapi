<?php
    date_default_timezone_set('Africa/Lagos');

    require_once('auth.php');
    require_once('functions.php');
    
    // Database Credential
    $db_user = 'aremssy_root';
    $db_password = 'aremssy0859';
    $db_name = 'aremssy_unilogix_crm_db';
    $db_host = 'localhost';
    $date = date('Y-m-d H:m:s');
    
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
    
    if (in_array($_SERVER['REQUEST_METHOD'],array("GET","POST","DELETE"))) {

        $raw_payload = file_get_contents('php://input');
        $payload = json_decode($raw_payload, true);
        $req_dump = print_r( $raw_payload, true );
        ;
        $fp = file_put_contents( 'request'.$payload['product']['reference'].'.log', $req_dump );
     
     
        // Update Delivery status from Inventory
        if(isset($_POST['inv_status'])){
            $status = $_POST['inv_status'];
            $ref = $_POST['ref_code'];
            $pay_m = $_POST['pay_method'];
            
            // Get the sales recod information
            $record = searchRef($conn, $ref);
            $state  = $record['del_state'];
            $expected_date = "";
            if ($status == 1){
                if($state == 'Lagos'){
                    $days = 1;
                }else{
                    $days = 4;
                }  
                $expected_date = date('Y-m-d H:m:s', strtotime($date. ' + '.$days.' days'));
            }
                $date_dispatched = ($status == 1) ? date('Y-m-d 00:00:00') : $record['date_dispatched'];
            
            // var_dump($date_dispatched);die();
            $rubies_update = updateInventory($login_result, $url, $ref, $status, $expected_date, $date_dispatched);
            echo json_encode($id);
        }
    
    
        // Update providus Payment Status
        if(isset($payload['amountPaid'])){
         
        //  This is reference for the Reserved account
            $ref = $payload['product']['reference'];
            $sent_amount    = $payload['amountPaid'];
    
        // From Unilogix DB
            $providus = searchRef($conn, $ref);
            $expected_amount = $providus['total_amount'];
            $paid_amount     = intval($providus['actual_amount']);
            
            $new_amount = $paid_amount + $sent_amount;
            
        // Condition to call API
            if($new_amount >= $expected_amount){
                $no  = 1;
            }else{
                $no  = 0;
            }
            
            $req = ProvidusUpdate($login_result, $url,  $ref, $no, $new_amount);
                        // var_dump($req);die();

            sendSMS($providus, $no);
            echo json_encode($providus['total_amount']);
       
    }

    }else{
        echo '401 Unauthorized';
    }
?>