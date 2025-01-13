<?
	$handle = fopen('application.log', 'r');
	
	$requestsToday = 0;
	$requestsYesterday = 0;
	
	if ($handle) {
		
		$today = date('Y-m-d');
		$yesterday = date('Y-m-d', strtotime('-1 days'));
		
    	while (($line = fgets($handle)) !== false) {
    	  if(strstr($line, $today)) {
          	$requestsToday++;	
    	  } else if (strstr($line, $yesterday)) {
    	  	$requestsYesterday++;
    	  }
    	}

    	fclose($handle);
    	
    	$diff = $requestsToday - $requestsYesterday;
    	
    	if($requestsYesterday > 0) {
    		$change = round(($diff / $requestsYesterday) * 100, 0);
    	} else {
    		$change = 100;
    	}

	} else {
    	
		$requests = 0;
		
	} 
?>
<tile>
  <visual>
    <binding template="TileSquareText03">
      <text id="1">UV radiation now</text>
      <text id="2"><?=$requestsToday?> requests</text>
      <text id="3"><?=($change > 0) ? '+' : ''?><?=$change?> % from yesterday</text>
    </binding>  
  </visual>
</tile>