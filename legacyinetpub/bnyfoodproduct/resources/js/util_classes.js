"use strict";
class AjaxClass {

 constructor(type,url,data) 
 {
        this.type=type;
        this.url="https://apiintrnet.bnyfoodproducts.com/"+url;
        this.data=data;        
 }

 executeAjax=function(){

     $.ajax({
         type: this.type,
         url: this.url, 
         data: this,data,
         dataType: "json",  
         cache:false,
         success: 
              function(data){
                console.log(data);  //as a debugging message.
                return data;
              }
              
          });// you have missed this bracket
     return false;

  });



 }



//============================