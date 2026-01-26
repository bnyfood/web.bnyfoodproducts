
$(function() {
  $('input[name="daterange"]').daterangepicker({
    opens: 'left'
  }, function(start, end, label) {
    console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
  });
});


$(function() {
  $('#expensedate').datepicker({
    format: 'yyyy-mm-dd',
    todayBtn: 'linked',
    todayHighlight: true,
    autoclose: true
  });

  $('#slipdate').datepicker({
    format: 'yyyy-mm-dd',
    todayBtn: 'linked',
    todayHighlight: true,
    autoclose: true
  });
});

$(".custom-file-input").on("change", function() {
  var fileName = $(this).val().split("\\").pop();
  $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
});