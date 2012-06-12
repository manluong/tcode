// Chat function

var telcoson = {
    connection: null,
    company : '',
    username :'',
    jid_to_id: function (jid) {
        return Strophe.getBareJidFromJid(jid)
            .replace("@", "-")
            .replace(".", "-");
    },
	logoff: function(){
		telcoson.connection.disconnect();
                jQuery("#list_chat").hide();
                jQuery("#show_hide_chat").removeClass('active');
                jQuery(".ac .chatBoxIner").hide();
                jQuery(".ac").remove();
                jQuery("#list_chat div.userchat").remove();
                
	},
	status: function(status){
		if(status != 'offline'){
			var status = $pres().c('show').t(status);
			telcoson.connection.send(status);
		}
		else {
			if(confirm("Are you sure you want to logoff ? ")){
				telcoson.logoff();
			}

		}
	},
	id_to_jid: function (id) {
        return   id.replace("-", "@")
            .replace("-", ".");
    },
    presence_value: function (elem) {
    if (elem.hasClass("online")) {
        return 2;
    } else if (elem.hasClass("away")) {
        return 1;
            }
    return 0;
	},
	on_roster: function (iq) {
                console.log("listed");
		$(iq).find("item").each(function () {
			var jid = $(this).attr("jid");
			var name = $(this).attr("name") || jid;
			var jid_id = telcoson.jid_to_id(jid);
                        var contact = $('<div class="chatBoxItem fl pv5 ph10 userchat" id="user_'+jid_id+'" onclick="openChat(\''+jid_id+'\',\''+name+'\');"><div class="avatar rounded14 fl mr5"><img src="/resources/template/default_web/img/avatar.png" alt="" width="28" class=" rounded14"></div><span class="fl dpb ofh cf1 mt5 fwb">'+name+'</span><div class="tools fr"><a href="#" class="fl mr5 mt3"><i class="iChat iChat3"></i></a><a href="#" class="fl w18 mt7" style="display:none;"><input type="checkbox" class="" /></a></div></div>');
			jQuery("#list_chat div.chatScroll").append(contact);
			//telcoson.insert_contact(contact)
				});
		telcoson.connection.addHandler(telcoson.on_presence, null, 'presence');
		telcoson.connection.send($pres());
	},
	on_presence: function (presence) {
		var ptype = $(presence).attr('type');
                console.log(ptype);
		var from = $(presence).attr('from');
		var to = $(presence).attr('to');
		if (ptype !== 'error') {
                    	var contact = $('#user_' + telcoson.jid_to_id(from) + ' i')
				.removeClass('iChat9')
				.removeClass('iChat2')
				.removeClass('iChat3');
			if (ptype === 'unavailable' ) {
				contact.addClass('iChat3');
                        } else {
				var show = $(presence).find('show').text();
                                if (show === '' || show === 'chat') {
					contact.addClass('iChat2');
				} else {
                                    if(show === 'invisible'){
                                        contact.addClass('iChat3');
                                    }
                                    else {
                                        contact.addClass('iChat9');
                                    }
					
 				}
			}
		}
		return true;
	},
	on_message: function (message) {
		var jid = telcoson.jid_to_id(Strophe.getBareJidFromJid($(message).attr("from")));

		var composing = $(message).find("composing");
                var name = jQuery("#user_"+jid+" span").html();
		if (composing.length > 0) {
                       
			if(jQuery("#chat_"+jid+" .typing").length == 0){
				var chatMess = '';
                                chatMess += '<div class="chatBoxItem fl pv1 ph10 typing">';
                                chatMess += '<div class="avatar rounded14 fl mr10"><img width="28" class=" rounded14" alt="" title="'+name+'" src="/resources/template/default_web/img/avatar.png"></div>';
                                chatMess += '<span class="fl dpb ofh cf1 mt5 w80p">';
                                chatMess += '<img class=" rounded14" alt="" title="'+name+'" src="/resources/template/default_web/img/typing.gif"><br>';
                                chatMess += '</span>';
                                chatMess += '</div>';
                                jQuery("#chat_"+jid+" .chatScroll").append(chatMess);
                                if( jQuery("#chat_"+jid).length > 0 )
                                    $("#chat_"+jid+" .chatScroll").animate({scrollTop: $("#chat_"+jid+" .chatScroll")[0].scrollHeight});
			}
			var draf= ''
			jQuery.each($(message).find("body"),function(index,value){
				if(index == 0){
					draf = jQuery(this).text();
				}
			});
			if(draf == ''){
				jQuery("#chat_"+jid+" .typing").remove();
			}
		}
		else {

                var body = '';
			jQuery("#chat_"+jid+" .typing").remove();
			jQuery.each($(message).find("body"),function(index,value){
				if(index == 0){
					body = jQuery(this).text();
				}
			});
                        if(body != '')
                            chatWith(jid,jQuery("#user_"+jid+" span").html(),body);

		 }


		return true;
	}

};
// End chat function



jQuery(document).ready(function(){
   // effect
    jQuery("#show_hide_chat").click(function(){
        if(document.getElementById('list_chat').style.display == 'none'){
            if(jQuery("#show_hide_chat i").hasClass('iChat7')){
                $(document).trigger("connect", {
                 user: telcoson.username,
                company: telcoson.company
                });
                jQuery("#set_status").hide();
                jQuery("#chat_status").html('Online');
                jQuery("#show_hide_chat i").removeClass('iChat7').addClass('iChat1');
            }
            jQuery("#list_chat").show();
            jQuery("#show_hide_chat").addClass('active');
            jQuery(".ac .chatBoxIner").hide();
            jQuery(".ac .chatItem").removeClass('active');
        }
        else {
            jQuery("#list_chat").hide();
            jQuery("#show_hide_chat").removeClass('active');
        }
    });
    window.isActive = true;
    jQuery(window).focus(function() {this.isActive = true;});
    jQuery(window).blur(function() {this.isActive = false;});
   //
   // Chat
   if(jQuery("#chat").length > 0){
       jQuery.post("/chat/get",{},function(data){
          if(data == 'You do not have the permissions to access this app'){
              console.log('You do not have the permissions to access this app');
          } 
          else {
              var user_array = jQuery.parseJSON(data);
              var username = user_array.id;
              var company = user_array.domain;
              telcoson.username = username;
              telcoson.company = company;
            $(document).trigger("connect", {
                user: telcoson.username,
                company: telcoson.company
            });
          }
       });

   }


   //



});
$(document).bind("connect", function (ev, data) {
        //var conn = new Strophe.Connection("/chat/forward");
	var conn = new Strophe.Connection("http://proxy.leo9x.co.cc:5280/http-bind");
	conn.connect(data.user, data.company, function (status) {
        if (status === Strophe.Status.CONNECTED) {
            $(document).trigger("connected");
        } else if (status === Strophe.Status.DISCONNECTED) {
            $(document).trigger("disconnected");
        }
    });
    telcoson.connection = conn;
});
$(document).bind("connected", function () {
	//list();
    console.log("connected");
    jQuery("#chat").removeAttr('style');
    var iq = $iq({type: "get"}).c("query", {xmlns: "jabber:iq:roster"});
    telcoson.connection.sendIQ(iq, telcoson.on_roster);
	telcoson.connection.addHandler(telcoson.on_message,
                           null, "message", "chat");
});
$(document).bind("disconnected", function () {
    // nothing here yet
});
// ---------- more function ------------ //
jQuery("#chat_status").click(function(){
    jQuery("#set_status").slideDown();
});
jQuery("#set_status a.mt15").click(function(){
    jQuery("#set_status").slideUp();
});
jQuery("#status_online").click(function(){
    telcoson.status('chat');
    jQuery("#show_hide_chat i").removeClass('iChat1').removeClass('iChat7').removeClass('iChat8').addClass('iChat1');
    jQuery("#chat_status").html('Online');
    jQuery("#set_status").slideUp();
});
jQuery("#status_busy").click(function(){
    telcoson.status('away');
    jQuery("#show_hide_chat i").removeClass('iChat1').removeClass('iChat7').removeClass('iChat8').addClass('iChat8');
    jQuery("#chat_status").html('Do Not Distrub');
    jQuery("#set_status").slideUp();
});
jQuery("#status_offline").click(function(){
    telcoson.status('offline');
    jQuery("#show_hide_chat i").removeClass('iChat1').removeClass('iChat7').removeClass('iChat8').addClass('iChat7');
    jQuery("#chat_status").html('Offline');
    jQuery("#set_status").slideUp();
});
// make chat window
function chatWith(id,name,body){
    if(!checkWindow()){
        var mySound = new buzz.sound( "/resources/sound/chat", {
        formats: [ "wav"]
        });
        mySound.play();
    }
    if(jQuery("#chat_"+id).length > 0){
		if(jQuery.trim(jQuery("#chat_"+id+" .chatBoxIner").attr('style')) == 'display: none;'){
			if(jQuery.trim(jQuery("#chat_"+id+" .count").attr('style')) == 'display: none;'){
				var count = 1;
				jQuery("#chat_"+id+" .count").removeAttr('style');
			}
			else {
				var count = parseInt(jQuery("#chat_"+id+" .count").html())+1;
			}
			jQuery("#chat_"+id+" .count").html(count);
                        
		}
		
			var chatMess = '';
			chatMess += '<div class="chatBoxItem fl pv1 ph10">';
                        chatMess += '<div class="avatar rounded14 fl mr10"><img width="28" class=" rounded14" alt="" title="'+name+'" src="/resources/template/default_web/img/avatar.png"></div>';
                        chatMess += '<span class="fl dpb ofh cf1 mt5 w80p">';
                        chatMess += body+'<br>';
                        chatMess += '</span>';
                        chatMess += '</div>';
			jQuery("#chat_"+id+" .chatScroll").append(chatMess);
			$("#chat_"+id+" .chatScroll").animate({scrollTop: $("#chat_"+id+" .chatScroll")[0].scrollHeight});
		
    }
    else {
                
		jQuery(".ac .chatBoxIner").hide();
		jQuery(".ac .chatItem").removeClass('active');
        // create chat area
                var chat = '';
                chat += '<div class="chatItemWrapper por fl mr1 ac" id="chat_'+id+'" >';
                chat += '<div class="chatItem fl cp h50 ph10 por active" onclick="selectChat(\''+id+'\');">';
		chat += '<span class="count bg2 fs12 fwb tac rounded7 lhn poa dpb" style="display: none;"></span>';
                chat += '<a href="javascript:void(0);" class="dpb mt10">';
                chat += '<div class="avatar rounded14 fl mr5"><img src="/resources/template/default_web/img/avatar.png" alt="" width="28" class="rounded14"></div>';
                chat += '<span class="fl dpb ofh cf1 mt5 fwb">'+name+'</span>';
                chat += '</a>';
                chat += '</div>';
                chat += '</div>';
        
        jQuery(".chatSlider").append(chat);
        //----------- Add first Messenge ---------------
        var mess = '';
            mess += '<div class="chatBox poa ">';
            mess += '<div class="chatBoxIner pb5 rounded7 fl abigChat" style="">';
            mess += '<div class="chatBoxItem fl pv5 ph10 bgN">';
            mess += '<div class="fr">';
            mess += '<a href="javascript:void(0);" onclick="min(\''+id+'\');"><i class="iChat iChat5 fl mr10 mt5"></i></a>';
            mess += '<a href="javascript:void(0);" onclick="chat_close(\''+id+'\');"><i class="iChat iChat6 fl"></i></a>';
            mess += '</div>';
            mess += '</div>';
            mess += '<div class="chatScroll">';
            mess += '<div class="chatBoxItem fl pv1 ph10">';
            mess += '<div class="avatar rounded14 fl mr10"><img src="/resources/template/default_web/img/avatar.png" alt=""  title="'+name+'"  width="28" class=" rounded14"></div>';
            mess += '<span class="fl dpb ofh cf1 mt5 w80p">';
            mess += body + '<br />';
            mess += '</span>';
            mess += '</div>';
            mess += '</div>';
            mess += '<div class="chatBoxItem fl pv1 ph10 bgN mess">';
            mess += '<input class="inv-field w95p mt10" type="text" onclick="value=\'\'" onblur="if(value==\'\'){value=\'This is description\'};" value="This is description" onkeyup="sendMess(event,\''+id+'\',this.value)">';
            mess += '</div>'
            mess += '</div>';
            mess += '</div>';
            jQuery("#chat_"+id).prepend(mess);
    }
    jQuery("#list_chat").hide();
    jQuery("#show_hide_chat").removeClass('active');
    jQuery("#chat_"+id+" .typing").remove();
}
function min(id){
        jQuery("#chat_"+id+" .active").removeClass('active');
	jQuery("#chat_"+id+" .chatBoxIner").hide();
}
function chat_close(id){
    jQuery("#chat_"+id).remove();
}
function selectChat(id){
        if(jQuery.trim(jQuery("#chat_"+id+" .chatBoxIner").attr('style')) == 'display: none;'){
		jQuery(".ac .chatBoxIner").hide();
		jQuery(".ac .chatItem").removeClass('active');
		jQuery("#chat_"+id+" .chatBoxIner").show();
		jQuery("#chat_"+id+" .cp").addClass('active');
                jQuery("#chat_"+id+" .count").html('');
                jQuery("#chat_"+id+" .count").hide();
                jQuery("#list_chat").hide();
                jQuery("#show_hide_chat").removeClass('active');
	}
	else {
		jQuery("#chat_"+id+" .chatBoxIner").hide();
		jQuery("#chat_"+id+" .active").removeClass('active');
	}
}
function sendMess(event,id,mess){
    if(event.keyCode == 13){
        var jid = telcoson.id_to_jid(id);
        mess = mess.replace(/^\s+|\s+$/g,"");
                  telcoson.connection.send($msg({
                            to: jid,
                            "type": "chat"
                    }).c("body").t(mess));
            jQuery("#chat_"+id+" .mess input").val('');
        var chatMess = '';
            chatMess += '<div class="chatBoxItem fl pv1 ph10">';
            chatMess += '<div class="avatar rounded14 fl mr10"><img width="28" class=" rounded14" alt="" title="Me" src="/resources/template/default_web/img/avatar.png"></div>';
            chatMess += '<span class="fl dpb ofh cf1 mt5 w80p">';
            chatMess += mess+'<br>';
            chatMess += '</span>';
            chatMess += '</div>';
            if(jQuery("#chat_"+id+" .typing").length > 0)
                jQuery(chatMess).insertBefore("#chat_"+id+" .typing");
            else
                jQuery("#chat_"+id+" .chatScroll").append(chatMess);
            $("#chat_"+id+" .chatScroll").animate({scrollTop: $("#chat_"+id+" .chatScroll")[0].scrollHeight});
    }
    else {
            message = mess.replace(/^\s+|\s+$/g,"");
            var jid = telcoson.id_to_jid(id);
            var notify = $msg({to: jid, "type": "chat"})
            .c("composing", {xmlns: "http://jabber.org/protocol/chatstates"}).c("body").t(message);
            telcoson.connection.send(notify);
    }
}
function openChat(id,name){
    	jQuery(".ac .chatBoxIner").hide();
	jQuery(".ac .chatItem").removeClass('active');
        // create chat area
        var chat = '';
        chat += '<div class="chatItemWrapper por fl mr1 ac" id="chat_'+id+'" >';
        chat += '<div class="chatItem fl cp h50 ph10 por active" onclick="selectChat(\''+id+'\');">';
        chat += '<span class="count bg2 fs12 fwb tac rounded7 lhn poa dpb" style="display: none;"></span>';
        chat += '<a href="javascript:void(0);"  class="dpb mt10">';
        chat += '<div class="avatar rounded14 fl mr5"><img src="/resources/template/default_web/img/avatar.png" alt="" width="28" class="rounded14"></div>';
        chat += '<span class="fl dpb ofh cf1 mt5 fwb">'+name+'</span>';
        chat += '</a>';
        chat += '</div>';
        chat += '</div>';
        if(jQuery("#chat_"+id).length == 0){
        jQuery(".chatSlider").append(chat);
        //----------- Add first Messenge ---------------
        var mess = '';
        mess += '<div class="chatBox poa ">';
        mess += '<div class="chatBoxIner pb5 rounded7 fl abigChat" style="">';
        mess += '<div class="chatBoxItem fl pv5 ph10 bgN">';
        mess += '<div class="fr">';
        mess += '<a href="javascript:void(0);" onclick="min(\''+id+'\');"><i class="iChat iChat5 fl mr10 mt5"></i></a>';
        mess += '<a href="javascript:void(0);" onclick="chat_close(\''+id+'\');"><i class="iChat iChat6 fl"></i></a>';
        mess += '</div>';
        mess += '</div>';
        mess += '<div class="chatScroll">';
        mess += '</div>';
        mess += '<div class="chatBoxItem fl pv1 ph10 bgN mess">';
        mess += '<input class="inv-field w95p mt10" type="text" onclick="value=\'\'" onblur="if(value==\'\'){value=\'This is description\'};" value="This is description" onkeyup="sendMess(event,\''+id+'\',this.value)">';
        mess += '</div>'
        mess += '</div>';
        mess += '</div>';
        jQuery("#chat_"+id).prepend(mess);
        jQuery("#list_chat").hide();
        jQuery("#show_hide_chat").removeClass('active');
        }
        else {
            selectChat(id);
        }
            
}
function checkWindow(){
    return window.isActive;
}