jQuery("#show_hide_chat").click(function(){
  if(document.getElementById('list_chat').style.display == 'none'){
      jQuery("#list_chat").show();
  }
  else {
      jQuery("#list_chat").hide();
  }
});
