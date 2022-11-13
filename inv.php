<?php
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
    function searchRubie($conn, $key){
        //The name of the module
        $sql = "SELECT * from contacts WHERE account_number = '$key' OR id = '$key'";
        $result = sql_fetchOne($conn, $sql);
        return $result;
    }
    function updateRubie($conn, $key, $no, $sent_amount){
        //The name of the module
        if($no == 1){
            $sql = "UPDATE contacts SET payment_status = 'Paid',  actual_amount = '$sent_amount' WHERE id = '$key' ";
        }else{
            $sql = "UPDATE contacts SET payment_status = 'Partial',  actual_amount = '$sent_amount' WHERE id = '$key' ";
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
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $raw_payload = file_get_contents('php://input');
    $payload = json_decode($raw_payload, true);

    if(isset($payload['craccount'])){
        $account = $payload['craccount'];
        $rubies = searchRubie($conn, $account);
        $expected_amount = $rubies['total_amount'];
        $id = $rubies['id'];
        $sent_amount    = $payload['amount'];
        if($sent_amount >= $expected_amount){
            $rubies_update = updateRubie($conn, $id, 1, $sent_amount);
            sendSMS($rubies, 1);
            echo json_encode(200);
        }else{
            $rubies_update = updateRubie($conn, $id, 0, $sent_amount);
            sendSMS($rubies, 0);
            echo json_encode(200);
        }
    }
    
    if(isset($payload['amountPaid'])){
     
        $ref = $payload['paymentReference'];
        $rubies = searchRubie($conn, $ref);
        $expected_amount = $rubies['total_amount'];
        $sent_amount    = $payload['amountPaid'];
        if($sent_amount >= $expected_amount){
            $rubies_update = updateRubie($conn, $ref, 1, $sent_amount);
            sendSMS($rubies, 1);
            echo json_encode($rubies['total_amount']);
        }else{
            $rubies_update = updateRubie($conn, $ref, 0, $sent_amount);
            sendSMS($rubies, 0);
            echo json_encode($rubies['total_amount']);
        }
    }

    }
?>