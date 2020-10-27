<?php

$yesterday_raw_csv = array_map('str_getcsv', file('yesterday.csv'));
$today_csv = array_map('str_getcsv', file('today.csv'));

foreach ( $yesterday_raw_csv as $key => $value) {
	$yesterday_csv[$value[0]] = $yesterday_raw_csv[$key];
}

print '<pre>';
//print_r( $yesterday_csv );
//print_r($today_csv);

//exit;
$calculation  = [];

if ( count( $yesterday_csv ) === count( $today_csv ) ) {
	foreach ($today_csv as $key => $value) {
		if ( ! empty( $value[0] ) && $key > 1 ) {

			//print $value[0];
			//print_r($yesterday_csv[$value[0]]);
			//Todays Calculation
			$calculation[$value[0]]['today']['high']  = format( str_replace( ',', '', trim( $value[2] ) ) );
			$calculation[$value[0]]['today']['low']   = format( str_replace( ',', '', trim( $value[3] ) ) );
			$calculation[$value[0]]['today']['close'] = format( str_replace( ',', '', trim( $value[4] ) ) );

			$calculation[$value[0]]['today']['r3'] = h3( $calculation[$value[0]]['today']['close'], $calculation[$value[0]]['today']['high'], $calculation[$value[0]]['today']['low'] );

			$calculation[$value[0]]['today']['r4'] = h4( $calculation[$value[0]]['today']['close'], $calculation[$value[0]]['today']['high'], $calculation[$value[0]]['today']['low'] );

			$calculation[$value[0]]['today']['r5'] = h5( $calculation[$value[0]]['today']['r4'], $calculation[$value[0]]['today']['r3'] );

			$calculation[$value[0]]['today']['r6'] = h6( $calculation[$value[0]]['today']['close'], $calculation[$value[0]]['today']['high'], $calculation[$value[0]]['today']['low'] );

			$calculation[$value[0]]['today']['l3'] = l3( $calculation[$value[0]]['today']['close'], $calculation[$value[0]]['today']['high'], $calculation[$value[0]]['today']['low'] );

			$calculation[$value[0]]['today']['l4'] = l4( $calculation[$value[0]]['today']['close'], $calculation[$value[0]]['today']['high'], $calculation[$value[0]]['today']['low'] );

			$calculation[$value[0]]['today']['l5'] = l5( $calculation[$value[0]]['today']['l4'], $calculation[$value[0]]['today']['l3'] );

			$calculation[$value[0]]['today']['l6'] = l6( $calculation[$value[0]]['today']['close'], $calculation[$value[0]]['today']['r6'] );

			//Yesterdays Calculation
			$calculation[$value[0]]['yesterday']['high']  = format( str_replace( ',', '', trim( $yesterday_csv[$value[0]][2] ) ) );
			$calculation[$value[0]]['yesterday']['low']   = format( str_replace( ',', '', trim( $yesterday_csv[$value[0]][3] ) ) );
			$calculation[$value[0]]['yesterday']['close'] = format( str_replace( ',', '', trim( $yesterday_csv[$value[0]][4] ) ) );

			$calculation[$value[0]]['yesterday']['r3'] = format( ( $calculation[$value[0]]['yesterday']['close'] + ( $calculation[$value[0]]['yesterday']['high'] - $calculation[$value[0]]['yesterday']['low'] ) * 0.275 ) );

			$calculation[$value[0]]['yesterday']['r4'] = format( ( $calculation[$value[0]]['yesterday']['close'] + ( $calculation[$value[0]]['yesterday']['high'] - $calculation[$value[0]]['yesterday']['low'] ) * 0.55 ) );

			$calculation[$value[0]]['yesterday']['l3'] = l3( $calculation[$value[0]]['yesterday']['close'], $calculation[$value[0]]['yesterday']['high'], $calculation[$value[0]]['yesterday']['low'] );

			$calculation[$value[0]]['yesterday']['l4'] = l4( $calculation[$value[0]]['yesterday']['close'], $calculation[$value[0]]['yesterday']['high'], $calculation[$value[0]]['yesterday']['low'] );
		}
    }
}

//print_r($calculation);
function h3( $close, $high, $low ) {
    return format( ( $close + ( $high - $low ) * 0.275 ) );
}

function h4(  $close, $high, $low ) {
    return format( ( $close + ( $high - $low ) * 0.55 ) );
}

function h5(  $r4, $r3 ) {
    return format( ( $r4 + ( $r4 - $r3 ) * 1.168 ) );
}

function h6(  $close, $high, $low ) {
    return format( ( $high/$low ) * $close );
}

function l3( $close, $high, $low ) {
    return format( ( $close - ( $high - $low ) * 0.275 ) );
}

function l4(  $close, $high, $low ) {
    return format( ( $close - ( $high - $low ) * 0.55 ) );
}

function l5(  $l4, $l3 ) {
    return format( ( $l4 - ( $l3 - $l4 ) * 1.168 ) );
}

function l6(  $close, $r6 ) {
    return format( $close - ( $r6 - $close ) );
}


function format( $value ) {
    return number_format( $value, 2, '.', '' );
}

?>

<table style="text-align: center;">
	<thead><h2>Camarilla Pivot Point</h2></thead>
	<tbody>
		<th>No</th>
		<th colspan="6">Symbol</th>
		<th colspan="6">H3</th>
		<th colspan="6">H4</th>
		<th colspan="6">H5</th>
		<th colspan="6">H6</th>
		<th colspan="6">L3</th>
		<th colspan="6">L4</th>
		<th colspan="6">L5</th>
		<th colspan="6">L6</th>
	<?php
		$count = 1;
		foreach ($calculation as $key => $value) {
			if ( $value['yesterday']['r3'] > $value['today']['r3'] && $value['yesterday']['l3']< $value['today']['l3'] ) {
	?>
		<tr>
			<td><?php print $count++; ?></td>
			<td colspan="6" style="font-weight: bold;"><?php print $key; ?></td>
			<td colspan="6" style="font-weight: bold;color: green;"><?php print $value['today']['r3']; ?></td>
			<td colspan="6" style="font-weight: bold;color: green;"><?php print $value['today']['r4']; ?></td>
			<td colspan="6" style="font-weight: bold;color: green;"><?php print $value['today']['r5']; ?></td>
			<td colspan="6" style="font-weight: bold;color: green;"><?php print $value['today']['r6']; ?></td>
			<td colspan="6" style="font-weight: bold;color: red;"><?php print $value['today']['l3']; ?></td>
			<td colspan="6" style="font-weight: bold;color: red;"><?php print $value['today']['l4']; ?></td>
			<td colspan="6" style="font-weight: bold;color: red;"><?php print $value['today']['l5']; ?></td>
			<td colspan="6" style="font-weight: bold;color: red;"><?php print $value['today']['l6']; ?></td>
		</tr>
	<?php
			}
		}
	?>
	</tbody>
</table>
