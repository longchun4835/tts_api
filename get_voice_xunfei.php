<?php
class tts_test {
	function tocurl($url, $header, $content){
		$ch = curl_init();
		if(substr($url,0,5)=='https'){
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);  // 从证书中检查SSL加密算法是否存在
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($content));
		$response = curl_exec($ch);
		$error=curl_error($ch);
		//var_dump($error);
		if($error){
			die($error);
		}
		$header = curl_getinfo($ch);

		curl_close($ch);
		$data = array('header' => $header,'body' => $response);
		return $data;
	}
	function xfyun() {
		$url = 'http://api.xfyun.cn/v1/service/v1/tts';
		$appid = '5b3f0826';
		$apikey = 'a37146e4930dfeb795d4ee021e5870d9';
		$param = array (
			'aue' => 'lame',
			'voice_name' => 'xiaoyan',
			'speed' => "30",
		);
		$time = (string)time();
		$xparam = base64_encode(json_encode(($param)));
		$checksum = md5($apikey.$time.$xparam);
		$header = array(
			'X-CurTime:'.$time,
			'X-Param:'.$xparam,
			'X-Appid:'.$appid,
			'X-CheckSum:'.$checksum,
			'X-Real-Ip:101.231.137.69',
			'Content-Type:application/x-www-form-urlencoded; charset=utf-8'
		);
		$content = array(
			'text' => $_GET['t'],
		);
		$response = $this->tocurl($url, $header, $content);
		$header = $response['header'];
		$filename = '';
		if($header['content_type'] == 'audio/mpeg'){
			$filename.= 'ceshi.wav';
			$res = file_put_contents($filename, $response['body']);
			header('Content-Type:audio/mpeg');
			echo $response['body'];
		}else{
			// $filename.= $time.'.wav';
			// $res = file_put_contents($filename, $response['body']);
			// var_dump($res);
			header('Content-Type:audio/wav');
			echo $response['body'];
		}

	}
}
$a = new tts_test();
$a->xfyun();

?>
