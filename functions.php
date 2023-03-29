<?php
date_default_timezone_set('Africa/Lagos');
    function search($login_result, $url, $getData){
    $session_id = $login_result->id;

      //The name of the module
       $search_by_module_parameters = array(  
        "session" => $session_id,  
        'search_string' => $getData,  
        'modules' => array(  
          'Contacts',  
          ),        
        'offset' => 0,  
        'max_results' => 1,  
        'assigned_user_id' => '',  
        'select_fields' => array('id','name'),  
        'unified_search_only' => false,  
        'favorites' => false  
        );  

        $search_by_module_results = call('search_by_module', $search_by_module_parameters, $url);  
        $id = $search_by_module_results->entry_list['0']->records['0'] ;
        
        return $id;

    }
    
    function UpdateOrder($login_result, $url,  $id){
              
       $session_id = $login_result->id;

    $set_entry_parameters = array(
      //session id
      "session" => $session_id,

      //The name of the module
      "module_name" => "Contacts",

      //Record attributes
      "name_value_list" => array(
        array("name" => "id", "value" => $id ),
        array("name" => "call_direction", "value" => "Outbound" ),
        array("name" => "payment_status", "value" => "Pending" )

        ),
      );
    $set_entry_result = call("set_entry", $set_entry_parameters, $url);
    // var_dump($set_entry_result->id);die();
    $id = $set_entry_result->id;
    if ($set_entry_result) {
      return true;
    }
    }    
    
    function saveUSSDUpdate($login_result, $url,  $id, $status, $phone_number){
              
        $session_id = $login_result->id;
        $date = date('Y-m-d 00:00:00');
        // if($no == 1){
        //     $status = 'Delivered';
        // }else{
        //     $status = 'canceled';
        // }
        $set_entry_parameters = array(
      //session id
      "session" => $session_id,

      //The name of the module
      "module_name" => "Contacts",

      //Record attributes
      "name_value_list" => array(
        array("name" => "id", "value" => $id ),
        array("name" => "dispatch_status", "value" => $status ),
        array("name" => "date_delivered",  "value" => $date ),
        array("name" => "ussd_delivery",   "value" => "1" ),
        array("name" => "rider_number",    "value" => $phone_number )
        ),
      );
    $set_entry_result = call("set_entry", $set_entry_parameters, $url);
    
    if ($set_entry_result) {
        if ($status == "Delivered") {
            $status = 2;
            ims_update($id, $status);
        }
      return $set_entry_result;
    }
    }

    // Update IMS 
    function ims_update($id, $status){
        $auth['user']          = 'shop';
        $auth['pass']          = 'exAPI-20';
        $auth['sales_id']      = $id;
        $auth['status']        = $status;
        $val = ['auth' => $auth];
        $string = http_build_query($val);

        $ch = curl_init();

        $url = 'https://unilogix.online/ims/orderinfo/updateaceorder?'.$string;
            // die(var_dump($url));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $output = curl_exec($ch);

        if($output){
            return true;
        }
        curl_close($ch); 
    }    
    
    function IMSUpdate($login_result, $url,  $id, $status){
              
        $session_id = $login_result->id;
        $date = date('Y-m-d 00:00:00');
        if($status == 1){
            $status = 'PickedUp';
        }elseif($status == 2){
            $status = 'Delivered';
        }else{
            $status = 'canceled';
        }
        $set_entry_parameters = array(
          //session id
          "session" => $session_id,

          //The name of the module
          "module_name" => "Contacts",

          //Record attributes
          "name_value_list" => array(
            array("name" => "id", "value" => $id ),
            array("name" => "dispatch_status", "value" => $status ),
            ),
        );
        $set_entry_result = call("set_entry", $set_entry_parameters, $url);
    
        if ($set_entry_result) {
          return $set_entry_result;
        }
    }  
    function updateInventory($login_result, $url,  $id, $status, $expected_date, $date_dispatched){
        $session_id = $login_result->id;
        if($status == 1){$status_value = 'Dispatched';}
                if($status == 2){$status_value = 'Delivered';}
                        if($status == 3){$status_value = 'canceled';}
                                if($status == 4){$status_value = 'Returned';}
        // $status_value = [ 1 => 'Dispatched',
        //             2 => 'Delivered',
        //             3 => 'canceled',
        //             4 => 'Returned',
        //     ];
       
        $date_delivered = ($status_value == 2) ? date('Y-m-d 00:00:00') : "";
        // $payment_status = ($status_value == 2) ? "Paid" : "Pending";
        $set_entry_parameters = array(
      //session id
      "session" => $session_id,

      //The name of the module
      "module_name" => "Contacts",

      //Record attributes
      "name_value_list" => array(
        array("name" => "id", "value" => $id ),
        array("name" => "dispatch_status", "value" => $status_value),
        array("name" => "date_delivered",  "value" => $date_delivered),
        array("name" => "date_expected",  "value" => $expected_date),
        array("name" => "date_dispatched",  "value" => $date_dispatched),
        
        // array("name" => "payment_status",    "value" => $payment_status)
        ),
      );
    $set_entry_result = call("set_entry", $set_entry_parameters, $url);
    
    var_dump($set_entry_result);die();
    if ($set_entry_result) {
      return $set_entry_result;
    }
    }    
    
    
    function ProvidusUpdate($login_result, $url,  $id, $no, $amount){
              
        $session_id = $login_result->id;
        $date = date('Y-m-d 00:00:00');
        if($no == 1){
            $paid       = 'Paid';
        }else{
            $paid       = 'Partial';
        }
        $set_entry_parameters = array(
      //session id
      "session" => $session_id,

      //The name of the module
      "module_name" => "Contacts",

      //Record attributes
      "name_value_list" => array(
        array("name" => "id", "value" => $id ),
        array("name" => "payment_status", "value" => $paid ),
        array("name" => "actual_amount", "value" => $amount ),
        array("name" => "date_payed", "value" => $date ),

        ),
      );
    $set_entry_result = call("set_entry", $set_entry_parameters, $url);
    // var_dump($set_entry_result);die();

    if ($set_entry_result) {
      return true;
    }
    }
    
    function UpdateOrderPaystatus($login_result, $url,  $id){
              
       $session_id = $login_result->id;

    $set_entry_parameters = array(
      //session id
      "session" => $session_id,

      //The name of the module
      "module_name" => "Accounts",

      //Record attributes
      "name_value_list" => array(
        array("name" => "id", "value" => $id ),
        array("name" => "payment_status", "value" => "Paid" )

        ),
      );
    $set_entry_result = call("set_entry", $set_entry_parameters, $url);
    // var_dump($set_entry_result->id);die();
    $id = $set_entry_result->id;
    if ($set_entry_result) {
      return true;
    }
    }

    function saveUSSDOrder($login_result, $url,  $order){
        
        if($order->product == 1){
            $supreme_combo = $order->qty;
        }        
        if($order->product == 2){
            $supreme_combo_plus = $order->qty;
        }        
        if($order->product == 3){
            $cc_qty = $order->qty;
        }
        
        $session_id = $login_result->id;
    
    $set_entry_parameters = array(
      //session id
      "session" => $session_id,

      //The name of the module
      "module_name" => "Contacts",

      //Record attributes
      "name_value_list" => array(
        array("name" => "last_name", "value" => $order->name ),
        array("name" => "phone_mobile", "value" => $order->phone ),
        array("name" => "email1", "value" => $order->email ),
        
         array("name" => "supreme_combo_plus", "value" => $supreme_combo_plus ),
         array("name" => "supreme_combo", "value" => $supreme_combo ),
        // array("name" => "ufp_qty", "value" => $ufp_qty ),
         
        array("name" => "total_amount", "value" => $order->amount ),
        array("name" => "call_direction", "value" => 'USSD' ),
        array("name" => "tv_channels", "value" => 'USSD' ),
        array("name" => "pay_method", "value" => 'Bank Transfer'),
        // Pending
        array("name" => "payment_status", "value" => 'Pending' ),

        ),
      );
    $set_entry_result = call("set_entry", $set_entry_parameters, $url);
    $id = $set_entry_result->id;
    if ($set_entry_result) {
    //   var_dump($set_entry_result);
      return true;
    }
    }
    function saveOrder($login_result, $url,  $order){

        $session_id = $login_result->id;
        
        $search = search($login_result, $url, $order->order_id);
        if (empty($search)) {
            
            if($order->payment_method == "bacs"){
                $payment_method = "Bank Transfer";
                $payment_status = "Pending";
            }
            if($order->payment_method == "cod"){
                $payment_method = "Pay On Delivery";
                $payment_status = "Pending";
            }
            if($order->payment_method == "zilla"){
                $payment_method = "BNPL";
                $payment_status = "Paid";
            }

            
            // Check if order has multiple Items
            // $searchString = ',';
            // if( strpos($searchString, $order->item_sku) !== false ) {
            $traffic_source = $order->traffic_source;
            $order_id = $order->order_id;
            
            $item_sku = (explode(",", $order->item_sku));
            $item_qty = (explode(",", $order->quantity));
            $item_price = (explode(",", $order->item_price));

            $item_color = (explode(",", $order->item_color));
            $item_size = (explode(",", $order->item_size));

            $item_color1 = (explode(",", $order->mf_color1));
            $item_size1 = (explode(",", $order->mf_size1));

            $item_color2 = (explode(",", $order->mf_color2));
            $item_size2 = (explode(",", $order->mf_size2));

            $item_watt = (explode(",", $order->item_watt));
            $item_characters = (explode(",", $order->item_characters));
            $item_napper_size = (explode(",", $order->item_napper_size));
            
        //   Search for Simply Fit
            if (in_array('STV540308', $item_sku)) {
            $key = array_search('STV540308', $item_sku);
            $s_fitqty    = $item_qty[$key];
            }
            
        //   Search for Arctic Air
            if (in_array('STV540306', $item_sku)) {
            $key = array_search('STV540306', $item_sku);
            $a_airqty    = $item_qty[$key];
            }
            
        //   Search for Turbo Pump
            if (in_array('STV540307', $item_sku)) {
                $key = array_search('STV540307', $item_sku);
                $t_pumpqty    = $item_qty[$key];
            }
           // $fitb212 = $fitdw212 = $fitlb212 = $fitb1420 = $fitdw1420 = $fitlb1420 = $genie_bra_mw = $genie_bra_mb = $genie_bra_lw  = $genie_bra_lb =  $genie_bra_xlw = $genie_bra_xlb = $genie_bra_1xw = $genie_bra_1xb = $genie_bra_2xw = $genie_bra_3xw = $nb9gold = $nb9grey = $nb9white = $nb6grey = $nb6white = 0;

        //   Search for Variable Product
            foreach ($item_sku as $key => $value){
                // Myfit
                            
                if ($value == 'STV540305' || $value == 'GGL540305' || $value == 'IG540305' || $value == 'FB540305' || $value == 'EM540305' || $value == 'STV540305B') {
                    if($item_size1[$key] == "14-20" && $item_color1[$key] == "light-blue"){
                        $fit_jen_1420lb = $item_qty[$key];
                    }
                    elseif($item_size1[$key] == "2-12" && $item_color1[$key] == "dark-wash"){
                        $fitdw212 = $item_qty[$key];
                    }
                    elseif($item_size1[$key] == "2-12" && $item_color1[$key] == "light-blue"){
                        $fitlb212 = $item_qty[$key];
                    }
                    elseif($item_size1[$key] == "14-20" && $item_color1[$key] == "black"){
                        $fitb1420 = $item_qty[$key];
                    }
                    elseif($item_size1[$key] == "14-20" && $item_color1[$key] == "dark-wash"){
                        $fitdw1420 = $item_qty[$key];
                    } 

                    if($item_size2[$key] == "14-20" && $item_color2[$key] == "light-blue"){
                        $fit_jen_1420lb_2 = $item_qty[$key];
                    }
                    elseif($item_size2[$key] == "2-12" && $item_color2[$key] == "dark-wash"){
                        $fitdw212_2 = $item_qty[$key];
                    }
                    elseif($item_size2[$key] == "2-12" && $item_color2[$key] == "light-blue"){
                        $fitlb212_2 = $item_qty[$key];
                    }
                    elseif($item_size2[$key] == "14-20" && $item_color2[$key] == "black"){
                        $fitb1420_2 = $item_qty[$key];
                    }
                    elseif($item_size2[$key] == "14-20" && $item_color2[$key] == "dark-wash"){
                        $fitdw1420_2 = $item_qty[$key];
                    } 
                }
                
                if(in_array('STV540305', $item_sku)) {$channel_sku = "Website";  }
                if(in_array('STV540305B', $item_sku)) {$channel_sku = "Website";  }
                if(in_array('IG540305', $item_sku))  {$channel_sku = "Instagram";}
                if(in_array('FB540305', $item_sku))  {$channel_sku = "Facebook"; }
                if(in_array('GGL540305', $item_sku)) {$channel_sku = "Google"; }  
                if(in_array('EM540305', $item_sku)) {$channel_sku = "Email";}


                if ($value == 'STV690684' || $value == 'GGL690684' || $value == 'EM690684' || $value == 'FB690684' || $value == 'EM690684') {
                    if($item_size[$key] == "M" && $item_color[$key] == "WHITE"){
                        $genie_bra_mw = $item_qty[$key];
                    }
                    elseif($item_size[$key] == "M" && $item_color[$key] == "BLACK"){
                        $genie_bra_mb = $item_qty[$key];
                    }
                    elseif($item_size[$key] == "L" && $item_color[$key] == "WHITE"){
                        $genie_bra_lw = $item_qty[$key];
                    }
                    elseif($item_size[$key] == "L" && $item_color[$key] == "BLACK"){
                        $genie_bra_lb = $item_qty[$key];
                    }
                    elseif($item_size[$key] == "XL" && $item_color[$key] == "WHITE"){
                        $genie_bra_xlw = $item_qty[$key];
                    }
                    elseif($item_size[$key] == "XL" && $item_color[$key] == "BLACK"){
                        $genie_bra_xlb = $item_qty[$key];
                    }
                    elseif($item_size[$key] == "1X" && $item_color[$key] == "WHITE"){
                        $genie_bra_1xw = $item_qty[$key];
                    }
                    elseif($item_size[$key] == "1X" && $item_color[$key] == "BLACK"){
                        $genie_bra_1xb = $item_qty[$key];
                    }
                    elseif($item_size[$key] == "2X" && $item_color[$key] == "WHITE"){
                        $genie_bra_2xw = $item_qty[$key];
                    }
                    elseif($item_size[$key] == "3X" && $item_color[$key] == "WHITE"){
                        $genie_bra_3xw = $item_qty[$key];
                    }
                }

                if(in_array('STV690684', $item_sku)) {$channel_sku = "Website"; }
                if(in_array('IG690684', $item_sku))  {$channel_sku = "Instagram";}
                if(in_array('FB690684', $item_sku))  {$channel_sku = "Facebook"; }
                if(in_array('GGL690684', $item_sku)) {$channel_sku = "Google"; }  
                if(in_array('EM690684', $item_sku)) {$channel_sku = "Email";}

                // Happy Nappers
                if ($value == 'STV6903105' || $value == 'GGL6903105' || $value == 'EM6903105') {
                    if($item_napper_size[$key] == "M" && $item_characters[$key] == "PINK CAT"){
                        // $napper_mpc = $item_qty[$key];
                        $napper_m = $item_qty[$key];
                    }
                    elseif($item_napper_size[$key] == "L" && $item_characters[$key] == "PINK CAT"){
                        // $napper_lpc = $item_qty[$key];
                        $napper_l = $item_qty[$key];
                    }
                    elseif($item_napper_size[$key] == "L" && $item_characters[$key] == "PINK UNICORN"){
                        // $napper_lpu = $item_qty[$key];
                        $napper_l = $item_qty[$key];
                    }
                    elseif($item_napper_size[$key] == "M" && $item_characters[$key] == "PINK UNICORN"){
                        // $napper_mpu = $item_qty[$key];
                        $napper_m = $item_qty[$key];
                    }
                    elseif($item_napper_size[$key] == "L" && $item_characters[$key] == "WHITE UNICORN"){
                        // $napper_lwu = $item_qty[$key];
                        $napper_l = $item_qty[$key];
                    }
                    elseif($item_napper_size[$key] == "M" && $item_characters[$key] == "WHITE UNICORN"){
                        // $napper_mwu = $item_qty[$key];
                        $napper_m = $item_qty[$key];
                    }
                    elseif($item_napper_size[$key] == "L" && $item_characters[$key] == "GREY SHARK"){
                        // $napper_lgs = $item_qty[$key];
                        $napper_l = $item_qty[$key];
                    }
                    elseif($item_napper_size[$key] == "M" && $item_characters[$key] == "GREY SHARK"){
                        // $napper_mgs = $item_qty[$key];
                        $napper_m = $item_qty[$key];
                    }
                }

                if(in_array('STV6903105', $item_sku)) {$channel_sku = "Website"; }
                if(in_array('GGL6903105', $item_sku)) {$channel_sku = "Google"; }
                if(in_array('EM6903105', $item_sku)) {$channel_sku = "Email";}

                // For Nutri Bullet
                if ($value == 'STV690387' || $value == 'GGL690387' || $value == 'EM690387' || $value == 'FB690387' || $value == 'EM690387') {
                    if($item_watt[$key] == "900" && $item_color1[$key] == "gold"){
                        $nb9gold = $item_qty[$key];
                    }
                    elseif($item_watt[$key] == "900" && $item_color1[$key] == "grey"){
                        $nb9grey = $item_qty[$key];
                    }
                    elseif($item_watt[$key] == "900" && $item_color1[$key] == "white"){
                        $nb9white = $item_qty[$key];
                    }
                    elseif($item_watt[$key] == "600" && $item_color1[$key] == "grey"){
                        $nb6grey = $item_qty[$key];
                    }
                    elseif($item_watt[$key] == "600" && $item_color1[$key] == "white"){
                        $nb6white = $item_qty[$key];
                    }
                }

                if(in_array('STV690387', $item_sku)) {$channel_sku = "Website";  }
                if(in_array('IG690387', $item_sku))  {$channel_sku = "Instagram";}
                if(in_array('FB690387', $item_sku))  {$channel_sku = "Facebook"; }
                if(in_array('GGL690387', $item_sku)) {$channel_sku = "Google"; }  
                if(in_array('EM690387', $item_sku)) {$channel_sku = "Email";}
            }


            $fit_jen_1420lb = myfit_qty($fit_jen_1420lb, $fit_jen_1420lb_2);
            $fitdw212  = myfit_qty($fitdw212, $fitdw212_2);
            $fitlb212  = myfit_qty($fitlb212, $fitlb212_2);
            $fitb1420  = myfit_qty($fitb1420, $fitb1420_2);
            $fitdw1420 = myfit_qty($fitdw1420, $fitdw1420_2) ;
            

            //   Contour 2-in-1 Leg Relief Wedge Pillow
            if (in_array('STV5403107', $item_sku) || in_array('EM5403107', $item_sku) || in_array('GGL5403107', $item_sku)) {

                if(in_array('STV5403107', $item_sku)) {$channel_sku = "Website";  
                $key = array_search('STV5403107', $item_sku);}
                if(in_array('GGL5403107', $item_sku)) {$channel_sku = "Google";   
                $key = array_search('GGL5403107', $item_sku);}
                if(in_array('EM5403107', $item_sku)) {$channel_sku = "Email";   
                $key = array_search('EM5403107', $item_sku);}

                $contour_wedge    = $item_qty[$key];
            }

        //   Search for slim_jeggings
            if (in_array('STV540317', $item_sku)) {
            $key = array_search('STV540317', $item_sku);
            $slim_jeggings_qty    = $item_qty[$key];
            }
        //   Search for velform_mini
            if (in_array('STV540316', $item_sku)) {
            $key = array_search('STV540316', $item_sku);
            $velform_mini_qty    = $item_qty[$key];
            }
        //   Search for comfortisse_bra
            if (in_array('STV540315', $item_sku)) {
            $key = array_search('STV540315', $item_sku);
            $comfortisse_bra_qty    = $item_qty[$key];
            }
        //   Search for polaryte_unglasses
            if (in_array('STV540314', $item_sku)) {
            $key = array_search('STV540314', $item_sku);
            $polaryte_unglasses_qty    = $item_qty[$key];
            }
        //   Search for starlyf_cam
            if (in_array('STV540313', $item_sku)) {
            $key = array_search('STV540313', $item_sku);
            $starlyf_cam_qty    = $item_qty[$key];
            }
        //   Search for insta_life
            if (in_array('STV540312', $item_sku)) {
            $key = array_search('STV540312', $item_sku);
            $insta_life_qty    = $item_qty[$key];
            }
        //   Search for starlyf_broom
            if (in_array('STV540311', $item_sku)) {
            $key = array_search('STV540311', $item_sku);
            $starlyf_broom_qty    = $item_qty[$key];
            }
        //   Search for gymform_abs
            if (in_array('STV540310', $item_sku)) {
            $key = array_search('STV540310', $item_sku);
            $gymform_abs_qty    = $item_qty[$key];
            }
            
          //   Only Skillet
            if (in_array('STV540304', $item_sku)) {
            $key = array_search('STV540304', $item_sku);
            $upsellprice  = $item_price[$key];
            $upsellqty1    = $item_qty[$key];
            }
            
        //   Only XL
            if (in_array('STV540319', $item_sku)) {
            $key = array_search('STV540319', $item_sku);
            $channel_sku = "Website";
            $xlqty    = $item_qty[$key];
            }

        //   Supereme Combo
            if (in_array('STV690300-1', $item_sku) || in_array('IG690300', $item_sku) || in_array('FB690300', $item_sku) || in_array('GGL690300', $item_sku) || in_array('EM690300', $item_sku)) {

                if(in_array('STV690300-1', $item_sku)) {$channel_sku = "Website";  $key = array_search('STV690300-1', $item_sku);}
                if(in_array('IG690300', $item_sku))  {$channel_sku = "Instagram";$key = array_search('IG690300', $item_sku);}
                if(in_array('FB690300', $item_sku))  {$channel_sku = "Facebook"; $key = array_search('FB690300', $item_sku);}
                if(in_array('GGL690300', $item_sku)) {$channel_sku = "Google";   $key = array_search('GGL690300', $item_sku);}
                if(in_array('EM690300', $item_sku)) {$channel_sku = "Email";   $key = array_search('EM690300', $item_sku);}

                $supereme    = $item_qty[$key];
            }

        //   Supereme Combo
            if (in_array('STV690308', $item_sku) || in_array('IG690308', $item_sku) || in_array('FB690308', $item_sku) || in_array('GGL690308', $item_sku) || in_array('EM690308', $item_sku)) {

                if(in_array('STV690308', $item_sku)) {$channel_sku = "Website";  $key = array_search('STV690308', $item_sku);}
                if(in_array('IG690308', $item_sku))  {$channel_sku = "Instagram";$key = array_search('IG690308', $item_sku);}
                if(in_array('FB690308', $item_sku))  {$channel_sku = "Facebook"; $key = array_search('FB690308', $item_sku);}
                if(in_array('GGL690308', $item_sku)) {$channel_sku = "Google";   $key = array_search('GGL690308', $item_sku);}
                if(in_array('EM690308', $item_sku)) {$channel_sku = "Email";   $key = array_search('EM690308', $item_sku);}

                $supereme_max    = $item_qty[$key];
            }
        //   Only Wonder Cooker
            if (in_array('STV540322-1', $item_sku) || in_array('IG540322', $item_sku) || in_array('FB540322', $item_sku) || in_array('GGL540322', $item_sku) || in_array('EM540322', $item_sku)) {
            // $key = array_search('STV540322-1', $item_sku);
            
                if(in_array('STV540322-1', $item_sku)) {$channel_sku = "Website";  $key = array_search('STV540322-1', $item_sku);}
                if(in_array('IG540322', $item_sku))  {$channel_sku = "Instagram";$key = array_search('IG540322', $item_sku);}
                if(in_array('FB540322', $item_sku))  {$channel_sku = "Facebook"; $key = array_search('FB540322', $item_sku);}
                if(in_array('GGL540322', $item_sku)) {$channel_sku = "Google";   $key = array_search('GGL540322', $item_sku);}
                if(in_array('EM540322', $item_sku)) {$channel_sku = "Email";   $key = array_search('EM540322', $item_sku);}
                
                $wcqty    = $item_qty[$key];
            }


        //   Steam Mop
            if (in_array('STV6903110', $item_sku) || in_array('EM6903110', $item_sku) || in_array('GGL6903110', $item_sku)) {
                if(in_array('STV6903110', $item_sku)) {$channel_sku = "Website";  $key = array_search('STV6903110', $item_sku);}
                if(in_array('GGL6903110', $item_sku)) {$channel_sku = "Google";   $key = array_search('GGL6903110', $item_sku);}
                if(in_array('EM6903110', $item_sku)) {$channel_sku = "Email";   $key = array_search('EM6903110', $item_sku);}

                $genesis_mop      = $item_qty[$key];
            }   

        //   Pressure King Pro
            if (in_array('STV5403115', $item_sku) || in_array('EM5403115', $item_sku) || in_array('GGL5403115', $item_sku)) {
                if(in_array('STV5403115', $item_sku)) {$channel_sku = "Website";  $key = array_search('STV5403115', $item_sku);}
                if(in_array('GGL5403115', $item_sku)) {$channel_sku = "Google";   $key = array_search('GGL5403115', $item_sku);}
                if(in_array('EM5403115', $item_sku)) {$channel_sku = "Email";   $key = array_search('EM5403115', $item_sku);}

                $pressure_king_pro      = $item_qty[$key];
            } 

        //   REVIVA GUN
            if (in_array('STV5403116', $item_sku) || in_array('EM5403116', $item_sku) || in_array('GGL5403116', $item_sku)) {
                if(in_array('STV5403116', $item_sku)) {$channel_sku = "Website";  $key = array_search('STV5403116', $item_sku);}
                if(in_array('GGL5403116', $item_sku)) {$channel_sku = "Google";   $key = array_search('GGL5403116', $item_sku);}
                if(in_array('EM5403116', $item_sku)) {$channel_sku = "Email";   $key = array_search('EM5403116', $item_sku);}

                $reviva_gun      = $item_qty[$key];
            } 

            
        //   Only Copper Chef Pan Website
            if (in_array('STV540303', $item_sku) || in_array('IG540303', $item_sku) || in_array('FB540303', $item_sku) || in_array('GGL540303', $item_sku) || in_array('EM540303', $item_sku)) {
                // $key = array_search('STV540303', $item_sku);
                
                if(in_array('STV540303', $item_sku)) {$channel_sku = "Website";  $key = array_search('STV540303', $item_sku);}
                if(in_array('IG540303', $item_sku))  {$channel_sku = "Instagram";$key = array_search('IG540303', $item_sku);}
                if(in_array('FB540303', $item_sku))  {$channel_sku = "Facebook"; $key = array_search('FB540303', $item_sku);}
                if(in_array('GGL540303', $item_sku)) {$channel_sku = "Google";   $key = array_search('GGL540303', $item_sku);}
                if(in_array('EM540303', $item_sku)) {$channel_sku = "Email";   $key = array_search('EM540303', $item_sku);}
                
                $chefqty    = $item_qty[$key];
            }

        //   Legacy Contour
            if (in_array('STV678495', $item_sku) || in_array('IG678495', $item_sku) || in_array('FB678495', $item_sku) || in_array('GGL678495', $item_sku) || in_array('EM678495', $item_sku)) {
                // $key = array_search('STV678495', $item_sku);
                
                if(in_array('STV678495', $item_sku)) {$channel_sku = "Website";  $key = array_search('STV678495', $item_sku);}
                if(in_array('IG678495', $item_sku))  {$channel_sku = "Instagram";$key = array_search('IG678495', $item_sku);}
                if(in_array('FB678495', $item_sku))  {$channel_sku = "Facebook"; $key = array_search('FB678495', $item_sku);}
                if(in_array('GGL678495', $item_sku)) {$channel_sku = "Google";   $key = array_search('GGL678495', $item_sku);}
                if(in_array('EM678495', $item_sku)) {$channel_sku = "Email";   $key = array_search('EM678495', $item_sku);}
                
                $contour    = $item_qty[$key];
            }
        //   Tripple Combo Plus
            if (in_array('STV690302', $item_sku) || in_array('IG690302', $item_sku) || in_array('FB690302', $item_sku) || in_array('GGL690302', $item_sku) || in_array('EM690302', $item_sku)) {
                // $key = array_search('STV690302', $item_sku);
                
                if(in_array('STV690302', $item_sku)) {$channel_sku = "Website";  $key = array_search('STV690302', $item_sku);}
                if(in_array('IG690302', $item_sku))  {$channel_sku = "Instagram";$key = array_search('IG690302', $item_sku);}
                if(in_array('FB690302', $item_sku))  {$channel_sku = "Facebook"; $key = array_search('FB690302', $item_sku);}
                if(in_array('GGL690302', $item_sku)) {$channel_sku = "Google";   $key = array_search('GGL690302', $item_sku);}
                if(in_array('EM690302', $item_sku)) {$channel_sku = "Email";   $key = array_search('EM690302', $item_sku);}
                
                $trippleplusqty    = $item_qty[$key];
            }
            
        //   Copper Chef Tripplle Combo Website
            if (in_array('STV730323', $item_sku) || in_array('IG730323', $item_sku) || in_array('FB730323', $item_sku) || in_array('GGL730323', $item_sku) || in_array('EM730323', $item_sku)) {
                // $key = array_search('STV730323', $item_sku);
                
                if(in_array('STV730323', $item_sku)) {$channel_sku = "Website";  $key = array_search('STV730323', $item_sku);}
                if(in_array('IG730323', $item_sku))  {$channel_sku = "Instagram";$key = array_search('IG730323', $item_sku);}
                if(in_array('FB730323', $item_sku))  {$channel_sku = "Facebook"; $key = array_search('FB730323', $item_sku);}
                if(in_array('GGL730323', $item_sku)) {$channel_sku = "Google";   $key = array_search('GGL730323', $item_sku);}
                if(in_array('EM730323', $item_sku)) {$channel_sku = "Email";   $key = array_search('EM730323', $item_sku);}
                
                $trippleqty    = $item_qty[$key];
                $xlqty         = $item_qty[$key];
            }
                
        //   Copper Chef Pan XL Combo
            if (in_array('STV690300', $item_sku) || in_array('IG540320', $item_sku) || in_array('FB540320', $item_sku) || in_array('GGL540320', $item_sku) || in_array('EM690300', $item_sku)) {
                // $key = array_search('STV690300', $item_sku);
                
                if(in_array('STV690300', $item_sku)) {$channel_sku = "Website";  $key = array_search('STV690300', $item_sku);}
                if(in_array('IG540320', $item_sku))  {$channel_sku = "Instagram";$key = array_search('IG540320', $item_sku);}
                if(in_array('FB540320', $item_sku))  {$channel_sku = "Facebook"; $key = array_search('FB540320', $item_sku);}
                if(in_array('GGL540320', $item_sku)) {$channel_sku = "Google";   $key = array_search('GGL540320', $item_sku);}
                if(in_array('EM690300', $item_sku)) {$channel_sku = "Email";   $key = array_search('EM690300', $item_sku);}
                
                $chefqty     = $item_qty[$key];
                $xlqty       = $item_qty[$key];
            } 
                
            
        //   Tripple Combo
            if (in_array('STV540321', $item_sku)) {
            $key = array_search('STV540321', $item_sku);
            $channel_sku = "Website";
            $chefqty     = $item_qty[$key];
            $xlqty       = $item_qty[$key];
            $upsellqty2    = $item_qty[$key];
            }
            
        //   Skillet Combo
            if (in_array('STV540322', $item_sku)) {
            $key = array_search('STV540322', $item_sku);
            $channel_sku = "Website";
            $chefqty     = $item_qty[$key];
            $upsellqty2    = $item_qty[$key];
            } 

        //   Skillet With Lid
            if (in_array('STV690306', $item_sku) || in_array('GGL690306', $item_sku) || in_array('IG690306', $item_sku) || in_array('FB690306', $item_sku) || in_array('EM690306', $item_sku)) {
            
                if(in_array('STV690306', $item_sku)) {$channel_sku = "Website";  $key = array_search('STV690306', $item_sku);}
                if(in_array('IG690306', $item_sku))  {$channel_sku = "Instagram";$key = array_search('IG690306', $item_sku);}
                if(in_array('FB690306', $item_sku))  {$channel_sku = "Facebook"; $key = array_search('FB690306', $item_sku);}
                if(in_array('GGL690306', $item_sku)) {$channel_sku = "Google";   $key = array_search('GGL690306', $item_sku);}
                if(in_array('EM690306', $item_sku)) {$channel_sku = "Email";   $key = array_search('EM690306', $item_sku);}
            $upsellqty2    = $item_qty[$key];
            } 

        //   Genie_bra
            if (in_array('STV690684', $item_sku)) {
            $key = array_search('STV690684', $item_sku);
            $channel_sku = "Website";
            $genie_bra    = $item_qty[$key];
            } 
                   
        //   Power Air Fryer
            if (in_array('PSTV990300', $item_sku) || in_array('IG990300', $item_sku) || in_array('FB990300', $item_sku) || in_array('GGL990300', $item_sku) || in_array('EM990300', $item_sku)) {
                if(in_array('PSTV990300', $item_sku)) {$channel_sku = "Website";  $key = array_search('PSTV990300', $item_sku);}
                if(in_array('IG990300', $item_sku))  {$channel_sku = "Instagram";$key = array_search('IG990300', $item_sku);}
                if(in_array('FB990300', $item_sku))  {$channel_sku = "Facebook"; $key = array_search('FB990300', $item_sku);}
                if(in_array('GGL990300', $item_sku)) {$channel_sku = "Google";   $key = array_search('GGL990300', $item_sku);}
                if(in_array('EM990300', $item_sku)) {$channel_sku = "Email";   $key = array_search('EM990300', $item_sku);}
            $power_air     = $item_qty[$key];
            }  

        //   Power Air Fryer
            if (in_array('STV690322', $item_sku) || in_array('IG690322', $item_sku) || in_array('FB690322', $item_sku) || in_array('GGL690322', $item_sku) || in_array('EM690322', $item_sku)) {
                if(in_array('STV690322', $item_sku)) {$channel_sku = "Website";  $key = array_search('STV690322', $item_sku);}
                if(in_array('IG690322', $item_sku))  {$channel_sku = "Instagram";$key = array_search('IG690322', $item_sku);}
                if(in_array('FB690322', $item_sku))  {$channel_sku = "Facebook"; $key = array_search('FB690322', $item_sku);}
                if(in_array('GGL690322', $item_sku)) {$channel_sku = "Google";   $key = array_search('GGL690322', $item_sku);}
                if(in_array('EM690322', $item_sku)) {$channel_sku = "Email";   $key = array_search('EM690322', $item_sku);}

                $dream_collection      = $item_qty[$key];
            }   

        //   H20 Mop
            if (in_array('STV690389', $item_sku) || in_array('EM690389', $item_sku) || in_array('GGL690389', $item_sku) || in_array('IG690389', $item_sku) || in_array('FB690389', $item_sku)) {
                if(in_array('STV690389', $item_sku)) {$channel_sku = "Website";  $key = array_search('STV690389', $item_sku);}
                if(in_array('IG690389', $item_sku))  {$channel_sku = "Instagram";$key = array_search('IG690389', $item_sku);}
                if(in_array('FB690389', $item_sku))  {$channel_sku = "Facebook"; $key = array_search('FB690389', $item_sku);}
                if(in_array('GGL690389', $item_sku)) {$channel_sku = "Google";   $key = array_search('GGL690389', $item_sku);}
                if(in_array('EM690389', $item_sku)) {$channel_sku = "Email";   $key = array_search('EM690389', $item_sku);}

                $h2omop      = $item_qty[$key];
            }  

        //   SPortEX Threadmil
            if (in_array('STV5403120', $item_sku) || in_array('GGL5403120', $item_sku) || in_array('EM5403120', $item_sku) ) {
                if(in_array('STV5403120', $item_sku)) {$channel_sku = "Website";  $key = array_search('STV5403120', $item_sku);}
                if(in_array('EM5403120', $item_sku)) {$channel_sku = "Google";   $key = array_search('EM5403120', $item_sku);}
                if(in_array('GGL5403120', $item_sku)) {$channel_sku = "Email";   $key = array_search('GGL5403120', $item_sku);}

                $threadmil  = $item_qty[$key];
            }   
  

            $new_qty = ($chefqty != "" ) ? $chefqty : 0;
            
            $ufp_price  = ($new_combo_qty <= 1 ) ? "11950" : "22000";      
        
         $phone  = str_replace(" ", "", $order->customer_phone);
         $phone  = str_replace('-', "", $phone);
         $set_entry_parameters = array(
          //session id
          "session" => $session_id,

          //The name of the module
          "module_name" => "Contacts",

          //Record attributes
          "name_value_list" => array(
            array("name" => "first_name", "value" => $order->bill_firstname ),
            array("name" => "last_name", "value" => $order->bill_surname ),
            array("name" => "phone_mobile", "value" => $phone ),
            array("name" => "email1", "value" => $order->customer_email ),
            array("name" => "description", "value" => $order->transaction_key ),
            array("name" => "traffic_source", "value" => $order->traffic_source ),
            array("name" => "phone_home", "value" => $order_id ),
            
             array("name" => "cc_xl_offer", "value" => $xlqty ),
             array("name" => "wonder_cooker", "value" => $wcqty ),
             array("name" => "power_airfryer", "value" => $power_air ),
             array("name" => "tripple_combo", "value" => $trippleqty ),
             array("name" => "ccp_qty", "value" => $new_qty ),
             array("name" => "ufp_qty", "value" => $upsellqty2 ),
             array("name" => "simply_fit", "value" => $s_fitqty ),
             array("name" => "turbo_pump", "value" => $t_pumpqty ),
             array("name" => "artic_air", "value" => $a_airqty ),
             array("name" => "contour_leg_pillow", "value" => $contour ),
             array("name" => "tripple_combo_plus", "value" => $trippleplusqty ),
             array("name" => "supreme_combo", "value" => $supereme ),
             array("name" => "supreme_combo_plus", "value" => $supereme_max ),
             array("name" => "dream_collection", "value" => $dream_collection ),
             array("name" => "h2omop", "value" => $h2omop ),
             array("name" => "slim_fold", "value" => $threadmil ),

             // Genie Bra
             array("name" => "genie_bra_mw", "value" => $genie_bra_mw ),
             array("name" => "genie_bra_mb", "value" => $genie_bra_mb ),
             array("name" => "genie_bra_lw", "value" => $genie_bra_lw ),
             array("name" => "genie_bra_lb", "value" => $genie_bra_lb ),
             array("name" => "genie_bra_xlw", "value" => $genie_bra_xlw ),
             array("name" => "genie_bra_xlb", "value" => $genie_bra_xlb ),
             array("name" => "genie_bra_1xw", "value" => $genie_bra_1xw ),
             array("name" => "genie_bra_1xb", "value" => $genie_bra_1xb ),
             array("name" => "genie_bra_2xw", "value" => $genie_bra_2xw ),
             array("name" => "genie_bra_3xw", "value" => $genie_bra_3xw ),
             
             array("name" => "slim_jeggings", "value" => $slim_jeggings_qty ),
             array("name" => "velform_mini", "value" => $velform_mini_qty ),
             array("name" => "comfortisse_bra", "value" => $comfortisse_bra_qty ),
             array("name" => "polaryte_sunglasses", "value" => $polaryte_unglasses_qty ),
             array("name" => "starlyf_cam", "value" => $starlyf_cam_qty ),
             array("name" => "insta_life", "value" => $insta_life_qty ),
             array("name" => "starlyf_broom", "value" => $starlyf_broom_qty ),
             array("name" => "gymform_abs", "value" => $gymform_abs_qty ),
             
            // My Fit Records
             // array("name" => "fit_jenb", "value" => $fitb212 ),
             array("name" => "fit_jendw", "value" => $fitdw212 ),
             array("name" => "fit_jenlb", "value" => $fitlb212 ),
             array("name" => "fit_jen_1420b", "value" => $fitb1420 ),
             array("name" => "fit_jen_1420dw", "value" => $fitdw1420 ),
             array("name" => "fit_jen_1420lb", "value" => $fit_jen_1420lb ),

             // Nutri Bullet
             array("name" => "nutribullet600white", "value" => $nb6white ),
             array("name" => "nutribullet600grey", "value" => $nb6grey ),
             array("name" => "nutribullet900white", "value" => $nb9white ),
             array("name" => "nutribullet900grey", "value" => $nb9grey ),
             array("name" => "nutribullet900gold", "value" => $nb9gold ),

             array("name" => "contour_2_in_one_wedge", "value" => $contour_wedge ),

             array("name" => "happy_nappers_large_c", "value" => $napper_l ),
             array("name" => "happy_nappers_medium_c", "value" => $napper_m ),
             array("name" => "genesis_mop_qty_c", "value" => $genesis_mop ),
             array("name" => "pressure_king_pro_c", "value" => $pressure_king_pro ),
             array("name" => "reviva_gun_c", "value" => $reviva_gun ),
             
            array("name" => "total_amount", "value" => $order->total ),
            array("name" => "deliverycharge", "value" => $order->shipping_cost ),
            array("name" => "primary_address_street", "value" => $order->bill_address1." ".$order->bill_address2 ),
            array("name" => "primary_address_city", "value" => $order->bill_city ),
            array("name" => "primary_address_state", "value" => $order->ship_state ),
            // array("name" => "lgas", "value" => $order->bill_city ),
            array("name" => "del_state", "value" => $order->ship_state ),
            array("name" => "primary_address_postalcode", "value" => $order->ship_zip ),
            array("name" => "primary_address_country", "value" => "NG" ),
            array("name" => "tv_channels", "value" => $channel_sku ),
            array("name" => "call_direction", "value" => "Website" ),
            array("name" => "pay_method", "value" => $payment_method),
            // Pending
            array("name" => "payment_status", "value" => $payment_status ),
            array("name" => "assigned_user_id", "value" => 1 ),

            ),
          );
        $set_entry_result = call("set_entry", $set_entry_parameters, $url);
        $id = $set_entry_result->id;
        if ($set_entry_result) {
            //   var_dump($set_entry_result);
          return true;
            }
        }
    }
    
    
    // 
    function myfit_qty($val1, $val2){
        if ($val1 >= 1 && $val2 >= 2) {
            $qty = $val1 + $val2;
        }else{
            if ($val1 >=1 & $val2 == 0) {
               $qty = $val1;
            } elseif ($val2 >=1 & $val1 == 0) {
               $qty = $val1;
            } else{
                $qty = 0;
            }
        }
        return $qty;
    }
    function saveMissedCalls($login_result, $url,  $number){
        $session_id = $login_result->id;
        $set_entry_parameters = array(
          "session" => $session_id,
          "module_name" => "AOBH_BusinessHours",
          //Record attributes
          "name_value_list" => array(
            array("name" => "name", "value" => $number ),
            array("name" => "description", "value" => "Request For Call Back"),
            ),
          );
        $set_entry_result = call("set_entry", $set_entry_parameters, $url);
        $id = $set_entry_result->id;
        if ($set_entry_result) {
        //   var_dump($set_entry_result);
          return true;
        }
    }
    
?>