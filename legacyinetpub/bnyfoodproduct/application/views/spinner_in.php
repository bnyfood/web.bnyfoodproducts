
<style>
    .overlay{
        display: none;
        position: fixed;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        z-index: 999;
        background: rgba(255,255,255,0.8) url("<?php echo base_url();?>resources/images/loader.gif") center no-repeat;
    }
    .body_loading{
        text-align: center;
    }
    /* Turn off scrollbar when body element has the loading class */
    .body_loading.loading{
        overflow: hidden;   
    }
    /* Make spinner image visible when body element has the loading class */
    .body_loading.loading .overlay{
        display: block;
    }

    .body_loading.noloading{
        display: none;   
    }
</style>
<script src="<?php echo base_url();?>global/vendor/jquery/jquery.js"></script>
<script>
// Initiate an Ajax request on button click


// Add remove loading class on body element depending on Ajax request status
$(document).ready(function() {
    $(".body_loading").addClass("loading");   
});

</script>
	<div class="body_loading">
	    <div class="overlay"></div>
	</div>
