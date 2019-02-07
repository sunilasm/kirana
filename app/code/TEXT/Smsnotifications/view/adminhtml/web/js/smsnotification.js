require([
  "jquery"
  ], function($){
      $(document).ready(function() {
        document.addEventListener('click', function (event) {
          if (!event.target.matches('.action-clear')){
              return;
          }else{
                  event.preventDefault();
                  var classname = document.getElementsByClassName("_has-datepicker");
                  for (var i = 0; i < classname.length; i++) {
                      //document.getElementsByClassName("_has-datepicker").value = ''
                      var list = document.getElementsByClassName("_has-datepicker")[i];
                      list.value = "";
                  }
                } 
          
        }, false);
        jQuery(".click-me").click(function(){

          jQuery("#popup-mpdal").addClass('open-popop');
        });
    
           jQuery(".close-popop").click(function(){
            
          jQuery("#popup-mpdal").removeClass('open-popop');
        });
      });
});

