<?php 

namespace MobilePushNotifications;

class iOSPushNotifications {

	var $connection;

	public function connection_start($apnsCert, $prod = false) {

		$apnsHost = 'gateway.sandbox.push.apple.com';
		if ($prod) {
			$apnsHost = 'gateway.push.apple.com';
		}
		
		$apnsPort = 2195;

		$streamContext = stream_context_create();
		stream_context_set_option($streamContext, 'ssl', 'local_cert', $apnsCert);
		//echo 'Cert Path: '.$apnsCert."\n";
		//echo 'ssl://' . $apnsHost . ':' . $apnsPort."\n";
		$errorString = null;
		$this->connection = stream_socket_client('ssl://' . $apnsHost . ':' . $apnsPort, $error, $errorString, 60, STREAM_CLIENT_CONNECT, $streamContext);
		
		//print_r($error);
		//print_r($errorString);
		
	}

	public function push($token, $message, $extraInfo = array(), $badge = 1, $sound = 'f.aif') {

		$payload = $extraInfo;

		$payload['aps'] = array('alert' => $message, 'badge' => $badge, 'sound' => $sound);

		//echo "Sendint to: ".$token."\n";
		
		$output = json_encode($payload);
		$token = pack('H*', str_replace(' ', '', $token));
		$apnsMessage = chr(0) . chr(0) . chr(32) . $token . chr(0) . chr(strlen($output)) . $output;
		
//		echo "Writing: ".$apnsMessage."\n";

		$len = @fwrite($this->connection, $apnsMessage);
		//$len = strlen($output);
		return $len;
	}

	public function connection_end() {
		@socket_close($this->connection);
		fclose($this->connection);
	}



}