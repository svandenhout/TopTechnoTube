var clicked = false;

function popupVideo() {
    if(clicked === false){
        clicked = true;
        $("#frame").animate({
            top: '-=660'
        });
        document.getElementById("reveal-player-button").innerHTML = "HIDE PLAYLIST";
    }else {
        clicked = false;
        $("#frame").animate({
            top: '+=660'
        });
        document.getElementById("reveal-player-button").innerHTML = "SHOW PLAYLIST";
    }
}