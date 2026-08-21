var path_project = "";

		if(window.location.hostname == "localhost"){
			var arr_path = window.location.pathname.split("/");
			path_project = arr_path[1];
		}
//var hostname_site = '';
var hostname_site = 'https://'+window.location.hostname+"/"+path_project;