"use strict";
class Map {
static backgroundIMG = new Image();
static scale=1;
static translatePos={ x: 0, y: 0 };
static startDragOffset = {};
static mouseDown = false;
static isDragging = false;
static dragStart = { x: 0, y: 0 };
static cameraOffset = { x: 0, y: 0 };
static cameraZoom = 1;
static isMobile=false;
static grid_p;
static w_h_ratio;
static resizesensor;
static canvas;
static ctx;
static canvas_width;
static canvas_height;
static img_width=0;
static img_height=0;
static initialPinchDistance = null;
static lastZoom;
static Robots=Array();
static BGReady=false;
static mapAtive;
static actual_map_height;
static event_Listeners_added=false;



 constructor(body,dashboard,canvas,BackGroundURL,original_img_width,original_img_height,actual_map_width,MAX_ZOOM,MIN_ZOOM,SCROLL_SENSITIVITY,grid_status,grid_size,grid_snap) 
 {
        this.body=body;
        this.dashboard=dashboard;
        this.canvas=canvas;
        this.BackGroundURL = BackGroundURL;
        this.original_img_width=original_img_width;
        this.original_img_height=original_img_height;
        this.actual_map_width=actual_map_width;
        this.MAX_ZOOM=MAX_ZOOM;
        this.MIN_ZOOM=MIN_ZOOM;
        this.SCROLL_SENSITIVITY=SCROLL_SENSITIVITY;
        this.grid_status=grid_status;
        this.grid_snap=grid_snap;
        this.grid_size=grid_size;

       
       this.initCanVas();
       console.log("class constracted: "); 


    }


initCanVas= function() {
     
     if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent) 
    || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4))) 
        { 
    this.isMobile = true;
        }  

    this.translatePos= {x: 0,y: 0};
       this.resizesensor = new ResizeSensor(this.dashboard, function(){
       this.w_h_ratio=parseInt(this.original_img_width)/parseInt(this.original_img_height) ;
       this.canvas_width=this.dashboard.width()-60;
       this.canvas_height=parseInt(Math.ceil(parseInt(this.canvas_width)/this.w_h_ratio));
       this.actual_map_height=this.actual_map_width/this.w_h_ratio;
       this.mapAtive=true;


     this.canvas.width=this.canvas_width;
     this.canvas.height=this.canvas_height;
     this.ctx = this.canvas.getContext("2d");
     this.ctx.save();


          });





if(this.isMobile)
    {
     this.body.css("overscroll-behavior", "contain");
     this.canvas.css("overscroll-behavior", "auto");
     
     }

this.mapActiveToogle();


    

this.backgroundIMG = this.drawImage(this.BackGroundURL);

     //wait till image is loaded then draw canvas
  this.backgroundIMG.onload = function(){
        
    }
}


mapActiveToogle=function(){

if(this.mapAtive)
{
 this.event_Listeners_added=true;
 if(!this.event_Listeners_added)
     {
        this.canvas.addEventListener('mousedown', this.onPointerDown)
        this.canvas.addEventListener('touchstart', (e) => this.handleTouch(e, this.onPointerDown))
        this.canvas.addEventListener('mouseup', this.onPointerUp)
        this.canvas.addEventListener('touchend',  (e) => this.handleTouch(e, this.onPointerUp))
        this.canvas.addEventListener('mousemove', this.onPointerMove)
        this.canvas.addEventListener('touchmove', (e) => this.handleTouch(e, this.onPointerMove))
        this.canvas.addEventListener( 'wheel', (e) => this.adjustZoom(e.deltaY*this.SCROLL_SENSITIVITY))
    }
}
else
{
    if(this.event_Listeners_added)
    {
this.canvas.removeEventListener('mousedown', this.onPointerDown,false)
this.canvas.removeEventListener('touchstart', (e) => this.handleTouch(e, this.onPointerDown),false)
this.canvas.removeEventListener('mouseup', this.onPointerUp,false)
this.canvas.removeEventListener('touchend',  (e) => this.handleTouch(e, this.onPointerUp),false)
this.canvas.removeEventListener('mousemove', this.onPointerMove,false)
this.canvas.removeEventListener('touchmove', (e) => this.handleTouch(e, this.onPointerMove),false)
this.canvas.removeEventListener( 'wheel', (e) => this.adjustZoom(e.deltaY*this.SCROLL_SENSITIVITY),false)
this.event_Listeners_added=false;
    }

}


}


draw=function(){
     
     
    
       
     this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
     this.ctx.translate(this.translatePos.x, this.translatePos.y);
     

    // Translate to the canvas centre before zooming - so you'll always zoom on what you're looking directly at
    this.ctx.translate( this.canvas.width / 2, this.canvas.height / 2 );
    this.ctx.scale(this.cameraZoom, this.cameraZoom);
    this.ctx.translate( -this.canvas.width / 2 + this.cameraOffset.x, -this.canvas.height / 2 + this.cameraOffset.y )
        

   //================
    
    
    //console.log("zoomLevel:"+$("#zoomlevel").val()+" backgroundIMGOBJ_width: "+backgroundIMG.width+" CanvasWidth: "+$("#canvas_width").val()+" CanvasHeight: "+$("#canvas_height").val()+" ImgWidth:"+$("#img_width").val()+" imgHeight: "+$("#img_height").val());
    this.ctx.drawImage(this.backgroundIMG,0,0,this.original_img_width,this.original_img_height,0,0,this.canvas_width, this.canvas_height);
    
   if(this.grid)
   {
    this.ctx.clearRect(0, 0, this.canvas_width, this.canvas_height);
    this.ctx.drawImage(this.backgroundIMG,0,0,this.img_width,this.img_width,0,0,this.canvas_width, this.canvas_height);
    
    
    this.initialPinchDistance = null;
    this.lastZoom = this.cameraZoom;


   this.drawGrid();

   } 
    

    this.ctx.restore();

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

  
}



  drawImage=function(url){
     var background = new Image();
     background.src = url;
     this.BGReady=true;     
     console.log("BG LOADED"+url);
    return background;
  }


//===========


drawGrid=function(){

//======================
  var p=parseInt((this.canvas_width/this.actual_map_width)*this.grid_size);
  
  //console.log("zoomLevel:"+cameraZoom+" grid szie im meters: "+p+"canvas_width: "+bw+" CanvasHeight: "+bh);

   this.ctx.lineWidth = 0.07;
   this.ctx.strokeStyle = "silver";
   this.ctx.beginPath();
  
   for (let x = this.translatePos.x; x < this.canvas_width; x += p) {
     this.ctx.moveTo(x, 0);
     this.ctx.lineTo(x, this.canvas_height);
   }
   
   for (let x = this.translatePos.x; x > 0; x -= p) {
     this.ctx.moveTo(x, 0);
     this.ctx.lineTo(x, bh);
   }
  
   for (let y = this.translatePos.y; y < this.canvas_height; y += p) {
     this.ctx.moveTo(0, y);
     this.ctx.lineTo(this.canvas_width, y);
   }
   
   for (let y = this.translatePos.y; y > 0; y -= p ) {
     this.ctx.moveTo(0, y);
     this.ctx.lineTo(this.canvas_width, y);
   }
  
   this.ctx.closePath();
   this.ctx.stroke();


    //============



    
}






//============================
// Gets the relevant location from a mouse or single touch event
getEventLocation=function(e)
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

drawRect =function(x, y, width, height)
{
    this.ctx.fillRect( x, y, width, height )
}

drawText=function(text, x, y, size, font)
{
    this.ctx.font = `${size}px ${font}`
    this.ctx.fillText(text, x, y)
}


onPointerDown=function(e)
{
    this.isDragging = true
    this.dragStart.x = this.getEventLocation(e).x/this.cameraZoom - this.cameraOffset.x
    this.dragStart.y = this.getEventLocation(e).y/this.cameraZoom - this.cameraOffset.y
}

onPointerUp=function(e)
{
    this.isDragging = false
    this.initialPinchDistance = null
    this.lastZoom = this.cameraZoom
}

onPointerMove=function(e)
{
    if (this.isDragging)
    {
        this.cameraOffset.x = this.getEventLocation(e).x/this.cameraZoom - this.dragStart.x
        this.cameraOffset.y = this.getEventLocation(e).y/this.cameraZoom - this.dragStart.y
    }
}

handleTouch=function(e, singleTouchHandler)
{
    if ( e.touches.length == 1 )
    {
        singleTouchHandler(e)
    }
    else if (e.type == "touchmove" && e.touches.length == 2)
    {
        this.isDragging = false
        this.handlePinch(e)
    }
}


handlePinch=function(e)
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
        this.adjustZoom( null, currentDistance/initialPinchDistance )
    }
}

adjustZoom=function(zoomAmount, zoomFactor)
{
    if (!this.isDragging)
    {
        if (zoomAmount)
        {
            this.cameraZoom += zoomAmount
        }
        else if (zoomFactor)
        {
            console.log(zoomFactor)
            this.cameraZoom = this.zoomFactor*this.lastZoom
        }
        
        this.cameraZoom = Math.min( this.cameraZoom, this.MAX_ZOOM )
        this.cameraZoom = Math.max( this.cameraZoom, this.MIN_ZOOM )
        
        console.log(this.zoomAmount)
    }
}
return;
}

//============================