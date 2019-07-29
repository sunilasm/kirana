require(['jquery', 'jquery/ui'], function($){ 
    $(document).ready(function() {
        console.log("delete images");

    });
    
    $(document).on("click", ".btn_delete", function (event){
    console.log("btn_delete"); 
    var btn_id = $(this).attr('id');
    console.log(btn_id);
    var data = {
             'id': btn_id
         }
         var current_url = window.location.pathname ;
         var admin_path_index =  current_url.split('/')[1];
         var admin_path = current_url.split('/')[2];
         
         var check_url = current_url.includes("index.php");
         if(check_url == true){
            var customurl = window.location.origin+'/'+admin_path_index+'/'+admin_path+'/slider/index/delete';
         }else{
            var customurl = window.location.origin+'/'+admin_path_index+'/slider/index/delete';
         }
         console.log(customurl);
        
         $.ajax({
                 url: customurl,
                 type: 'POST',
                 dataType: 'json',
                 data: data,
                 showLoader: true,
                 complete: function(response) {
                     console.log(response);
                    window.location.reload(true);
                 },
                 error: function (xhr, status, errorThrown) {
                 }
             });
    });
});

