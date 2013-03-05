var clicked = false;

function popupVideo() {
    if(clicked === false){
        clicked = true;
        $("#frame").animate({
            top: '-=660'
        });
        document.getElementById("reveal-player-button").innerHTML = 
            "<h4>HIDE PLAYLIST</h4>";
    }else {
        clicked = false;
        $("#frame").animate({
            top: '+=660'
        });
        document.getElementById("reveal-player-button").innerHTML = 
            "<h4>SHOW PLAYLIST</h4>";
    }
}