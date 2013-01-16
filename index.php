<?php 
include_once "includes/settings.php";

$app_token_url = "https://graph.facebook.com/oauth/access_token?"
    . "client_id=" . $app_id
    . "&client_secret=" . $app_secret 
    . "&grant_type=client_credentials";

    $response = file_get_contents($app_token_url);
    $params = null;
parse_str($response, $params);

$graph_url = "https://graph.facebook.com/316380181796546/feed?access_token=" 
    . $params['access_token'];


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
        if(
            $fields[$i]['data'][$j]['likes'] !== 'undefined' &&
            $fields[$i]['data'][$j]['type'] === 'video'
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

echo "<body>";
echo "<div class='top15div'>";
$url = "";
$strIndex = 0;
$videoIdLength = 11;

for($i = 0; $i < count($top15); $i++) {
    $rank = $i + 1;
    
    echo "<h4>" . $rank . ": " . $top15[$i]['name'] . "</h4>";

    if(strpbrk($top15[$i]['source'], "youtube") !== false) {


        // makes the beginning of the youtube url string
        // also adds the playlist attrebute
        // see https://developers.google.com/youtube/player_parameters#playlist
        if($i < 1) {
            $strIndex = strrpos($top15[$i]['source'], "?") + 1;
            $url = substr($top15[$i]['source'], 0, $strIndex);
            $url = $url . "playlist=";
            //echo $url . "</br>";
        }else {
            $strIndex = strrpos($top15[$i]['source'], "/") + 1;
            $url = $url . substr(
                            $top15[$i]['source'], 
                            $strIndex, 
                            $videoIdLength
                          ) . ",";
        }
    }
}

echo  "</div>";

echo
"<iframe   
    id='frame'
    width='800'
    height='600'
    src=" . $url . "
    frameborder='0' 
    allowfullscreen>
</iframe>";
?>

</body>
<footer>
    <link href="stylesheets/style.css" type="text/css" rel="stylesheet" id="stylesheet"/>
</footer>