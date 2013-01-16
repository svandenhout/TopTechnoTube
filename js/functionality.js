var clicked = false;
$("#reveal-player-button").click(function() {
    if(clicked === false){
        clicked = true;
        $("#frame").animate({
            top: '-=600'
        });
    }else {
        clicked = false;
        $("#frame").animate({
            top: '+=600'
        });
    }
})