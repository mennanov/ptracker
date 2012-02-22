$(document).ready(function(){
    $('select#responsible').change(function(){
        var label = $(this).next('.label');
        $.getJSON($(this).val(), function(response){
            if(response == "success") {
                label.show().delay(1000).fadeOut();
            }
        });
    });
});

