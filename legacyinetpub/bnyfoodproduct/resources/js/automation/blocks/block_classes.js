"use strict";
class Block {

static blockid;

 constructor(shopid,mapid,blocksize,url) 
 {
        this.shopid=shopid;
        this.blockid=blockid;
        this.mapid=mapid;
        this.blocksize = blocksize;
        

        var data={ShopID:this.shopid,amt_map_id:this.mapid,block_size:this.blocksize};

        var AjacCall=new AjaxClass("POST",url)
        var newBlockid=AjacCall.executeAjax();
             if(newBlockid.responseMsg=="success")
            {
             console.log("return data: "+newBlockid);
             this.blockid=newBlockid.data[0]('block_id');

            }

            

}

deploybox=function(position,url){


}

movebox=function(position,url){


}


removemovebox=function(url){


}


}

