var canvas;
var backgroundIMG = new Image();
var scale=1;
var translatePos={ x: 0, y: 0 };
var startDragOffset = {};
var mouseDown = false;
var isDragging = false;
var dragStart = { x: 0, y: 0 };
var spawn_padding={x:2,y:2};
var cameraOffset = { x: 0, y: 0 };
var cameraZoom = 1;
var MAX_ZOOM = 10;
var MIN_ZOOM = 1;
var SCROLL_SENSITIVITY = 0.0005;
var grid_p;
var ctx;
var actual_map_width;
var actual_map_height;
var activeobj=1;   //1=map, 2=block, 3=robot, 4= machine, 5 =silo, 6= anchor



var isMobile = false; //initiate as false
// device detection
if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent) 
    || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4))) { 
    isMobile = true;
}



$(document).ready(function(){




new ResizeSensor($('.dashboard-content'), function(){
var map = $('#map');
var path=map.attr("imgpath");
var original_img_width=map.attr("imgwidth");
var original_img_height=map.attr("imgheight");
var w_h_ratio=parseInt(original_img_width)/parseInt(original_img_height) ;
var canvas_width=$('.dashboard-content').width()-60;
var canvas_height=parseInt(Math.ceil(parseInt(canvas_width)/w_h_ratio));
var img_width=0;
var img_height=0;
actual_map_width=$("#actual_map_width").val();
actual_map_height=actual_map_width/w_h_ratio;

//console.log("Canvas_width :"+canvas_width.toString());
//console.log("Canvas_height :"+canvas_height.toString());
//console.log("ratio :"+w_h_ratio.toString());



  

  
       $("#canvas_width").val(canvas_width);
       $("#canvas_height").val(canvas_height);


     initCanVas();





        
           
      });



});

//capture map element change to submit Ajax to server








function initCanVas() {

    const aj=null;
    const blocks=null;
    const machines=null;
    const silos=null;
    const doors=null;
    const windows_and_door=null;
    const robots=null;
    const data=null;

    //get block
    
    data={amt_map_id:$("#amt_map_id").val()};
    aj=new AjaxClass("POST","automation/block/block_get_by_mapid_json",data);
    if(aj.sesponseMgs="success")
        {
            blocks=aj.datas;
        }
     //get machines
    
    aj=new AjaxClass("POST","automation/machines/machine_get_by_mapid_json",data);
    if(aj.sesponseMgs="success")
        {
            machines=aj.datas;
        }
     //get silo
    
     aj=new AjaxClass("POST","automation/silos/silo_get_by_mapid_json",data);
     if(aj.sesponseMgs="success")
         {
             silos=aj.datas;
         }
 

    //get doors
    aj=new AjaxClass("POST","automation/doors/windows_and_door_get_by_mapid_json",data);
    if(aj.sesponseMgs="success")
        {
            windows_and_door=aj.datas;
        }


        //get Robots

aj=new AjaxClass("POST","automation/doors/robot_get_by_mapid_json",data);
    if(aj.sesponseMgs="success")
        {
            robots=aj.datas;
        }



     
mapdata=new MapData(blocks,machines,silos,doors,windows_and_door,robots); 


canvas = document.getElementById("canvas");
canvas.width=$("#canvas_width").val();
canvas.height=$("#canvas_height").val();

translatePos= {
        x: 0,
        y: 0
      };


if(isMobile)
    {
     $("body").css("overscroll-behavior", "contain");
     $("canvas").css("overscroll-behavior", "auto");
     
     }

var map = $('#map');
var path=map.attr("imgpath");
backgroundIMG = drawImage(path);

     //wait till image is loaded then draw canvas
  backgroundIMG.onload = function(){
  var   img_width=backgroundIMG.width;
  var   img_height=backgroundIMG.height;
 
$("#img_width").val(img_width);
$("#img_height").val(img_height);



        canvas.addEventListener('mousedown', onPointerDown)
        canvas.addEventListener('touchstart', (e) => handleTouch(e, onPointerDown))
        canvas.addEventListener('mouseup', onPointerUp)
        canvas.addEventListener('touchend',  (e) => handleTouch(e, onPointerUp))
        canvas.addEventListener('mousemove', onPointerMove)
        canvas.addEventListener('touchmove', (e) => handleTouch(e, onPointerMove))
        canvas.addEventListener( 'wheel', (e) => adjustZoom(e.deltaY*SCROLL_SENSITIVITY))



  window.requestAnimationFrame(draw);
}
}

function draw() {
 
     
     
     
     ctx = canvas.getContext("2d");
     ctx.save();
     ctx.clearRect(0, 0, $("#canvas_width").val(), $("#canvas_height").val());


var atLeastOneIsChecked = $('#checkArray:checkbox:checked').length > 0;
   
     

      ctx.translate(translatePos.x, translatePos.y);
      ctx.scale($("#zoomlevel").val(), $("#zoomlevel").val());

    //==================


    // Translate to the canvas centre before zooming - so you'll always zoom on what you're looking directly at
    ctx.translate( canvas.width / 2, canvas.height / 2 );
    ctx.scale(cameraZoom, cameraZoom);
    ctx.translate( -canvas.width / 2 + cameraOffset.x, -canvas.height / 2 + cameraOffset.y )
        

   //================
    
    
    //console.log("zoomLevel:"+$("#zoomlevel").val()+" backgroundIMGOBJ_width: "+backgroundIMG.width+" CanvasWidth: "+$("#canvas_width").val()+" CanvasHeight: "+$("#canvas_height").val()+" ImgWidth:"+$("#img_width").val()+" imgHeight: "+$("#img_height").val());
    ctx.drawImage(backgroundIMG,0,0,$("#img_width").val(),$("#img_height").val(),0,0,$("#canvas_width").val(), $("#canvas_height").val());
    
   if($('#grid_status:checkbox:checked').length > 0)
   {
    ctx.clearRect(0, 0, $("#canvas_width").val(), $("#canvas_height").val());
    ctx.drawImage(backgroundIMG,0,0,$("#img_width").val(),$("#img_height").val(),0,0,$("#canvas_width").val(), $("#canvas_height").val());
    
    
    

   drawGrid();

   } 
    

    ctx.restore();

    //Ajax call all robot position

    /*







  const ctx = document.getElementById("canvas").getContext("2d");

  
  ctx.clearRect(0, 0, 300, 300); // clear canvas

  ctx.fillStyle = "rgb(0 0 0 / 40%)";
  ctx.strokeStyle = "rgb(0 153 255 / 40%)";
  ctx.save();
  ctx.translate(150, 150);

  // Earth
  const time = new Date();
  ctx.rotate(
    ((2 * Math.PI) / 60) * time.getSeconds() +
      ((2 * Math.PI) / 60000) * time.getMilliseconds(),
  );
  ctx.translate(105, 0);
  ctx.fillRect(0, -12, 40, 24); // Shadow
  ctx.drawImage(earth, -12, -12);

  // Moon
  ctx.save();
  ctx.rotate(
    ((2 * Math.PI) / 6) * time.getSeconds() +
      ((2 * Math.PI) / 6000) * time.getMilliseconds(),
  );
  ctx.translate(0, 28.5);
  ctx.drawImage(moon, -3.5, -3.5);
  ctx.restore();

  ctx.restore();

  ctx.beginPath();
  ctx.arc(150, 150, 105, 0, Math.PI * 2, false); // Earth orbit
  ctx.stroke();

  ctx.drawImage(sun, 0, 0, 300, 300);
  */

  window.requestAnimationFrame(draw);
}


$('#actual_map_width,#grid_snap,#grid_status,#grid_size').change(function(){

var amt_map_id=$('#amt_map_id').val();
var actual_map_width=$('#actual_map_width').val();
var grid_status=$('#grid_status:checked').length;
var grid_snap=$('#grid_snap:checked').length;
var grid_size=$('#grid_size').find(":selected").val();
var data={amt_map_id:amt_map_id,actual_map_width:actual_map_width,grid_status:grid_status,grid_snap:grid_snap,grid_size:grid_size};


var ajax=new AjaxClass("POST","https://intranet.bnyfoodproducts.com/automation/map/map_update",data);

ajax.executeAjax();

});

function drawImage(url){
     var background = new Image();
     background.src = url;
     
    return background;
  }


//===========


function  drawObj(){
    var bw=$("#canvas_width").val();
    var bh=$("#canvas_height").val();
    var aw=actual_map_width;
    var p=parseInt((bw/aw)*$("#grid_size").val());

    const ratio=$("#canvas_width").val()/$("#actual_map_width").val();
//draw blocks
if(mapdata.blocks!=null)
    {
      mapdata.blocks.forEach(function(block_data){
        ctx.fillStyle = 'rgba(255,225,0,0.4)';
        ctx.lineWidth = 0.08;
        ctx.strokeStyle = "silver";
        ctx.beginPath();
        
        ctx.Rect(parseInt((block_data.x-block_data.block_size/2)*ratio),parseInt((block_data.y-block_data.block_size/2)*ratio),parseInt(block_data.block_size*ratio),parseInt(block_data.block_size*ratio));
        ctx.closePath();
        ctx.stroke();
        ctx.fill();

      
      });
                
                  
         
    } 
         


  //draw machine
if(mapdata.machines!=null)
    {
      mapdata.machines.forEach(function(machines_data){
        ctx.save();
        ctx.rotate(machines_data.heading * Math.PI / 180);

        //machine
        ctx.fillStyle = 'rgba(138,138,138,1)';
        ctx.lineWidth = 0.08;
        ctx.strokeStyle = "black";
        ctx.beginPath();
        ctx.Rect(parseInt((machines_data.x-machines_data.machine_length/2)*ratio),parseInt((machines_data.y-machines_data.machine_width/2)*ratio),parseInt(machines_data.machine_length*ratio),parseInt(machines_data.machine_width*ratio));
        ctx.closePath();
        ctx.stroke();
        ctx.fill();


         

        //machine silo    
        ctx.lineWidth = 0.08;
        ctx.strokeStyle = "black";
        ctx.beginPath();
        ctx.arc(parseInt((machines_data.x+machines_data.machine_length/2-0.3)*ratio),parseInt(machines_data.y*ratio),ParseInt(0.5*ratio),0,2 * Math.PI);
        ctx.closePath();
        ctx.stroke();
        ctx.fill();



        if(machines_data.machine_status(0).status_value=="0")
            {
        ctx.fillStyle = 'rgba(255,0,0,1)';
            }
            else
            {
                ctx.fillStyle = 'rgba(0,255,0,1)';                
            }

        //chargepoint
        ctx.lineWidth = 0.08;
        ctx.strokeStyle = "black";
        ctx.beginPath();
        ctx.Rect(parseInt((machines_data.x+machines_data.machine_length/2)*ratio), parseInt((machines_data.y-0.1)*ratio),parseInt(0.2*ratio),parseInt(0.2*ratio));
        ctx.closePath();
        ctx.stroke();
        ctx.fill();


        //power button
        ctx.arc(parseInt((machines_data.x+machines_data.machine_length/2-0.06)*ratio),parseInt((machines_data.y-machines_data.machine_width/2+0.06)*ratio),ParseInt(0.05*ratio),0,2 * Math.PI);
           

        ctx.fill();
        ctx.resetTransform();
        ctx.restore();
      
      });
         
    } 



     //draw silo
if(mapdata.silos!=null)
    {
      mapdata.silos.forEach(function(silos_data){
        ctx.save();
        ctx.rotate(silos_data.heading * Math.PI / 180);

        ctx.lineWidth = 0.08;
        ctx.strokeStyle = "black";
        ctx.fillStyle = 'rgba(138,138,138,1)';

        ctx.beginPath();
        ctx.rect(parseInt((silos_data.x-silos_data.silo_length/2)*ratio),parseInt((silos_data.y-silos_data.silo_width/2)*ratio),parseInt(silos_data.silo_length*ratio),parseInt(silos_data.silo_width*ratio))
        ctx.closePath();
        ctx.stroke();
        
        ctx.beginPath();
        ctx.arc(parseInt(silos_data.x*ratio),parseInt(silos_data.y*ratio),ParseInt(0.5*ratio),2 * Math.PI);
        ctx.closePath();
        ctx.stroke();
        ctx.fill();


         //chargepoint
            if(machines_data.machine_status[0].status_value=="0")
            {
        ctx.fillStyle = 'rgba(255,0,0,1)';
            }
            else
            {
                ctx.fillStyle = 'rgba(0,255,0,1)';                
            }

        ctx.lineWidth = 0.08;
        ctx.strokeStyle = "black";
        ctx.beginPath();
        ctx.Rect(parseInt((machines_data.x+silos_data.silo_length/2-0.2)*ratio),parseInt((machines_data.y-0.1)*ratio),parseInt(0.2*ratio),parseInt(0.2*ratio));
        ctx.closePath();
        ctx.stroke();
        ctx.fill();


       //power button
        ctx.lineWidth = 0.08;
        ctx.strokeStyle = "black";
        ctx.beginPath();
        ctx.arc(parseInt((silos_data.x+silos_data.silo_length/2-0.06)*ratio),parseInt((silos_data.y+0.06)*ratio),ParseInt(0.05*ratio),0,2 * Math.PI);
             
        ctx.fill();
        ctx.resetTransform();
        ctx.restore();
      
      });
         
    } 


         //draw doors and windows
if(mapdata.windows_and_door!=null)
    {
      mapdata.windows_and_door.forEach(function(windows_and_door_data){
        ctx.save();
        ctx.rotate(windows_and_door_data.heading * Math.PI / 180);

        

        if(Object.keys(windows_and_door_data.percent_opening).length == 0) // this is map editing mode laying windows with fully closed drawn
        {
        ctx.fillStyle = 'rgba(138,138,138,1)';
        ctx.lineWidth = 0.08;
        ctx.strokeStyle = "black";
        ctx.beginPath();
             
        ctx.rect(parseInt((windows_and_door_data.x-windows_and_door_data.width/2)*ratio),parseInt((windows_and_door_data.y-windows_and_door_data.thickness/2)*ratio),parseInt(windows_and_door_data.width*ratio),parseInt(windows_and_door_data.thickness*ratio));
        ctx.closePath();
        ctx.stroke();
        ctx.fill();

        }
        else // this is runing mode with full detail
        {
          

            ctx.fillStyle = 'rgba(138,138,138,1)';
            ctx.lineWidth = 0.08;
            ctx.strokeStyle = "black";
            ctx.beginPath();
                 
            ctx.rect(parseInt((windows_and_door_data.x-windows_and_door_data.width/2)*ratio),parseInt((windows_and_door_data.y-windows_and_door_data.thickness/2)*ratio),parseInt(windows_and_door_data.width*ratio),parseInt(windows_and_door_data.thickness*ratio));
            ctx.closePath();
            ctx.stroke();

            ctx.beginPath();
            ctx.rect(parseInt((windows_and_door_data.x-windows_and_door_data.width/2)*ratio),parseInt((windows_and_door_data.y-windows_and_door_data.thickness/2)*ratio),parseInt(windows_and_door_data.width*windows_and_door_data.percent_opening*ratio),parseInt(windows_and_door_data.thickness*ratio));
            ctx.closePath();
            ctx.stroke();
            ctx.fill();
        }
        
            ctx.resetTransform();
        ctx.restore();
      
      });
         
    } 


     //draw robots
if(mapdata.robots!=null)
    {
        mapdata.robots.forEach(function(robots_data){
            ctx.save();
            if(Object.keys(robots_data.robot_location)==0)  // this is robot with no initial position info
            {
             
                ctx.fillStyle = 'rgba(255,0,0,1)';
                ctx.lineWidth = 0.08;
                ctx.strokeStyle = "black";
                ctx.beginPath();
                ctx.rect(parseInt(spawn_padding.x*ratio),parseInt(spawn_padding.y*ratio),parseInt(robots_data.robot_length*ratio),parseInt(robots_data.robot_width*ratio));
                ctx.closePath();
                ctx.stroke();
                ctx.fill();


                //robot silo

                ctx.beginPath();
                ctx.arc(parseInt((spawn_padding.x+robots_data.robot_length-0.3)*ratio),parseInt((spawn_padding.y+robots_data.robot_width/2)*ratio),ParseInt(0.5*ratio),2 * Math.PI);
                ctx.closePath();
                ctx.stroke();
                ctx.fill();


            }
            else   // this is with life mode
            {
                ctx.rotate(robots_data.robot_location.heading * Math.PI / 180);
                
                //robot body
                ctx.fillStyle = 'rgba(0,255,0,1)';
                ctx.lineWidth = 0.08;
                ctx.strokeStyle = "black";
                ctx.beginPath();
                ctx.rect(parseInt((robots_data.robot_location.x-robots_data.robot_length/2)*ratio),parseInt((robots_data.robot_location.y-robots_data.robot_width/2)*ratio),parseInt(robots_data.robot_length*ratio),parseInt(robots_data.robot_width*ratio));
                ctx.closePath();
                ctx.stroke();
                ctx.fill();

                //robot silo

                ctx.beginPath();
                ctx.arc(parseInt((robots_data.robot_location.x+robots_data.robot_length/2-0.3)*ratio),parseInt((robots_data.robot_location.y)*ratio),ParseInt(0.5*ratio),2 * Math.PI);
                ctx.closePath();
                ctx.stroke();
                ctx.fill();
                
                
            }
            ctx.rotate(machines_data.heading * Math.PI / 180);
            ctx.fillStyle = 'rgba(138,138,138,1)';
            ctx.arc(parseInt((silos_data.x-silos_data.silo_width/2)*ratio),parseInt((silos_data.y-silos_data.silo_length/2)*ratio),ParseInt(0.5*ratio),2 * Math.PI);
            ctx.fill();
    
    
            
                if(machines_data.machine_status[0].status_value=="0")
                {
            ctx.fillStyle = 'rgba(255,0,0,1)';
                }
                else
                {
                    ctx.fillStyle = 'rgba(0,255,0,1)';                
                }
    
            ctx.fillRect(parseInt((machines_data.x)*ratio),parseInt((machines_data.y-0.1)*ratio),parseInt(0.2*ratio),parseInt(0.2*ratio));
            ctx.fill();
    
            ctx.arc(parseInt((silos_data.x-silos_data.machine_width/2+0.06)*ratio),parseInt((silos_data.y+0.06)*ratio),ParseInt(0.05*ratio),0,2 * Math.PI);
                 
            ctx.fill();
            ctx.resetTransform();
            ctx.restore();
          
          });
         
    } 

  }         

             

function drawGrid(){

//======================
  var bw=$("#canvas_width").val();
  var bh=$("#canvas_height").val();
  var aw=actual_map_width;
  var p=parseInt((bw/aw)*$("#grid_size").val());
  
  //console.log("zoomLevel:"+cameraZoom+" grid szie im meters: "+p+"canvas_width: "+bw+" CanvasHeight: "+bh);

   ctx.lineWidth = 0.07;
   ctx.strokeStyle = "silver";
   ctx.beginPath();
  
   for (let x = translatePos.x; x < bw; x += p) {
     ctx.moveTo(x, 0);
     ctx.lineTo(x, bh);
   }
   
   for (let x = translatePos.x; x > 0; x -= p) {
     ctx.moveTo(x, 0);
     ctx.lineTo(x, bh);
   }
  
   for (let y = translatePos.y; y < bh; y += p) {
     ctx.moveTo(0, y);
     ctx.lineTo(bw, y);
   }
   
   for (let y = translatePos.y; y > 0; y -= p ) {
     ctx.moveTo(0, y);
     ctx.lineTo(bw, y);
   }
  
   ctx.closePath();
   ctx.stroke();


    //============



    
}






//============================
// Gets the relevant location from a mouse or single touch event
function getEventLocation(e)
{
    if (e.touches && e.touches.length == 1)
    {
        return { x:e.touches[0].clientX, y: e.touches[0].clientY }
    }
    else if (e.clientX && e.clientY)
    {
        return { x: e.clientX, y: e.clientY }        
    }
}

function drawRect(x, y, width, height)
{
    ctx.fillRect( x, y, width, height )
}

function drawText(text, x, y, size, font)
{
    ctx.font = `${size}px ${font}`
    ctx.fillText(text, x, y)
}


function onPointerDown(e)
{
    isDragging = true
    dragStart.x = getEventLocation(e).x/cameraZoom - cameraOffset.x
    dragStart.y = getEventLocation(e).y/cameraZoom - cameraOffset.y
}

function onPointerUp(e)
{
    isDragging = false
    initialPinchDistance = null
    lastZoom = cameraZoom
}

function onPointerMove(e)
{

    var xval=dragStart.x;
    var yval=dragStart.y;
    console.log("X:"+xval+" Y:"+yval);

    if (isDragging)
    {
        cameraOffset.x = getEventLocation(e).x/cameraZoom - dragStart.x
        cameraOffset.y = getEventLocation(e).y/cameraZoom - dragStart.y
    }
}

function handleTouch(e, singleTouchHandler)
{
    if ( e.touches.length == 1 )
    {
        singleTouchHandler(e)
    }
    else if (e.type == "touchmove" && e.touches.length == 2)
    {
        isDragging = false
        handlePinch(e)
    }
}

let initialPinchDistance = null
let lastZoom = cameraZoom

function handlePinch(e)
{
    e.preventDefault()
    
    let touch1 = { x: e.touches[0].clientX, y: e.touches[0].clientY }
    let touch2 = { x: e.touches[1].clientX, y: e.touches[1].clientY }
    
    // This is distance squared, but no need for an expensive sqrt as it's only used in ratio
    let currentDistance = (touch1.x - touch2.x)**2 + (touch1.y - touch2.y)**2
    
    if (initialPinchDistance == null)
    {
        initialPinchDistance = currentDistance
    }
    else
    {
        adjustZoom( null, currentDistance/initialPinchDistance )
    }
}

function adjustZoom(zoomAmount, zoomFactor)
{
    if (!isDragging)
    {
        if (zoomAmount)
        {
            cameraZoom += zoomAmount
        }
        else if (zoomFactor)
        {
            console.log(zoomFactor)
            cameraZoom = zoomFactor*lastZoom
        }
        
        cameraZoom = Math.min( cameraZoom, MAX_ZOOM )
        cameraZoom = Math.max( cameraZoom, MIN_ZOOM )
        
        console.log(zoomAmount)
    }
}


function mapActiveToogle()
{

    if(mapactive)
    {
        mapactive=false;
    }
    else
    {
        mapactive=true;
    }
initCanVas();
    console.log("mapActive:"+mapactive);
}

//============================