require(['jquery', 'jquery/ui',"mage/calendar"], function($) { 
  $("#fromdate_offer").datetimepicker({
  stepMinute: 5,
  dateFormat:'yy-mm-dd',
  beforeShowDay: $.datepicker.noWeekends,
  hourMin: 1,
  hourMax: 24,
         minDate: new Date(),
         onSelect: function(dateStr) 
         {         
             $("#todate_offer").datepicker("option",{ minDate: new Date(dateStr)})
         }
     });

  $('#todate_offer').datetimepicker({
  stepMinute: 5,
  dateFormat:'yy-mm-dd',
  //  beforeShowDay: $.datepicker.noWeekends,
  hourMin: 1,
  hourMax: 24,
      onSelect: function(dateStr) {
      toDate = new Date(dateStr);
       }
     }); 
 $(".btnxyz").click(function(event) {
 
 //$(".btnclass").click(function(event) {
        //event.preventDefault();

        console.log("==============");
        var genral = jQuery("#genral").val();
        var dta = {"buy_product":[]};

      //  alert("in js file:"+genral);
        var json='{';
        for(i=0; i<=genral;i++  ){
         var tmpArr = {};
         var name=jQuery('#buy_product'+i).val();
         var qty=jQuery('#buy_quantity'+i).val();
         tmpArr['sku'] = name;
         tmpArr['qty'] = qty;
         dta.buy_product[i] = tmpArr;
          //json=json +"name:"+name+",quantity:"+qty+",";
        }
        console.log('dtpring');
        console.log(dta);
     
        //json=json + '}';
//        var buy_product = document.getElementById('buy_product').value;

        var post_id = document.getElementById('post_id').value;
        var name = document.getElementById('name').value;
        var status = document.getElementById('status').value;
        var store_id = document.getElementById('store_id').value;
     
        var discount_product = document.getElementById('discount_product').value;
        var discount = document.getElementById('discount').value;
        var priority = document.getElementById('priority').value;

        var fromdate_offer = document.getElementById('fromdate_offer').value;
        var todate_offer = document.getElementById('todate_offer').value;
        var customer_group = jQuery('.selectpicker').val();
      

    
        //console.log(buy_product);
        console.log("postid",post_id);
        console.log(discount_product);
        console.log(discount);
        console.log(priority);
      
        console.log(fromdate_offer);
        console.log(todate_offer);
        console.log(customer_group);

        
       if(store_id == '' || name =='' ||status =='' ||discount_product == ''|| discount == '' ||priority == '' ||fromdate_offer == '' ||todate_offer == '' ||customer_group == '')
        {
      

        }    
        else
        {
          //alert("buy_product:"+ buy_product);
          //var orderid = this.id;
          if(post_id){
            var param={
              name:name,
              status:status,
              store_id:store_id,
              post_id:post_id,
              rule_condition:JSON.stringify(dta),
              
              discount_product:discount_product,  
              discount:discount,
            priority:priority,
            fromdate_offer:fromdate_offer,
            todate_offer:todate_offer,
            customer_group:customer_group
            }

          }else{
            var param={
              name:name,
              status:status,
              store_id:store_id,
              rule_condition:JSON.stringify(dta),
  
              discount_product:discount_product,
              discount:discount,  
            priority:priority,
            fromdate_offer:fromdate_offer,
            todate_offer:todate_offer,
            customer_group:customer_group
            }

          }
          
          


          //['tot_type='+tot_type+'category='+category+'seller='+seller+'volume='+volume+'uom='+uom+'commission='+commission;
          var admin_path = window.location.pathname.split('/')[1];
     
          var customurl = window.location.origin+'/'+admin_path+'/retailinsights_pricerules/postXYZ/save';
  
          console.log("customurl");
          console.log('from here');
          console.log(JSON.stringify(dta));
  //        alert(JSON.stringify(dta));
          $.ajax({
              showLoader: true,
              url: customurl,
              data: param,
              type: "POST",
              dataType: 'json',
              complete:function(response){
                console.log("++++++++");
                console.log(response);
              },
              error:function(xhr,status,errorThrown){
              }
          });
        } 
        //return false;
    });
});