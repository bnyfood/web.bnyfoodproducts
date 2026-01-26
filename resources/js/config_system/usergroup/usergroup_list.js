$("#shop_sel").change(function(event) {
		
		shop_id = $(this).val();

        var urls = hostname_site+"/"+"config_system/usergroup/set_shop_search/"+shop_id;

        window.location.href = urls;

	});

$("#manage_group_sel").change(function(event) {
		
		group_id_sel = $(this).val();


        var urls = hostname_site+"/"+"config_system/usergroup/usergroup_manage/"+group_id_sel;

        window.location.href = urls;

	});