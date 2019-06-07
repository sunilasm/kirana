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
     var admin_path = window.location.pathname.split('/')[1];
         var customurl = window.location.origin+'/'+admin_path+'/admin_lirbuz/slider/index/delete';
        
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

