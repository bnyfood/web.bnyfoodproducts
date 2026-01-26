"use strict";
class MapData {

static blocks; 
static machines;
static silos;
static windows_and_door;
static robots;




 constructor(blocks,machines,silos,windows_and_door,robots) 
 {
        this.blocks=blocks;
        this.machines=machines;
        this.silos=silos;
        this.windows_and_door=windows_and_door;
        this.robots=robots;
        
       console.log("mapdata class constracted: "); 


    }


    

 update_blocks = function(data) {

    this.blocks=data;
}

update_machines= function(data) {
    this.machines=data;
 
}


update_silos= function(data) {
    this.silos=data;
}

update_doors=function(data){
    this.door=data;

}

update_windows=function(data){
    this.windows=data;

}

}

//============================