<?php

include('functions.php');

$coins = coins();
$queued = coins();
$coins_orders = $coins;
$coinnames = $coins;
$fee_btc = current_fee();
$fee_mult_btc = (100-$fee_btc)*.01;

$coinrates = current_rates();

?>

<!doctype html>
<html>
	<head>
		<title>Currency Trade Watch</title>
		<link href='http://fonts.googleapis.com/css?family=Patrick+Hand|Bad+Script|Aladin|Shadows+Into+Light+Two' rel='stylesheet' type='text/css'>
		<link href='styles.css' rel='stylesheet' type='text/css'>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script> 
	</head>
	<body>
	<div id="bg"></div>
	<div id="wrapper">

<h2> The Numbers </h2>
<table id="xchangerates">
<tr><td> Exchange Rates </td></td>
<?php
	$count=1;
foreach ( $coinrates as $xchng => $xrate ) {
			$xview = $xrate.' '.str_replace("_", "/", $xchng);
			if($count == 1){echo '<tr>';}
			echo '<td title="'.$xview.'">';
			echo $xchng.': '.$xrate;
			echo "</td>";
			if($count == 3){$count=0; echo "</tr>";}
			$count++;
}
?>
</table>

<br><br>

<table id="openfunds">
<?php
echo '<tr><td> Open Funds </td></tr>';
$result = btce_query("getInfo");

$trys = 0;
while($result['success'] == 0 and $trys < 5){
	$result = btce_query("getInfo");
	$trys++;
}

echo "<!--".print_r($result, true)."-->";
if($result['success'] == 0){ //if error
	echo '<tr><td>'.$result['error'].'</td></tr>';
	//if($result['success']['error'] == 'no orders'\
	}else{
	$count=1;
	$estfunds = 0;
foreach ($result["return"]["funds"] as $coin => $value) {if(!is_numeric($coin)){
			if($coin == "btc"){$estrate = $coinrates['btc_usd'];}
			elseif($coin == "nvc"){$estrate = $coinrates['btc_usd']*$coinrates['nvc_btc'];}
			elseif($coin == "rur"){$estrate = 1/$coinrates['usd_rur'];}
			elseif($coin == "eur"){$estrate = $coinrates['eur_usd'];}
			elseif($coin == "ltc"){$estrate = $coinrates['ltc_usd'];}
			elseif($coin == "trc"){$estrate = $coinrates['btc_usd']*$coinrates['trc_btc'];}
			elseif($coin == "nmc"){$estrate = $coinrates['btc_usd']*$coinrates['nmc_btc'];}
			elseif($coin == "nmc"){$estrate = $coinrates['btc_usd']*$coinrates['nmc_btc'];}
			elseif($coin == "ppc"){$estrate = $coinrates['btc_usd']*$coinrates['ppc_btc'];}
			elseif($coin == "ftc"){$estrate = $coinrates['btc_usd']*$coinrates['ftc_btc'];}
			elseif($coin == "cnc"){$estrate = $coinrates['btc_usd']*$coinrates['cnc_btc'];}
			elseif($coin == "usd"){$estrate = 1;}
			else{$estrate = 0;}
			$estvalue = $value*$estrate;
			$estfunds += $estvalue;
			if($count == 1){echo '<tr>';}
			echo '<td title="'.$value.' '.$coin.'">';
			echo '<span class="coinvalue">'.round($value, 2 ).'</span> <span class="coinname"> '.$coin.'</span> <span>(&#36;'.round($estvalue, 2 ).')</span>';
			if($count == 3){$count=0; echo "</td></tr>";}else{echo "</td>";}
			
			$count++;
			}}
			unset($count);
			}

echo "</table>";
echo "<b>Estimated value: ".$estfunds."</b>";




echo '<h2> Transactions </h2>
		<div id="transactions" >
		<h3> All transactions </h3>
		<div class="datatable1" id="alltransactions">';
echo '<table>';
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
	$pair = explode("_",$transact['pair']);
	$pair[2] = $pair[1]." / ".$pair[0];
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
echo "</table>";
echo"	</div><br><br>";

echo' <div>';

foreach($exchanges as $xchange => $value) {
echo '<div class="halfsize"><h4>'.$xchange.'</h4>';
recenttrades($xchange, $transhistory);
echo '</div>';
}

echo' </div>';

?>
<br><br>
<div>
<script>
(function() {
  var tradesAPI = "https://btc-e.com/api/2/btc_usd/trades";
  $.getJSON( tradesAPI )
  .done(function( data ) {
	$.each( data.items, function( i, item ) {
	});
  });
})();
</script>
</div>
<?php


echo "<h2> Orders in Queue </h2>";
echo '<div id="queue" class="datatable1">';
$exchanges = exchanges();

echo '<table>';

$count = 1;

$result_queue = btce_query("OrderList");

$trys = 0;
while($result_queue['success'] == 0 and $trys < 5){
	$result_queue = btce_query("OrderList");
	$trys++;
}

echo "<!--"; print_r($result_queue); echo "-->";
if($result_queue['success'] == 0) //if error
	{
	echo '<tr><td colspan="8">'.$result_queue['error'].'</td></tr>';
	}else{
foreach($result_queue["return"] as $transact)
		{
	$pair = $transact['pair'];
	$pair = explode("_",$transact['pair']);
	$pair[2] = $pair[1]." / ".$pair[0];
	if ($transact['type'] == "buy"){
			$net_coef = "1";
			$units[0] = $pair[0];
			$units[1] = $pair[1];
		}else{
			$net_coef = "-1";
			$units[0] = $pair[0];
			$units[1] = $pair[1];
		}
	if($transact['type'] == 'buy'){
		echo '<tr class="buy"><td class="human">';
		echo '<span class="a1">Buying </span> <span class="b1">'.$transact['amount'].' '.$units[0].'</span>';
		echo ' for <span class="b1 b2">'.round($transact['amount']*$transact['rate'], 5 ).' '.$units[1].'</span>';
		$exchanges[$transact['pair']][$units[1]] += $transact['amount']*$transact['rate'];		
		$queued[$units[1]] += $transact['amount']*$transact['rate'];
	}elseif($transact['type'] == 'sell'){
		echo '<tr class="sell"><td class="human">';
		echo '<span class="a2">Selling </span> <span class="b1 b2">'.$transact['amount'].' '.$units[0].'</span>';
		echo ' for <span class="b1">'.round($transact['amount']*$transact['rate'], 5 ).' '.$units[1].'</span>';
		$queued[$units[0]] += $transact['amount'];
		$exchanges[$transact['pair']][$units[0]] += $transact['amount'];
	}
	echo ' at a rate of <span class="c">'.$transact['rate'].' '.$pair[2].'</span>.';
	echo "</td>";
	echo "</tr>";
	$count++;
		}}
echo '</table>';

echo '</div>';

//print_r($queued);

$i=0;
echo '<div><table class="col1-hilight">';
?>
<tr><td>Exchange</td><td>Sales</td><td>Buys</td></tr>
<?php
foreach($exchanges as $exchange => $val){
	echo "<tr><td>";
	echo $exchange;
	echo "</td>";
		foreach($val as $coin => $value){
			echo "<td>";
			echo	$coin.": ".$value.'<br />';
			echo "</td>";
		}
	echo "</tr>";
	}
echo "</table></div>";

echo "<br><br>";

echo "<table>";
$count=1;
$estfunds2 = 0;
foreach ($queued as $coin => $value) {if(!is_numeric($coin)){
			if($count == 1){echo "<tr>";}
			if($coin == "btc"){$estrate = $coinrates['btc_usd'];}
			elseif($coin == "nvc"){$estrate = $coinrates['btc_usd']*$coinrates['nvc_btc'];}
			elseif($coin == "rur"){$estrate = 1/$coinrates['usd_rur'];}
			elseif($coin == "eur"){$estrate = $coinrates['eur_usd'];}
			elseif($coin == "ltc"){$estrate = $coinrates['ltc_usd'];}
			elseif($coin == "trc"){$estrate = $coinrates['btc_usd']*$coinrates['trc_btc'];}
			elseif($coin == "nmc"){$estrate = $coinrates['btc_usd']*$coinrates['nmc_btc'];}
			elseif($coin == "ppc"){$estrate = $coinrates['btc_usd']*$coinrates['ppc_btc'];}
			elseif($coin == "ftc"){$estrate = $coinrates['btc_usd']*$coinrates['ftc_btc'];}
			elseif($coin == "cnc"){$estrate = $coinrates['btc_usd']*$coinrates['cnc_btc'];}
			else{$estrate = 1;}
			$estvalue = $value*$estrate;
			$estfunds2 += $estvalue;
			echo '<td><span title="'.$value.'">'.round($value, 5 ).' '.$coin.'</span> <span>('.$estvalue.')</span></td>';
			if($count == 3){$count=0; echo "</tr>";}
			
			$count++;
			}}
			unset($count);

echo "</table>";


echo "<b>Estimated value: ".$estfunds2."</b>";


echo "<h2>Estimated total w/ funds:  </h2>";

echo "<table>";
echo "<tr><td>".($estfunds2+$estfunds)." USD</td></tr>";
echo "<tr><td>".(($estfunds2+$estfunds)/$coinrates['btc_usd'])." BTC</td></tr>";
echo "</table>";


echo "<h2>Possible Deals to be Made</h2>";

echo "Current fee: ".$fee_btc;

echo "<table>";

echo "<tr><td>BTC->USD</td><td> 1 BTC-> ".$coinrates['btc_usd']*$fee_mult_btc." USD</td></tr>";

$btc_ltc = (1/$coinrates['ltc_btc'])*$fee_mult_btc;
$btc_ltc_usd = ($btc_ltc*$coinrates['ltc_usd'])*$fee_mult_btc;
echo "<tr><td>BTC->LTC->USD</td><td> 1 BTC-> ".$btc_ltc." LTC -> ".$btc_ltc_usd." USD</td></tr>";

$btc_eur_usd = $coinrates['eur_usd']*$coinrates['btc_eur']*$fee_mult_btc*$fee_mult_btc;
echo "<tr><td>BTC->EUR->USD</td><td> 1 BTC-> ".$coinrates['btc_eur']*$fee_mult_btc." EUR -> ".$btc_eur_usd." USD</td></tr>";

$usd_ltc_btc = (($coinrates['btc_usd']*$fee_mult_btc)/$coinrates['ltc_usd'])/$btc_ltc;
$usd_ltc_btc_usd = $usd_ltc_btc*($coinrates['btc_usd']*$fee_mult_btc);
echo "<tr><td>USD->LTC->BTC->USD</td><td>".$coinrates['btc_usd']*$fee_mult_btc." USD -> ".$usd_ltc_btc." BTC -> ".$usd_ltc_btc_usd."</td></tr>";


echo "</table>";






?>
</div>
</body>
</html>