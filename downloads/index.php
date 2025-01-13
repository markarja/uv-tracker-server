<?
	$myfile = fopen('data.csv', 'r') or die('Unable to open file!');
	
	$i = 0;
	$downloads = 0;
	$countryToDownloads = array();
	
	while(!feof($myfile)) {
		$row = fgets($myfile);
		if($i > 0) {
			$parts = split(',', $row);
			$country = $parts[0];
			
			if(empty($countryToDownloads[$country])) {
				$countryToDownloads[$country] = $parts[2];
			} else {
				$countryToDownloads[$country] = $countryToDownloads[$country] + $parts[2];
			}
			
			$downloads = $downloads + $parts[2];
			
		}
		$i++;	
	}
	
	foreach ($countryToDownloads as $k => $v) {
		$countryToDownloads[$k] = round(($v / $downloads) * 100, 2);
	}
	
	
	fclose($myfile);
	
	$json = "[['Country', '% of all downloads']";
	
	foreach ($countryToDownloads as $k => $v) {
		$json = $json . ",['" . str_replace("'","",$k) . "'," . $v . "]";
	}
	
	$json = $json . "]";
	
	echo $json;
	
?>