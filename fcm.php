<?php

    $config = [
        'SERVER_KEY' => 'AAAAik53ttw:APA91bHte2bzlwZ4ztMs7Qmqe5bPXI-55ijYRU05gIpNwe1bxwScYCf16-fAUjHzJDwE3hmvLjSPj3C4C194HgZSe6lasIppsScSnPQ4DnAoPckcg56fhXHS_xgEdwljQtPzP0eIQgZa',        
        'SERVER_SEND_URL' => 'https://fcm.googleapis.com/fcm/send'
    ];
    define("FCM_CONFIG", $config);

class PushNotificationController 
{
    // Register ios or android device. Save its device token to Our DB.
    // Save device token along with userid
	public function subscribeDevices($request)
	{
		
        if (!$request) {
            $return['error']=true;
            $return['msg']= "Empty data fields";

        }else{
        	$device_token = $request->device_token;
        	$time=Carbon::now();

			$user_device = DB::table("user_fcm_token")->where('token', $device_token)->get();		
			if(count($user_device) > 0){
				// already registered device, UPDATE
				$update = DB::table('user_fcm_token')->where("token", $device_token)->update([
                	"user_id" => $request->user_id? $request->user_id: '',
	                "token" =>$request->device_token,
	                "device"=>$request->device?$request->device: '',
                	"updated_at" => $time]);

				$return['error']=false;
		        $return['msg'] = "Updated registered device";  
		        
			}else{
				// new device registration				
	           	$result=DB::table('user_fcm_token')->insert([
	           		"user_id" => $request->user_id? $request->user_id: '',
	                "token" =>$request->device_token,
	                "device"=>$request->device,
	                "created_at"=>$time,
	                "updated_at"=>$time,
	                ]);

				$return['error']=false;
		        $return['msg']="Newly registered device"; 
			}

        }

        return $return;		
	}

	// Send common notification to all users who subscribed their device.
    // Example : New video / song uploaded to the application
	public function sendNotificationsToAll(Request $request)
	{
		if(empty($data)){
            return false;
        }
        
        $payload = array();
        $user_device = DB::table("user_fcm_token")->orderBy("id","desc")->get();

        $arr = [];
        foreach ($user_device as $device) {
            $arr[] = $device->token;
        }

        if(!empty($arr)){
            $payload = array(
                'registration_ids'=> $arr,    // multple devices
                'notification'=>array(
                    'title'=>$data['title'],
                    'body'=>$data['body'],
                    'sound'=>'default'
                  ),
                'data'=>array(
                    'type' => $data['type'],
                    'id' => $data['id']
                    )
            );       

            $url = FCM_CONFIG["SERVER_SEND_URL"];
            $headers = array(
              'Authorization:key='.FCM_CONFIG["SERVER_KEY"],
              'Content-Type: application/json'
            );

            $ch = curl_init();
            curl_setopt( $ch,CURLOPT_URL, $url);
            curl_setopt( $ch,CURLOPT_POST, true );
            curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
            curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
            curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
            curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $payload ) );
            $result = curl_exec($ch );
            curl_close( $ch );
        }
        //var_dump($result);exit
	}

    // Send a notification to single user who subscribed their device.
    // Example : Payment due date of a user from an application
    public function sendNotifications($data)
    {
        if(empty($data)){
            return false;
        }
        
        $payload = array();
        $user_device = DB::table("user_fcm_token")->where("user_id", $data['user_id'])->orderBy("id","desc")->first();
        if(empty($user_device)){
            return false;
        }

        if($user_device->token){
            $payload = array(
                'to'=> $user_device->token,   // one device
                'notification'=>array(
                    'title'=>$data['title'],
                    'body'=>$data['body'],
                    'sound'=>'default'
                  ),
                'data'=>array(
                    'type' => $data['type'],
                    'id' => $data['id']
                    )
            );
        

            $url = FCM_CONFIG["SERVER_SEND_URL"];
            $headers = array(
              'Authorization:key='.FCM_CONFIG["SERVER_KEY"],
              'Content-Type: application/json'
            );

            $ch = curl_init();
            curl_setopt( $ch,CURLOPT_URL, $url);
            curl_setopt( $ch,CURLOPT_POST, true );
            curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
            curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
            curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
            curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $payload ) );
            $result = curl_exec($ch );
            curl_close( $ch );
        }
        //var_dump($result);exit;

    }

}