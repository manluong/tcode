jQuery("#show_hide_chat").click(function(){
  if(document.getElementById('list_chat').style.display == 'none'){
      jQuery("#list_chat").show();
      jQuery("#show_hide_chat").addClass('active');
  }
  else {
      jQuery("#list_chat").hide();
      jQuery("#show_hide_chat").removeClass('active');
  }
});
