require([
  "jquery"
  ], function($){
      $(document).ready(function() {
        jQuery(".click-me").click(function(){

          jQuery("#popup-mpdal").addClass('open-popop');
        });
    
           jQuery(".close-popop").click(function(){
            
          jQuery("#popup-mpdal").removeClass('open-popop');
        });
      });
});

