<?php
  /*  date_default_timezone_set('Africa/Lagos');

    require_once('auth.php');
    // require_once('functions.php');
    $date = date('Y-m-d h:m:s');
  
    
    function updateInventory($login_result, $url,  $data){
        
        $session_id = $login_result->id;
        $id = $data['OrderNumber'];
        $updatedAt = $data['UpdatedAt'];
        $newDate = date("Y-m-d h:m:s", strtotime($updatedAt));
        $TransitDate = "";
        $DispatchedDate = "";
        $DeliveredDate = "";
        $FailedDate = "";
        $PartialDate = "";
        $RescheduledDate = "";
        $DroppedDate = "";
        $PickedUpDate = "";
        
        
        if($data['PackageStatusId'] == 2){
                $status_value = 'Transit';
                $dataArray = array(
                    array("name" => "id", "value" => $id ),
                    array("name" => "dispatch_status",  "value"          => $status_value),
                    array("name" => "date_intransit",  "value"       => $newDate),
                    array("name" => "date_reviewed",  "value"     => $newDate)
                        );
        }
        if($data['PackageStatusId'] == 3){
                $status_value = 'AtTheHub';
                $dataArray = array(
                    array("name" => "id", "value" => $id ),
                    array("name" => "dispatch_status",  "value"          => $status_value),
                    array("name" => "date_at_the_hub",  "value"       => $newDate),
                    array("name" => "date_reviewed",  "value"     => $newDate)
                        );
        }
        if($data['PackageStatusId'] == 8){
                $status_value = 'Dispatched';
                $dataArray = array(
                    array("name" => "id", "value" => $id ),
                    array("name" => "dispatch_status",  "value"          => $status_value),
                    array("name" => "date_dispatched",  "value"       => $newDate),
                    array("name" => "date_reviewed",  "value"     => $newDate)
                        );
        }
        if($data['PackageStatusId'] == 9){
                $status_value = 'Delivered';
                $dataArray = array(
                    array("name" => "id", "value" => $id ),
                    array("name" => "dispatch_status",  "value"          => $status_value),
                    array("name" => "date_delivered",  "value"       => $newDate),
                    array("name" => "date_reviewed",  "value"     => $newDate)
                        );
        }
        if($data['PackageStatusId'] == 10){
                $status_value = 'Failed';
                $dataArray = array(
                    array("name" => "id", "value" => $id ),
                    array("name" => "dispatch_status",  "value"          => $status_value),
                    array("name" => "date_failed",  "value"       => $newDate),
                    array("name" => "date_reviewed",  "value"     => $newDate)
                        );
        }
        if($data['PackageStatusId'] == 11){
                $status_value = 'Partial';
                $dataArray = array(
                    array("name" => "id", "value" => $id ),
                    array("name" => "dispatch_status",  "value"          => $status_value),
                    array("name" => "date_partial_delivery",  "value"       => $newDate),
                    array("name" => "date_reviewed",  "value"     => $newDate)
                        );
        }
        if($data['PackageStatusId'] == 12){
                $status_value = 'Rescheduled';
                $dataArray = array(
                    array("name" => "id", "value" => $id ),
                    array("name" => "dispatch_status",  "value"          => $status_value),
                    array("name" => "date_rescheduled",  "value"       => $newDate),
                    array("name" => "date_reviewed",  "value"     => $newDate)
                        );
        }
        if($data['PackageStatusId'] == 40){
                $status_value = 'Dropped';
                $dataArray = array(
                    array("name" => "id", "value" => $id ),
                    array("name" => "dispatch_status",  "value"          => $status_value),
                    array("name" => "date_dropped",  "value"       => $newDate),
                    array("name" => "date_reviewed",  "value"     => $newDate)
                        );
        }
        if($data['PackageStatusId'] == 1037){
                $status_value = 'PickedUp';
                $dataArray = array(
                    array("name" => "id", "value" => $id ),
                    array("name" => "dispatch_status",  "value"          => $status_value),
                    array("name" => "date_dispatched",  "value"       => $newDate),
                    array("name" => "date_reviewed",  "value"     => $newDate)
                        );
        }
        if($status == 4){$status_value = 'Returned';}
        // $status_value = [ 1 => 'Dispatched',
        //             2 => 'Delivered',
        //             3 => 'canceled',
        //             4 => 'Returned',
        //     ];
       
        // $date_delivered = ($status_value == 2) ? date('Y-m-d 00:00:00') : "";
        // $payment_status = ($status_value == 2) ? "Paid" : "Pending";
        
        
        $set_entry_parameters = array(
      //session id
      "session" => $session_id,

      //The name of the module
      "module_name" => "Contacts",

      //Record attributes
      "name_value_list" => $dataArray,
      );
    $set_entry_result = call("set_entry", $set_entry_parameters, $url);
    $entry_parameters = json_encode($set_entry_parameters);
    $fp = file_put_contents( 'ACE_requestInside.log', $entry_parameters);

    // var_dump($set_entry_result);die();
    if ($set_entry_result) {
      return $set_entry_result;
    }
    }
    if (in_array($_SERVER['REQUEST_METHOD'],array("GET","POST","DELETE"))) {

        $raw_payload = file_get_contents('php://input');
        $payload = json_decode($raw_payload, true);
        $req_dump = print_r( $raw_payload, true );
        
    if(isset($payload['PackageId'])){
            $aceData = updateInventory($login_result, $url,  $payload);
            if($aceData){
                $fp = file_put_contents( 'ACE_request'.$date.'.log', $req_dump );
                echo json_encode(["200" => "Successfull"]);
            }
    }

     else{
            echo json_encode(["401" => "Unauthorized"]);
    }   
        
    }*/
?>