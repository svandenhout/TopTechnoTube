var clicked = false;

function popupVideo() {
    if(clicked === false){
        clicked = true;
        $("#frame").animate({
            top: '-=660'
        });
    }else {
        clicked = false;
        $("#frame").animate({
            top: '+=660'
        });
    }
}