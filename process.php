<?php
 
// check for either ip, email or domain to vet
if($_POST['ip'] == '' && $_POST['email'] == '' && $_POST['domain'] == ''){
    echo 'Results will show here when form data submitted.';
    die;
}

// process.php file 
// clear and setup keys and values
   unset($post_array);
   $post_array = array();
   
// add a keyword and value for each item to pass in the API call
// the $post_array names should match E-HAWK keywords
// the $_POST values should match the form names exactly
   $post_array['apikey'] = 'YOUR-APIKEY';   
   $post_array['timeout'] = "false";
   $post_array['revet'] = 'true';   

   if($_POST['ip'] != "") { $post_array['ip'] = $_POST['ip'];  }
   if($_POST['email'] != "") { $post_array['email'] = $_POST['email'];  }
   if($_POST['firstname'] != "") { $post_array['firstname'] = $_POST['firstname'];  }
   if($_POST['lastname']  != "") { $post_array['lastname']= $_POST['lastname']; }
   if($_POST['domain']  != "") { $post_array['domain']= $_POST['domain']; }

   
// Call the API
$curl = curl_init("https://api.e-hawk.net/");
if (!empty($curl)) {
    curl_setopt ($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt ($curl, CURLOPT_POST, true);
    curl_setopt ($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt ($curl, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt ($curl, CURLOPT_TIMEOUT, 10);
    curl_setopt ($curl, CURLOPT_USERAGENT, "E-HAWK API Call");
    curl_setopt ($curl, CURLOPT_POSTFIELDS, $post_array);
    $result = curl_exec ($curl);
    curl_close ($curl);
}              
               
// process result
$response_array = json_decode($result, true);    

// check if API response contains status and responded
if (isset($response_array['status'])) {
    // check for status value success or error
    if($response_array['status'] == '0' ) {
      // check for JSON response format 1 or 2
      // JSON Format 1
      if(isset($response_array['scores'])) {
        echo '<span class="badge badge-secondary mr-3">Risk Score</span>'.$response_array['score'][1].'<br>';
        echo '<span class="badge badge-secondary mr-3">Risk Type&nbsp;</span>'.$response_array['score'][2]; 
        }
      // JSON Format 2  
      if(isset($response_array['area'])) {
        echo '<span class="badge badge-secondary mr-3">Risk Score</span>'.$response_array['score']['risk'].'<br>';
        echo '<span class="badge badge-secondary mr-3">Risk Type&nbsp;</span>'.$response_array['score']['type']; 
        }  
    } else {
        echo '<p>API Status Error: '.$response_array['error_message'].'</p>';
    }
} else {
    // API did not respond properly. Probably a timeout issue
    // Recommend trying to send API call again
    echo '<p>Did not get a proper response from API</p>';
}
    
    
    // print result
#print_r($result); 
echo '<br><br><pre>';
$json_result= json_decode($result);
echo json_encode($json_result, JSON_PRETTY_PRINT);
echo '</pre>';
?>