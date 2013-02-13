<?php 
include_once "includes/settings.php";

$app_token_url = "https://graph.facebook.com/oauth/access_token?"
    . "client_id=" . $app_id
    . "&client_secret=" . $app_secret
    . "&grant_type=client_credentials";

    $response = file_get_contents($app_token_url);
    $params = null;
parse_str($response, $params);

$last_week = time() - (7 * 24 * 60 * 60);

/*
 * FQL!!!!!
 */ 
$fql_query_url = 
    "https://graph.facebook.com/"
    . "fql?q=SELECT+attachment,+likes,+created_time,+actor_id+"
    . "FROM+stream+WHERE+source_id="
    . $group_id . "+AND+updated_time>" . $last_week . "+LIMIT+9999"
    . '&access_token=' . $params[access_token];
$fql_query_result = file_get_contents($fql_query_url);
$fql_query_obj = json_decode($fql_query_result, true);

// echo '<pre>';
// print_r($fql_query_obj);
// echo '</pre>';

$top15 = array();

for($i = 0; $i < count($fql_query_obj['data']); $i++) {
    $post = array();
        
    $source = 
        $fql_query_obj
            ['data'][$i]['attachment']['media'][0]['video']['source_url'];
    $likes = $fql_query_obj['data'][$i]['likes']['count'];
    $name = $fql_query_obj['data'][$i]['attachment']['media'][0]['alt'];
    $user = $fql_query_obj['data'][$i]['actor_id'];
    
    $post['source'] = $source;
    $post['likes'] = $likes;
    $post['name'] = $name;
    $post['user'] = $user;
    
    $youtubeCheck = strstr(
        $source,
        "youtube"
    );
    
    if(
        $likes !== 'undefined' &&
        $youtubeCheck !== false
    ) {
        if(count($top15) < 15) {
            array_push($top15, $post);
        }else {
            // compare new value to arrays
            for($j = 0; $j < count($top15); $j++) {
                if( 
                    $top15[$j]['likes'] < 
                    $post['likes']
                ) {
                    $top15[$j] = $post;
                    break;
                }
            }
        }
    }
    
}

echo "<body>";
echo "<div class='top15div'>";
$url = "";
$strIndex = 0;
$videoIdLength = 11;
$urlFlag = true;

for($i = count($top15); $i > 0; $i--) {
    $arrayIndex = $i - 1;

    $uid = number_format($top15[$arrayIndex]['user'], 0, '.', '');
    
     
     /*
     * user names are commented 
     */       
    // $fql_query_url = "https://graph.facebook.com/fql?q=SELECT+"
        // . "first_name,+last_name+FROM+user+WHERE+uid=" . $uid
        // . "&access_token=" . $params[access_token];
    // $fql_query_result = file_get_contents($fql_query_url);
    // $fql_query_obj = json_decode($fql_query_result, true);
//     
    // $userName = 
        // $fql_query_obj['data'][0]['first_name'] . " " .
        // $fql_query_obj['data'][0]['last_name'];
    
    echo "<div class='track-block'>";
    echo "<h4 class='track-names'>" 
        . $i . ": " . $top15[$arrayIndex]['name'] . "</h4>";
    // echo "<h4 class='names'>" . $userName . "</h4>";
    echo "</div>";
    
    // makes the beginning of the youtube url string
    // also adds the playlist attrebute
    // see https://developers.google.com/youtube/player_parameters#playlist
    if($urlFlag) {
        $urlFlag = false;
        $url = "http://www.youtube.com/embed/";
        $strIndex = strrpos($top15[$arrayIndex]['source'], "/") + 1;
        
        $url = $url.substr(
            $top15[$arrayIndex]['source'],
            $strIndex, 
            $videoIdLength
        );
            
        $url = $url . "?listType=playlist&playlist=";
    }else {
        $strIndex = strrpos($top15[$arrayIndex]['source'], "/") + 1;
        $url = $url . substr(
                        $top15[$arrayIndex]['source'], 
                        $strIndex, 
                        $videoIdLength
                      ) . ",";
    }
}

echo 
"<button 
    id='reveal-player-button' 
    onclick='popupVideo()'>
    <h4>SHOW PLAYLIST</h4>
</button>";

echo
"<iframe 
    id='frame'
    width='640'
    height='480'
    src=" . $url . "
    frameborder='0'
    allowfullscreen>
</iframe>";

echo "</div>";    
?>

</body>
<footer>
    <link href="stylesheets/style.css" type="text/css" rel="stylesheet" id="stylesheet"/>
    <link href='http://fonts.googleapis.com/css?family=Monda' rel='stylesheet' type='text/css'>
    <script src="js/jquery-1.9.0.min.js"></script>
    <script src="js/functionality.js"></script>
</footer>