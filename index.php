<?
    
    error_reporting(0);
	include_once 'util/class.Logger.php';
	define("FINLAND", 1);	
	define("AUSTRALIA", 2);	
	define("LOCATION_MAX_LENGTH", 25);		
	
	$coordinates = array(
		"adelaide" => array("-34.9286212", "138.5999594", 2), 
		"alice springs" => array("-23.7002104", "133.8806114", 2),
		"brisbane" => array("-27.4709331", "153.0235024", 2),
	    "canberra" => array("-35.2819998", "149.1286843", 2),
	    "casey" => array("-38.1105316", "145.2922335", 2), 
		"darwin" => array("-12.4628271", "130.8417772", 2),
	    "davis" => array("-68.5762470", "77.9696240", 2), //
		"kingston" => array("-42.9756751", "147.3094943", 2),
	    "macquarie island" => array("-54.6208115", "158.855615", 2),
	    "mawson" => array("-35.365", "149.094444", 2), 
		"melbourne" => array("-37.8142155", "144.9632307", 2),
		"newcastle" => array("-32.926696", "151.7788922", 2),
		"perth" => array("-31.9528536", "115.8573389", 2),
		"sydney" => array("-33.873651", "151.2068896", 2),
		"townsville" => array("-19.2576223", "146.8178787", 2),
		"Parainen Utö" => array("59.77909", "21.37479", 1),
		"Helsinki Kumpula" => array("60.20307", "24.96131", 1),
		"Jokioinen Jokioisten observatorio" => array("60.81401", "23.49761", 1),
		"Jyväskylä lentoasema" => array("62.39758", "25.67087", 1),
		"Sotkamo Kuolaniemi" => array("64.11197", "28.33639", 1),
		"Sodankyla Lapin ilmatieteellinen tutkimuskeskus" => array("67.36663", "26.62901", 1)
	);
	
	define("APIKEY", "dXYtdHJhY2tlci1pZA==");
	$endtime = "";
	
	if(isset($_GET['apikey']) && $_GET['apikey'] == APIKEY) {
		
		header('Content-Type: text/html; charset=utf-8');
		
		$jsonResult = "{}";
		
		$logger = new Logger('application.log');
			
		//$logger->info("REMOTE_ADDR: [" . $_SERVER['REMOTE_ADDR'] . "], REMOTE_HOST: [" . gethostbyaddr($_SERVER['REMOTE_ADDR']) . "], HTTP_USER_AGENT: [" . $_SERVER['HTTP_USER_AGENT'] ."], REQUEST_URI: [" . $_SERVER['REQUEST_URI'] . "]");
		
		$connection = mysqli_connect(
				'localhost',
				'np2902_uv',
				'Uv2015',
				'np2902_uv');
		mysqli_set_charset($connection, 'utf8');

		$remote_address = $_SERVER['REMOTE_ADDR'];
		$remote_host = gethostbyaddr($_SERVER['REMOTE_ADDR']);
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		$platform = extractPlatform($user_agent);
		$request_uri = $_SERVER['REQUEST_URI'];
		
		$request_uri_parts = explode('&', $request_uri);
		$parts = explode('=', $request_uri_parts[1]);
		$lat_lng = explode('%2C', $parts[1]);
		
		$parts = explode('=', $request_uri_parts[2]);
		$starttime = str_replace('Z', '', str_replace('T', ' ', (str_replace('%3A', ':', $parts[1]))));
		 
		$parts = explode('=', $request_uri_parts[3]);
		$endtime = str_replace('Z', '', str_replace('T', ' ', (str_replace('%3A', ':', $parts[1]))));
		
		if($remote_address != '' && !strstr($request_uri, 'country')) {
			$sql = "INSERT INTO request(remote_address,remote_host,http_user_agent,request_uri,platform,lat,lng,start_time, end_time) " .
					"VALUES('$remote_address','$remote_host','$user_agent','$request_uri','$platform','$lat_lng[0]','$lat_lng[1]','$starttime','$endtime')";
			mysqli_query($connection, $sql);		
		}
		
		$country = isset($_GET['country']) ? $_GET['country'] : 1; 
		
		$language = isset($_GET['language']) ? $_GET['language'] : 'en-us';

		if(isset($_GET['q'])) {
				
			//$_GET['q'] = "37.385574,-122.08205"; //Google's headquarters
			//$_GET['q'] = "37.323011,-122.032252"; //Apple's headquarters
			//$_GET['q'] = "47.669414,-122.123877"; //Microsoft's headquarters			
			//$_GET['q'] = "60.166628,24.943508"; //Helsinki, Finland
			//$_GET['q'] = "-35.281937,149.128894"; //Canberra
			//$_GET['q'] = "59.325117,18.071094"; //Stockholm
			//$_GET['q'] = "-27.4709331,153.0235024"; //Sydney
			//$_GET['q'] = "40.7127837,-74.0059413"; //New York
			
			$keys = array_keys($coordinates); 
			$min = 9999999999.0;
			$location = "";
			
			for($i = 0;$i < count($keys);$i++) {
				
				$q_ = explode(",", $_GET['q']);
				$distance = distance($q_[0], $q_[1], $coordinates[$keys[$i]][0], $coordinates[$keys[$i]][1], "K");

				if($distance < $min) {
					$min = $distance;
					$location = $keys[$i];
				}
			}
			
			/*$logger->info("Closest station:");
			$logger->info("Name : " . $location);
			$logger->info("Latitude : " . $coordinates[$location][0]);
			$logger->info("Longitude : " . $coordinates[$location][1]);
			$logger->info("Country : " . $coordinates[$location][2]);
			$logger->info("Distance : " . $min);*/
			
			if($min <= 100) {
			
				$source = "N/A";
				
				if($coordinates[$location][2] == FINLAND) {
					$obj = json_decode(fmiDataAsJson($_GET['starttime'], $_GET['endtime']));
					
					if($language == 'fi-fi') {
						$source = "Ilmatieteen laitos";
					} else if($language == 'sv-se') {
						$source = "Meteorologiska institutet";
					} else {
						$source = "Finnish Meteorological Instititue";
					}
					
				} else if($coordinates[$location][2] == AUSTRALIA) {
					$obj = json_decode(arpansaDataAsJson($coordinates));
					$source = "Australian Radiation Protection and Nuclear Safety Agency";
				}
				
				if(count($obj->data) == 1) {
					$result = '{"data":[{"index":"' . $obj->data[0]->index . '","date":"' . date("d-m-Y") . '","source":"' . $source . ', ' . $obj->data[$i]->name . ' (' . round($min) . ' km)"'; 
				}
				
				for($i = 0;$i < count($obj->data);$i++) {
					if($obj->data[$i]->latitude == $coordinates[$location][0] &&
					   $obj->data[$i]->longitude == $coordinates[$location][1]) {
						$result = '{"data":[{"index":"' . $obj->data[$i]->index /* '8.5 */ . '","date":"' . date("d-m-Y") . '","source":"' . $source . ', ' . $obj->data[$i]->name . ' (' . round($min) . ' km)"';
						break;    	
					}	
				}
				
				if($result == "") {
					
					//$url = "http://api.worldweatheronline.com/free/v2/weather.ashx?q=" . $_GET['q'] . "&format=json&num_of_days=1&key=9ece06a86ec543378da55986ca895";
                    $url = "http://api.worldweatheronline.com/premium/v1/weather.ashx?q=" . $_GET['q'] . "&format=json&num_of_days=1&key=31fc2b7c0eaf4f62ab7124633170206";
					$json = file_get_contents($url);
					$obj = json_decode($json);
					$source = 'World Weather Online';
					$uvIndex = getUVIndexWithSunriseSunsetEffect($obj, $endtime);
					if($uvIndex == -1) {
						$source = '';
						$uvIndex = 0;	
					} 
					$result = '{"data":[{"index":"' . $uvIndex . '","date":"' . $obj->data->weather[0]->date . '","source":"' . $source . '"';
						
				}
				
			} else {
			
				//$url = "http://api.worldweatheronline.com/free/v2/weather.ashx?q=" . $_GET['q'] . "&format=json&num_of_days=1&key=9ece06a86ec543378da55986ca895";
                $url = "http://api.worldweatheronline.com/premium/v1/weather.ashx?q=" . $_GET['q'] . "&format=json&num_of_days=1&key=31fc2b7c0eaf4f62ab7124633170206";
				$json = file_get_contents($url);
				$obj = json_decode($json);
				$source = 'World Weather Online';
				$uvIndex = getUVIndexWithSunriseSunsetEffect($obj, $endtime);
				if($uvIndex == -1) {
					$source = '';
					$uvIndex = 0;	
				} 
				$result = '{"data":[{"index":"' . $uvIndex . '","date":"' . $obj->data->weather[0]->date . '","source":"' . $source . '"';			
				
			}
			
			$url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=" . $_GET['q'] . "&result_type=locality&key=AIzaSyD8NMfNB4munfbFVU_cUoZU8FLK-nUQWg0";
			$json = file_get_contents($url);
			$obj = json_decode($json);
			
			$location_name = $obj->results[0]->formatted_address;
			
			if(strlen($location_name) > LOCATION_MAX_LENGTH) {
				$location_name = $obj->results[0]->address_components[0]->long_name . ", " . 
				$obj->results[0]->address_components[2]->long_name;
			}

			if(strlen($location_name) > LOCATION_MAX_LENGTH) {
				$location_name = $obj->results[0]->address_components[0]->long_name;
			}
			
			if(strlen($location_name) > LOCATION_MAX_LENGTH) {
				$location_name = substr($obj->results[0]->address_components[0]->long_name, 0, LOCATION_MAX_LENGTH - 3) . "...";
			}
		 
			$result = $result . ',"location":"' . $location_name . '"}]}';
			
			$jsonResult = $result;
		
	    } else if($country == FINLAND) {
			
			$jsonResult = fmiDataAsJson($_GET['starttime'], $_GET['endtime']);
			
		} else if($country == AUSTRALIA) {
			
			$jsonResult = arpansaDataAsJson($coordinates);
			
		}

		$jsonResultObject = json_decode($jsonResult);
		
		$uv_index = $jsonResultObject->data[0]->index;
		$data_source = $jsonResultObject->data[0]->source;
		$location = $jsonResultObject->data[0]->location;
		
		$sql = "INSERT INTO response(id, uv_index, data_source, location) " . 
			   "VALUES(LAST_INSERT_ID(),'$uv_index','$data_source','$location')";
		                
        if(!mysqli_query($connection, $sql)) {
        	$logger->error("A MySQL error occurred: '" . mysqli_error($connection) . "'");
        }
		
		mysqli_close($connection);
		
		echo $jsonResult;
				
	} else {
		
		header('HTTP/1.0 401 Unauthorized');
    	echo 'Unauthorized Request!';
    	exit;
		
	}
	
	function distance($lat1, $lon1, $lat2, $lon2, $unit) {
  		$theta = $lon1 - $lon2;

  		$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)); 

  		$dist = acos($dist);

  		$dist = rad2deg($dist);

  		$miles = $dist * 60 * 1.1515;

  		$unit = strtoupper($unit);

  		if ($unit == "K") {

    		return ($miles * 1.609344);

  		} else if ($unit == "N") {

      		return ($miles * 0.8684);

    	} else {

        	return $miles;

      	}
	}
	
	function fmiDataAsJson($start_time, $end_time) {
		$urlprefix = "http://data.fmi.fi/fmi-apikey/";
		$apikey = "49a0f22e-b58d-44f3-9e9d-65a49d9dca99";
		$urlsuffix = "/wfs?";
		$request = "request=getFeature";
		$storedquery = "&storedquery_id=fmi::observations::radiation::timevaluepair";
		$starttime = "&starttime=" . $start_time;
		$endtime = "&endtime=" . $end_time;
		$timestep = "&timestep=1";
		$parameters = "&parameters=UVB_U";
		
		$querystring = $urlprefix . $apikey . $urlsuffix . $request . $storedquery . $starttime . $endtime . $timestep . $parameters;
		
 	    try {
 	    	
			$xmldata = @file_get_contents($querystring);
			$xml = new SimpleXmlElement($xmldata);
			$namespaces = $xml->getDocNamespaces();
			$members = $xml->children($namespaces['wfs'], false);	
			$json = "";
			
			foreach($members as $member) {
				$observations = $member->children($namespaces['omso'], false);
				foreach($observations as $observation) {
					$properties =
						$observation->children($namespaces['om'], false);
					foreach($properties as $property) {
						if(trim($property->getName()) == 'featureOfInterest') {
							$spatialSamplingFeatures = $property->children($namespaces['sams'], false);
							foreach($spatialSamplingFeatures as $spatialSamplingFeature) {
								$shapes = $spatialSamplingFeature->children($namespaces['sams'], false);
								foreach($shapes as $shape) {
									if(trim($shape->getName()) == 'shape') {
										$points = $shape->children($namespaces['gml'], false);
										foreach($points as $point) {
											$lnglat = explode(' ', trim($point->pos));
											if($json == "") {
												$json = '{"data":[{"name":"' . $point->name . '","longitude":"' . $lnglat[1] . '","latitude":"' . $lnglat[0] . '",';
											} else {
												$json = $json . ',{"name":"' . $point->name . '","longitude":"' . $lnglat[1] . '", "latitude":"' . $lnglat[0] . '",';
											}
										}
									}
								}
							}
						} else if(trim($property->getName()) == 'result') {
							$sum = 0.0;
							$numberOfObservations = 0;
							$measurementTimeseries = $property->children($namespaces['wml2'], false);
							foreach($measurementTimeseries as $entry) {
								$points = $entry->children($namespaces['wml2'], false);
								foreach($points as $measurementTVP) {
									$timeValuePairs = $measurementTVP->children($namespaces['wml2'], false);
									foreach($timeValuePairs as $timeValuePair) {
										$sum = $sum + floatval($timeValuePair->value);	
										$numberOfObservations++;
									}
								}
							}
							$json = $json . '"index":"' . round($sum / $numberOfObservations, 1) . '", "observations" : "' . $numberOfObservations . '"}';
						}
					}		 
				}
			}
			
			$json = $json . "]}";
			
			return $json;
			
		} catch (Exception $e) {
 	    	//$logger->error($e);	
 	    	return '{"data":[{"name":"Observation data for Finland not available right now, please try again later.","longitude":"23.1009469","latitude":"61.0906683","index":"0","observations":"0"}]}';
 	    }
	}
	
	function arpansaDataAsJson($coordinates) {
		$json = "";
		$xmldata = file_get_contents("http://www.arpansa.gov.au/uvindex/realtime/xml/uvvalues.xml");
		$xml = new SimpleXmlElement($xmldata);
		$members = $xml->children();
		foreach($members as $member) {
			if(trim($member->getName()) == 'location') {
				$name = strtolower(trim($member['id']));
				if($json == "") {
					$json = '{"data":[{"name":"' . ucfirst($name) . '","longitude":"' . $coordinates[$name][1] . '","latitude":"' . $coordinates[$name][0] . '","index":"' . $member->index . '", "observations" : "' . $member->date . ' ' . $member->time . '"}';
				} else {
					$json = $json . ',{"name":"' . ucfirst($name) . '","longitude":"' . $coordinates[$name][1] . '", "latitude":"' . $coordinates[$name][0] . '","index":"' . $member->index . '", "observations" : "' . $member->date . ' ' . $member->time . '"}';	
				}	
			}
		}
		$json = $json . "]}";
		return $json;	
	}
	
	function extractPlatform($useragent) {
		
		$platform = "";
		
		if(strstr($useragent, 'iPhone') || strstr($useragent, 'iPad') ||
		   strstr($useragent, 'iPod') || strstr($useragent, 'Macintosh')) {
		  	$platform = 'iOS';
		} else if(strstr($useragent, 'Windows')) {
		  	$platform = 'Windows';
		} else if(strstr($useragent, 'Android')) {
			$platform = 'Android';
		} else {
			$platform = 'Unknown / Other';
		}
		
		return $platform;
	}
	
	function getUVIndexWithSunriseSunsetEffect($obj, $endtime) {
		
		$endtime_tt = strtotime($endtime);
		$sunrise_tt = strtotime(date("H:i", strtotime($obj->data->weather[0]->astronomy[0]->sunrise)));
		$sunset_tt = strtotime(date("H:i", strtotime($obj->data->weather[0]->astronomy[0]->sunset)));
	
		$uvIndex = $obj->data->weather[0]->uvIndex;
		
		if($endtime_tt < $sunrise_tt || $endtime_tt > $sunset_tt) {
			$uvIndex = -1;
		} 
		
		return $uvIndex;
		
	}
	
?>