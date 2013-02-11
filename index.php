<--!
_______ ______ __  __ _____             _____                 _
|__   __|  ____|  \/  |  __ \           / ____|               | |
| |  | |__  | \  / | |__) |__ _ __  | |  __ _ __ __ _ _ __ | |__
| |  |  __| | |\/| |  ___/ _ \ '__| | | |_ | '__/ _` | '_ \| '_ \
| |  | |____| |  | | |  |  __/ |    | |__| | | | (_| | |_) | | | |
|_|  |______|_|  |_|_|   \___|_|     \_____|_|  \__,_| .__/|_| |_|
| |
|

by Sergi Ambel (2013)
-->

<html>
	<head>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
		<script src="http://code.highcharts.com/stock/highstock.js"></script>
		<script src="http://code.highcharts.com/stock/modules/exporting.js"></script>
		<script src="http://ibleu.googlecode.com/svn-history/r10/trunk/js/highcharts.theme.gray.js"></script>

		<?php
			$local_time_offset = date('Z');
			$filenames = scandir( getcwd() );

			$csv_data_reads = array();
			foreach( $filenames as $filename )
			{
				if ( 'csv' == strtolower( pathinfo($filename, PATHINFO_EXTENSION) ) )
				{
					$csv_data_reads = array_merge( $csv_data_reads, file( $filename ) );
				}
			}

			$data_tuples = array();
			foreach( $csv_data_reads as $read )
			{
				if ( preg_match( "/^\d+\,(\d+\,\d+)\,(.+)$/", $read, $matches ) )
				{
					$timestamp = strtotime( preg_replace( "/^([^\/]+)\/([^\/]+)\/(.*)/", "$2/$1/$3", $matches[2] ) );
					// JS Timestamp is expected in miliseconds:
					$timestamp = ( $timestamp + $local_time_offset )* 1000;

					$data_tuples[$timestamp] = '['.$timestamp.','.(real) str_replace( ',','.', $matches[1] ).']';
				}
				
			}
			asort( $data_tuples );

		?>
		<script type= "text/javascript">
			data = <?php echo "[".implode( ',', $data_tuples )."]"?>
		</script>
		<script type= "text/javascript">

			$(function() {

				// Create the chart
				window.chart = new Highcharts.StockChart({
						chart : {
							renderTo : 'container'
						},

						rangeSelector : {
							selected : 0,
							buttons: [{
								type: 'day',
								count: 1,
								text: '1d'
							}, {
								type: 'day',
								count: 7,
								text: '7d'
							}, {
								type: 'month',
								count: 1,
								text: '1m'
							}, {
								type: 'month',
								count: 3,
								text: '3m'
							}, {
								type: 'month',
								count: 6,
								text: '6m'
							}, {
								type: 'all',
								text: 'All'
							}]
						},
						title : {
							text : 'TEMPer records'
						},
						series : [{
							name : 'Celcius',
							data : data,
							tooltip: {
								valueDecimals: 2
							}
						}]
				});
			});

		</script>
	</head>
	<body >
		<div id="container" style="height: 100%; min-width: 500px"></div>
	</body>
</html>