<?php

function exchanges() {return array("btc_usd" => array("btc" => 0 ,"usd" => 0 ),"btc_rur" => array("btc" => 0 ,"rur" => 0 ),"btc_eur" => array("btc" => 0 ,"eur" => 0 ),"ltc_btc" => array("ltc" => 0 ,"btc" => 0) ,"ltc_usd" => array("ltc" => 0 ,"usd" => 0 ),"ltc_rur" => array("ltc" => 0 ,"rur" => 0) ,"nmc_btc" => array("nmc" => 0 ,"btc" => 0 ),"usd_rur" => array("usd" => 0 ,"rur" => 0 ),"eur_usd" => array("eur" => 0 ,"usd" => 0 ),"nvc_btc" => array("nvc" => 0 ,"btc" => 0 ),"trc_btc" => array("trc" => 0 ,"btc" => 0 ),"ppc_btc" => array("ppc" => 0 ,"btc" => 0),"ftc_btc" => array("ftc" => 0 ,"btc" => 0),"cnc_btc" => array("cnc" => 0 ,"btc" => 0));}


function btce_query($method, array $req = array()) {
	// API settings
	
	include('apikey.php'); 

	$req['method'] = $method;
	$mt = explode(' ', microtime());
	$req['nonce'] = $mt[1];
	
	// generate the POST data string
	$post_data = http_build_query($req, '', '&');

	$sign = hash_hmac("sha512", $post_data, $secret);

	// generate the extra headers
	$headers = array(
		'Sign: '.$sign,
		'Key: '.$key,
	);

	// our curl handle (initialize if required)
	static $ch = null;
	if (is_null($ch)) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; BTCE PHP client; '.php_uname('s').'; PHP/'.phpversion().')');
	}
	curl_setopt($ch, CURLOPT_URL, 'https://btc-e.com/tapi/');
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

	// run the query
	$res = curl_exec($ch);
	if ($res === false) throw new Exception('Could not get reply: '.curl_error($ch));
	$dec = json_decode($res, true);
	if (!$dec) throw new Exception('Invalid data received, please make sure connection is working and requested API exists');
	return $dec;
}

function current_rates() {
        // create curl resource
        $ch = curl_init();

        // set url
        curl_setopt($ch, CURLOPT_URL, "https://btc-e.com/exchange");

        //limit the input (to a save a tiny bit of bandwith) to a bit past where the data generally lies
        curl_setopt($ch, CURLOPT_RANGE, 6500);

        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);


        //change the UA to spoof IE7.
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)');

        // $output contains the output string
        $output = curl_exec($ch);

        // close curl resource to free up system resources
        curl_close($ch);     
		
		
		$output = explode("<ul class='pairs'>", $output);
		$output = explode("</ul>", $output[1]);
		$output = explode("<li", $output[0]);
		
		//print_r($output);
		
		$i = 0;
		foreach ($output as $value) {if (!$i==0){
				$values = explode("'>", $value);
				$values = explode("<br/>", $values[1]);
				$values = strtolower($values[0]);
				$values = str_replace("/","_",$values);
				$ex = explode("'>", $value);
				$val = explode("</span>", $ex[2]);
				$val = $val[0];
				$exchange[$values] = $val;
				//echo $i;
			}
			$i++;
			}
		return($exchange);
		
		//echo "<br>".$exchange['usd_btc'];
		}

		/*
function allthedata() {
        // create curl resource
		$exchanges = array("btc_usd","btc_rur","btc_eur","ltc_btc","ltc_usd","ltc_rur","nmc_btc","usd_rur","eur_usd","nvc_btc","trc_btc","ppc_btc","ftc_btc","cnc_btc")
        
		foreach($exchanges as $coinex){
		
		$ch = curl_init();
        // set url
        curl_setopt($ch, CURLOPT_URL, "https://btc-e.com/exchange/".$coinex );

        //limit the input (to a save a tiny bit of bandwith) to a bit past where the data generally lies        curl_setopt($ch, CURLOPT_RANGE, 6500);

        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);


        //change the UA to spoof IE7.
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)');

        // $output contains the output string
        $output = curl_exec($ch);

        // close curl resource to free up system resources
        curl_close($ch);     
		
		
		$output = explode("<script type='text/javascript' src='https://www.google.com/jsapi'></script>", $output);
		$output = explode("</script>", $output[1]);
		$output = "<script type='text/javascript' src='https://www.google.com/jsapi'></script>".$output[0]."</script>"  ;
		
		//print_r($output);
		
		$i = 0;
		foreach ($output as $value) {if (!$i==0){
				$values = explode("'>", $value);
				$values = explode("<br/>", $values[1]);
				$values = strtolower($values[0]);
				$values = str_replace("/","_",$values);
				$ex = explode("'>", $value);
				$val = explode("</span>", $ex[2]);
				$val = $val[0];
				$exchange[$values] = $val;
				//echo $i;
			}
			$i++;
			}
		}
		return($exchange);
		
		//echo "<br>".$exchange['usd_btc'];
		} */

function current_fee() {
        // create curl resource
        $ch = curl_init();

        // set url
        curl_setopt($ch, CURLOPT_URL, "https://btc-e.com/api/2/btc_usd/fee");

        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);


        //change the UA to spoof IE7.
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)');

        // $output contains the output string
        $output = curl_exec($ch);

        // close curl resource to free up system resources
        curl_close($ch);     
		
		
		$output = explode(":", $output);
		$output = explode("}", $output[1]);
		$output = $output[0];
		
		return($output);
		
		}

function coins()	{array("ltc" => 0 ,"btc" => 0 ,"nmc" => 0 ,"nvc" => 0 ,"rur" => 0 ,"usd" => 0 ,"ppc" => 0 ,"eur" => 0 ,"trc" => 0 ,"ftc" => 0,"cnc" => 0 );}

function orderbookkeeping() {


}


function recenttrades( $exchange = "all", $transhistory = 0 )
	{
	
		echo '<div class="dataview datatable2"><table>';
		$count = 1;
		
		/*if ( $transhistory == 0 ){
		$transhistory = btce_query("TradeHistory", array("order" => "DESC"));
		$trys = 0;
		while($transhistory['success'] == 0 and $trys < 25){
			$transhistory = btce_query("TradeHistory");
			$trys++;
		}}*/
		
		$exchanges = exchanges();
		
		foreach($transhistory["return"] as $transact){
			
			$pair = $transact['pair'];
			if( $exchange == "all" or $exchange == $pair ){
			
			if ($lasttransacttype == $transact['type'] and $lasttransacttype != ""){
			
			
			/// do work here
			
			
				}else{
			
			echo '<tr class"'.$transact['type'].'">';
			
			}
			
			$lasttransacttype = "";
			
			//prepare pair array
				$pair = explode("_",$transact['pair']);
				$pair[2] = $pair[0]."/".$pair[1];
				$units[0] = $pair[0];
				$units[1] = $pair[1];
			//end prepare pair array
			
				if($transact['type'] == 'buy'){
						echo '<tr class="buy">';
						echo '<td class="a1">Bought </td><td class="b1">'.round($transact['amount'].$units[0], 3 ).'</td>';
						echo '<td class="b1 b2">'.round($transact['amount']*$transact['rate'], 3 ).$units[1].'</td>';
						echo '<td class="">@['.$transact['rate'].'<sub>'.$pair[2].'</sub>]</td>';
					}
					elseif($transact['type'] == 'sell')
					{
						echo '<tr class="sell">';
						echo '<td class="a2">Sold </td> <td class="b1 b2">'.round($transact['amount'], 3 ).$units[0].'</td>';
						echo '<td class="b1">'.round($transact['amount']*$transact['rate'], 3 ).$units[1].'</td>';
						echo '<td class="">@['.$transact['rate'].'<sub>'.$pair[2].'</sub>]</td>';
					}
			echo "</td>";
			echo "</tr>";
			echo "<tr>";
			echo '<td colspan="4">';
			echo '<div class="smallrate">'.date('Y-m-d \| H:i:s', $transact['timestamp']).'</div>';
			echo "</td>";
			echo "</tr>";
			$count++;
			}
			$lasttransacttype = $transact['type'];
			}
		echo "</table></div>";
	
	}


function recenttrades_old( $exchange = "all", $transhistory = 0 )
	{
	
		echo '<div class="dataview datatable2"><table>';
		$count = 1;
		
		/*if ( $transhistory == 0 ){
		$transhistory = btce_query("TradeHistory", array("order" => "DESC"));
		$trys = 0;
		while($transhistory['success'] == 0 and $trys < 25){
			$transhistory = btce_query("TradeHistory");
			$trys++;
		}}*/
		
		$exchanges = exchanges();

		foreach($transhistory["return"] as $transact){
			//print_r($transact); echo "<br>";
			$pair = $transact['pair'];
			if( $exchange == "all" or $exchange == $pair ){
			
			$pair = explode("_",$transact['pair']);
			$pair[2] = $pair[0]."/".$pair[1];
			$units[0] = $pair[0];
			$units[1] = $pair[1];
			if ($transact['type'] == "buy"){
					$net_coef = "1";
				}else{
					$net_coef = "-1";
				}
			
			$pairs[$transact['pair']] = $coins[$transact['pair']] + $transact['amount']*$transact['rate'];
			$coins[$units[0]] = $coins[$units[0]] + $net_coef*$transact['amount'];
			$coins[$units[1]] = $coins[$units[1]] + (-1)*$net_coef*$transact['amount']*$transact['rate'];
			if(!isset($pairs[$transact['pair']][$units[0]])){$pairs[$transact['pair']][$units[0]] = 0;}
			if(!isset($pairs[$transact['pair']][$units[1]])){$pairs[$transact['pair']][$units[1]] = 0;}
			
				if($transact['type'] == 'buy'){
						echo '<tr class="buy">';
						echo '<td class="a1">Bought </td><td class="b1">'.round($transact['amount'].$units[0], 3 ).'</td>';
						echo '<td class="b1 b2">'.round($transact['amount']*$transact['rate'], 3 ).$units[1].'</td>';
						echo '<td class="">@['.$transact['rate'].'<sub>'.$pair[2].'</sub>]</td>';
						$coins[$units[1]] = $coinss[$units[1]] + $transact['amount']*$transact['rate'];
						$pairs[$transact['pair']][$units[1]] += $transact['amount']*$transact['rate']; 
						echo '<!--'.$pairs[$transact['pair']][$units[0]].'-->';
					}
					elseif($transact['type'] == 'sell')
					{
						echo '<tr class="sell">';
						echo '<td class="a2">Sold </td> <td class="b1 b2">'.round($transact['amount'], 3 ).$units[0].'</td>';
						echo '<td class="b1">'.round($transact['amount']*$transact['rate'], 3 ).$units[1].'</td>';
						echo '<td class="">@['.$transact['rate'].'<sub>'.$pair[2].'</sub>]</td>';
						$coins[$units[0]] = $coins[$units[0]] + $transact['amount'];
						$pairs[$transact['pair']][$units[1]] += $transact['amount']; 
						echo '<!--'.$pairs[$transact['pair']][$units[1]].'-->';
					}
			echo "</td>";
			echo "</tr>";
			echo "<tr>";
			echo '<td colspan="4">';
			echo '<div class="smallrate">'.date('Y-m-d \| H:i:s', $transact['timestamp']).'</div>';
			echo "</td>";
			echo "</tr>";
			$count++;
			}
			}
		echo "</table></div>";
	
	}
function recenttrades_human( $exchange = "all" )
	{
	
		echo '<div class="dataview"><table>';
		$count = 1;
		$transhistory = btce_query("TradeHistory", array("order" => "DESC"));
		$trys = 0;
		while($transhistory['success'] == 0 and $trys < 25){
			$transhistory = btce_query("TradeHistory");
			$trys++;
		}
		$exchanges = exchanges();

		foreach($transhistory["return"] as $transact){
			//print_r($transact); echo "<br>";
			$pair = $transact['pair'];
			if( $exchange == "all" or $exchange == $pair ){
			
			$pair = explode("_",$transact['pair']);
			$pair[2] = $pair[0]." / ".$pair[1];
			$units[0] = $pair[0];
			$units[1] = $pair[1];
			if ($transact['type'] == "buy"){
					$net_coef = "1";
				}else{
					$net_coef = "-1";
				}
			
			$pairs[$transact['pair']] = $coins[$transact['pair']] + $transact['amount']*$transact['rate'];
			$coins[$units[0]] = $coins[$units[0]] + $net_coef*$transact['amount'];
			$coins[$units[1]] = $coins[$units[1]] + (-1)*$net_coef*$transact['amount']*$transact['rate'];
			if(!isset($pairs[$transact['pair']][$units[0]])){$pairs[$transact['pair']][$units[0]] = 0;}
			if(!isset($pairs[$transact['pair']][$units[1]])){$pairs[$transact['pair']][$units[1]] = 0;}
			
				if($transact['type'] == 'buy'){
						echo '<tr class="buy"><td class="human"><span class="count">'.$count."</span>";
						echo '<span class="a1">Bought </span> <span class="b1">'.$transact['amount'].' '.$units[0].'</span>';
						echo ' for <span class="b1 b2">'.round($transact['amount']*$transact['rate'], 5 ).' '.$units[1].'</span>';
						$coins[$units[1]] = $coinss[$units[1]] + $transact['amount']*$transact['rate'];
						$pairs[$transact['pair']][$units[1]] += $transact['amount']*$transact['rate']; 
						echo '<!--'.$pairs[$transact['pair']][$units[0]].'-->';
					}
					elseif($transact['type'] == 'sell')
					{
						echo '<tr class="sell"><td colspan="9" class="human"><span class="count">'.$count."</span>";
						echo '<span class="a2">Sold </span> <span class="b1 b2">'.$transact['amount'].' '.$units[0].'</span>';
						echo ' for <span class="b1">'.round($transact['amount']*$transact['rate'], 5 ).' '.$units[1].'</span>';
						$coins[$units[0]] = $coins[$units[0]] + $transact['amount'];
						$pairs[$transact['pair']][$units[1]] += $transact['amount']; 
						echo '<!--'.$pairs[$transact['pair']][$units[1]].'-->';
					}
			echo ' at a rate of <span class="c">'.$transact['rate'].' '.$pair[2].'</span>';
			echo ' <div class="date"> on '.date('Y-m-d \<\b\r\>\a\t H:i:s', $transact['timestamp']).'</div>';
			echo "</td>";
			echo "</tr>";
			$count++;
			}
			}
		echo "</table></div>";
	
	}

?>