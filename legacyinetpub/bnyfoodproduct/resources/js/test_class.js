var v_id;
var v_1 = 10;
var v_2;

class Robot {

	constructor() {
	    this.someData = 0;
	    this.someData1 = 0;
	    this.someData2 = 10;
	    this.someData3 = 0;
	    this.someData4 = 0;

	    this.log3;

	    this.val1;
	  }

	PlotRobot(id,x,y){

		console.log("id="+id);

		this.someData = id;

		this.someData = this.someData+10;

		console.log("1--->>>"+this.someData);

		this.log();
	}

	log(){

		console.log("2--->>>"+this.someData);

		this.someData2 = this.someData+this.someData2;

		console.log("3--->>>"+this.someData2);

		

	}

	PlotRobot2(id,x,y){

		console.log("id="+id);

		v_id = id;

		v_id = v_id+10;

		console.log("1--->>>"+v_id);

		this.log2();
	}

	log2(){

		console.log("2--->>>"+v_id);

		v_1 = v_id + v_1;

		console.log("3--->>>"+v_1);

	}


	show_log(){

		console.log("3--->>>"+this.someData);
	}

	PlotRobot3(id,x,y){

		var arr_pathc = window.location.pathname.split("/");
		var pahth_project ="";
		if(window.location.hostname == 'localhost'){
		  pahth_project = arr_pathc[1]+"/";
		}

		console.log("id="+id);

		this.someData = id;

		var urls = 'http://'+window.location.hostname+"/"+pahth_project+"category/category_get_by_id";

		const data = {

			flower : async function() {
			  try {
			  	//alert(id)
			  	var arr_pathc = window.location.pathname.split("/");
				var pahth_project ="";
				if(window.location.hostname == 'localhost'){
				  pahth_project = arr_pathc[1]+"/";
				}

			  	var urls = 'http://'+window.location.hostname+"/"+pahth_project+"category/category_get_by_id";
			    const res = await postData(urls,id)
			    console.log(res)
			    return res;
			  } catch(err) {
			    console.log(err);
			  }
			}

		}
		//var aa = data.flower();
		//console.log("aa--->>>"+aa);
		//this.aaa();

		var aa = async_ajax(id);
		console.log("aa--->>>"+aa);

  		function log4() {

		    this.MyMethod = function () {
		      console.log("val2--->>>"+this.val1);
		    }
		    this.MyMethod(); //should now work
		  }

	  		

	  		

		function getData(ajaxurl) { 
		  return $.ajax({
		    url: ajaxurl,
		    type: 'GET',
		  });
		};  

		function postData(ajaxurl,id) { 
			console.log("id_ajax>>"+id);
		  return $.ajax({
		    type: "POST",
		    url: ajaxurl,
		    data:  {parent_cat: id}, 
		    dataType: "json",
		  })
		  .done(function( data ) {  

	  		var cat_data = data.cat_data;

	  		console.log("val--->>>"+cat_data['Title']);
	  
		    this.val1 = cat_data['Title'];
		    console.log("val1--->>>"+this.val1);
		    //this.log3();
		    //this.aaa();
		     
		  });

		}; 

	  	async function async_ajax(id) {
		  try {

		  	var arr_pathc = window.location.pathname.split("/");
			var pahth_project ="";
			if(window.location.hostname == 'localhost'){
			  pahth_project = arr_pathc[1]+"/";
			}

		  	var urls = 'http://'+window.location.hostname+"/"+pahth_project+"category/category_get_by_id";
		    const res = await postData(urls,id)
		    console.log(res)
		  } catch(err) {
		    console.log(err);
		  }
		}


		
	}

	aaa(){

	  		console.log('aaaaaa');
	  		console.log("val2--->>>"+this.val1);
	  	}


	log3(){

		//console.log("val2--->>>"+this.val1);

		this.MyMethod = function () {
         alert('It works');
       }

	}



}

function bbb(){
	alert("123456");
}

function postData(ajaxurl,id) { 
			console.log("id_ajax>>"+id);
		  return $.ajax({
		    type: "POST",
		    url: ajaxurl,
		    data:  {parent_cat: id}, 
		    dataType: "json",
		  })
		  .done(function( data ) {  

	  		var cat_data = data.cat_data;

	  		
		     
		  });

		}; 

	  	async function async_ajax(id) {
		  try {

		  	var arr_pathc = window.location.pathname.split("/");
			var pahth_project ="";
			if(window.location.hostname == 'localhost'){
			  pahth_project = arr_pathc[1]+"/";
			}

		  	var urls = 'http://'+window.location.hostname+"/"+pahth_project+"category/category_get_by_id";
		    const res = await postData(urls,id)
		    console.log(res)
		    return res;
		  } catch(err) {
		    console.log(err);
		  }
		}




document.addEventListener("DOMContentLoaded", get_val_robot);


function get_val_robot(){

	j_draw_robot(48,10,20);

	j_draw_robot(47,30,40);

}

function j_draw_robot(id,x,y){

	const robot = new Robot();
	robot.PlotRobot3(id,x,y);

}

function get_val_robot2(){

	j_draw_robot2(48,10,20);

	j_draw_robot2(47,30,40);

}

function j_draw_robot2(id,x,y){

	const robot = new Robot();
	robot.PlotRobot2(id,x,y);

}

$("#btn_move").click(function () {

	const robot_path = new Robot();

	robot_path.show_log();

});




