<?php 
include_once "includes/settings.php";

$app_token_url = "https://graph.facebook.com/oauth/access_token?"
    . "client_id=" . $app_id
    . "&client_secret=" . $app_secret 
    . "&grant_type=client_credentials";

    $response = file_get_contents($app_token_url);
    $params = null;
parse_str($response, $params);

$graph_url = "https://graph.facebook.com/" . $group_id . "/feed?access_token="
    . $params['access_token'];

/*
 * FQL!!!!!
$fql_query_url = 'https://graph.facebook.com/'
    . '/fql?q=SELECT+uid2+FROM+group+WHERE+uid1'
    . '&access_token=' . $access_token;
$fql_query_result = file_get_contents($fql_query_url);
$fql_query_obj = json_decode($fql_query_result, true);
*/


// alle zooi ophalen uit de groep
$fields = array();

$content = json_decode(file_get_contents($graph_url), true);
array_push($fields, $content);

// will allways return an empty last array
while($content['paging']['next']) {
    $content = json_decode(file_get_contents($content['paging']['next']), true);
    array_push($fields ,$content);
}

$allPosts = array();
$top15 = array();

// fill the $allPosts array with all posts
// TODO: dit is echt heel lelijk

for($i = 0; $i < count($fields) - 1; $i++) {
    for($j = 0; $j < count($fields[$i]['data']); $j++) {
        $youtubeCheck = strstr($fields[$i]['data'][$j]['source'], "youtube");
        if(
            $fields[$i]['data'][$j]['likes'] !== 'undefined' &&
            $fields[$i]['data'][$j]['type'] === 'video' &&
            $youtubeCheck !== false
        ) {
            if(count($top15) < 15) {
                array_push($top15, $fields[$i]['data'][$j]);
            }else {
                // compare new value to arrays
                for($k = 0; $k < count($top15); $k++) {
                    if( 
                        $top15[$k]['likes']['count'] < 
                        $fields[$i]['data'][$j]['likes']['count']
                    ) {
                        $top15[$k] = $fields[$i]['data'][$j];
                        break;
                    }
                }
            }
        }
    }
}

echo "<head>";
echo "<link href='stylesheets/style.css' type='text/css' rel='stylesheet' id='stylesheet'/>";
echo "<script src='http://code.jquery.com/jquery-1.9.0.min.js'></script>";
echo "<script src='js/functionality.js'></script>";
echo "</head>";
echo "<body>";
echo "<div class='top15div'>";
$url = "";
$strIndex = 0;
$videoIdLength = 11;

for($i = 0; $i < count($top15); $i++) {
    $rank = $i + 1;
    
    
    echo "<h4>" . $rank . ": " . $top15[$i]['name'] . "</h4>";
        // makes the beginning of the youtube url string
        // also adds the playlist attrebute
        // see https://developers.google.com/youtube/player_parameters#playlist
        if($i < 1) {
            
            $url = "http://www.youtube.com/embed/";
            $strIndex = strrpos($top15[$i]['source'], "/") + 1;
            $url = $url. substr($top15[$i]['source'], $strIndex, $videoIdLength);
            $url = $url . "?listType=playlist&playlist=";
        }else {
            $strIndex = strrpos($top15[$i]['source'], "/") + 1;
            $url = $url . substr(
                            $top15[$i]['source'], 
                            $strIndex, 
                            $videoIdLength
                          ) . ",";
        }
}

echo 
"<button id='reveal-player-button' onclick='popupVideo()' >GET PLAYLIST</button>";

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