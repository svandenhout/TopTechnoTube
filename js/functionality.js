var clicked = false;

function popupVideo() {
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
}