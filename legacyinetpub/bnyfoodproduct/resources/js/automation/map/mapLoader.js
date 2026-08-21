var MAP;
$(document).ready(function(){

    MAP=new Map(
    $('body'),
    $('#dashboard-content'),
    $('#canvas'),
    $('#map').attr('imgpath'),
    $('#map').attr('imgwidth'),
    $('#map').attr('imgheight'),
    $("#actual_map_width").val(),
    15,
    1,
    0.0005,
    ($('#grid_status:checkbox:checked').length > 0) ? true :false,
    $('#grid_size').val(),
    ($('#grid_snap:checkbox:checked').length > 0) ? true :false
    );




loop();





$('#actual_map_width,#grid_snap,#grid_status,#grid_size').change(function(){

var amt_map_id=$('#amt_map_id').val();
var actual_map_width=$('#actual_map_width').val();

var grid_status=$('#grid_status:checked').length;
var grid_snap=$('#grid_snap:checked').length;
var grid_size=$('#grid_size').find(":selected").val();

if($('#grid_status:checkbox:checked').length > 0)
{
map.grid_status=true;
}
else
{
map.grid_status=false;
}

if($('#grid_snap:checkbox:checked').length > 0)
{
map.grid_snap=true;
}
else
{
map.grid_snap=false;
}

map.grid_size=grid_size;


 $.ajax({
         type: "POST",
         url: "https://intranet.bnyfoodproducts.com/automation/map/map_update", 
         data: {amt_map_id:amt_map_id,actual_map_width:actual_map_width,grid_status:grid_status,grid_snap:grid_snap,grid_size:grid_size},
         dataType: "json",  
         cache:false,
         success: 
              function(data){
                console.log(data);  //as a debugging message.
              }
          });// you have missed this bracket
     return false;

  });




  });


function loop()
{
    console.log("loop begin:"+MAP.BGReady);       
    if(MAP.BGReady)
    {
console.log("will draw");
MAP.draw(MAP);
requestAnimationFrame(loop());
    }
    else
    {
 console.log("will wait");       
setInterval(loop(),1000);
        
    }



}