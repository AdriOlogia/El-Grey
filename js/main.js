$(document).ready(function(){       
    var scroll_pos = 0;
    $(document).scroll(function() { 
        scroll_pos = $(this).scrollTop();
        if(scroll_pos > 40) {
            $("header").css('background-color', 'white');
        } else {
            $("header").css('background-color', 'transparent');
        }
    });
});


