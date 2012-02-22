$(document).ready(function(){
    // responsible user <select> change
    $('select#responsible').change(function(){
        var label = $(this).next('.label');
        $.getJSON($(this).val(), function(response){
            if(response == "success") {
                label.show().delay(1000).fadeOut();
            }
        });
    });
    
    // change task status
    $('.status:not(.disabled)').live('click', function(){
        var $this = $(this);
        $.getJSON($(this).data('href'), function(response){ 
            if(response == "success") {
                $('.status').addClass('disabled', true);
                $('#status').val($this.text());
            }
        });
        return false;
    });
});

