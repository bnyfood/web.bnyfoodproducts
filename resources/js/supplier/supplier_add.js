
$(document).ready(function(){

$('#supplier_headoffice').change(function(){
            if($(this).prop("checked") == true){
                console.log("Checkbox is checked.");
                document.getElementById("branchid").style.display = "none";
            }
            else if($(this).prop("checked") == false){
                console.log("Checkbox is unchecked.");
                document.getElementById("branchid").style.display = "contents";

            }
        });
});