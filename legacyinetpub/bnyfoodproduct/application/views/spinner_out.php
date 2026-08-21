
<script>
// Initiate an Ajax request on button click


// Add remove loading class on body element depending on Ajax request status
$(document).ready(function() {
    setInterval(set_loading, <?php echo $curl_time;?>);
    //$(".body_loading").removeClass("loading").addClass("noloading");
   // $("#body_loading").addClass("noloading"); 
   function set_loading(){
    $(".body_loading").removeClass("loading").addClass("noloading");
   }
});

</script>