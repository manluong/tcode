// Chat function

var telcoson = {
    connection: null,
    on_roster: function (iq) {
    },
    jid_to_id: function (jid) {
        return Strophe.getBareJidFromJid(jid)
            .replace("@", "-")
            .replace(".", "-");
    },
	logoff: function(){
		telcoson.connection.disconnect();
                jQuery("list_chat").hide();
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
	insert_contact: function (elem) {
		var jid = elem.find(".roster-jid").text();
		var pres = telcoson.presence_value(elem.find(".roster-contact"));
		var contacts = $("#roster-area li");
		if (contacts.length > 0) {
			var inserted = false;
			contacts.each(function () {
				var cmp_pres = telcoson.presence_value(
					$(this).find(".roster-contact"));
				var cmp_jid = $(this).find(".roster-jid").text();
				if (pres > cmp_pres) {
					$(this).before(elem);
					inserted = true;
					return false;
				} else {
					if (jid < cmp_jid) {
						$(this).before(elem);
						inserted = true;
						return false;
					}
				}
			});
			if (!inserted) {
				$("#roster-area ul").append(elem);
			}
		} else {
			$("#roster-area ul").append(elem);
		}
	},
	on_roster: function (iq) {
                console.log("listed");
		$(iq).find("item").each(function () {
			var jid = $(this).attr("jid");
			var name = $(this).attr("name") || jid;
			var jid_id = telcoson.jid_to_id(jid);
			//var contact = $('<div id="' + jid_id + '" class="offline" onclick="chatWith(\''+jid_id+'\',\''+name+'\')">'+name+'</div>');
                        var contact = $('<div class="chatBoxItem fl pv5 ph10" id="user_'+jid_id+'"><div class="avatar rounded14 fl mr5"><img src="/resources/template/default_web/img/avatar.png" alt="" width="28" class=" rounded14"></div><span class="fl dpb ofh cf1 mt5 fwb">'+name+'</span><div class="tools fr"><a href="#" class="fl mr5 mt3"><i class="iChat iChat3"></i></a><a href="#" class="fl w18 mt7" style="display:none;"><input type="checkbox" class="styled" /></i></a></div></div>');
                        //console.log(contact);
			jQuery("#list_chat div.chatBoxIner").append(contact);
			//telcoson.insert_contact(contact)
				});
               // var contact = $('<div class="chatBoxItem fl pv5 ph10"><div class="avatar rounded14 fl mr5"><span class="rounded14 cf4 bg4 fwb noAvatar tac vam dpib">GP</span></div><span class="fl dpb ofh cf2 mt5 fwb">Group Chat</span><div class="tools fr"><a href="#" class="fl mr5 mt3"><i class="iChat iChat3"></i></a></div></div>');
               // jQuery("#list_chat div.chatBoxIner").append(contact);
                jQuery("#list_chat").removeAttr('style');
		// set up presence handler and send initial presence
		telcoson.connection.addHandler(telcoson.on_presence, null, 'presence');
		telcoson.connection.send($pres());
	},
	on_presence: function (presence) {
		var ptype = $(presence).attr('type');
		var from = $(presence).attr('from');
                //console.log(from);
		var to = $(presence).attr('to');
		if (ptype !== 'error') {
                    	var contact = $('#user_' + telcoson.jid_to_id(from) + ' i')
				.removeClass('iChat9')
				.removeClass('iChat2')
				.removeClass('iChat3');
			if (ptype === 'unavailable') {
				contact.addClass('iChat3');
                               // console.log(from + ' Offline');
				//jQuery("#chatbox_"+telcoson.jid_to_id(from)+" .chatboxtitle").addClass('offline2');
			} else {
				var show = $(presence).find('show').text();
				if (show === '' || show === 'chat') {

					contact.addClass('iChat2');
                                        //console.log(from + ' Online');
					//jQuery("#chatbox_"+telcoson.jid_to_id(from)+" .chatboxtitle").addClass('online2');
				} else {
					contact.addClass('iChat9');
                                        //console.log(from + ' Busy');
					//jQuery("#chatbox_"+telcoson.jid_to_id(from)+" .chatboxtitle").addClass('away2');
				}
			}
			//var li = contact.parent();
			//li.remove();


			//telcoson.insert_contact(li);
		}
		return true;
	},
	on_message: function (message) {
		var jid = telcoson.jid_to_id(Strophe.getBareJidFromJid($(message).attr("from")));

		var composing = $(message).find("composing");

		if (composing.length > 0) {
			if(jQuery("#typing").length == 0){
				body = "<li id='typing'><b>"+jQuery("#"+jid).html()+": </b>Typing....<img src='"+jQuery("#base_url").val()+"images/pencil.png' /></li>";
				jQuery("#chatbox_"+jid+" .chatboxcontent ul").append(body);
			}
			var draf= ''
			jQuery.each($(message).find("body"),function(index,value){
				if(index == 0){
					draf = jQuery(this).text();
				}
			});
			if(draf == ''){
				jQuery("#typing").remove();
			}
		}
		else {
		chatWith(jid,jQuery("#"+jid).html());
		var body = '';
			jQuery("#typing").remove();
			jQuery.each($(message).find("body"),function(index,value){
				if(index == 0){
					body = jQuery(this).text();
				}
			});
			if(body != '')
				body = "<li><b>"+jQuery("#"+jid).html()+": </b>"+body+"</li>";

			//console.log("#chatbox_"+jid+" .chatboxcontent");
			jQuery("#chatbox_"+jid+" .chatboxcontent ul").append(body);
		 }
		 //console.log(jid);
			if(jQuery("#chatbox_"+jid+" .chatboxcontent").length > 0)
				$("#chatbox_"+jid+" .chatboxcontent").animate({scrollTop: $("#chatbox_"+jid+" .chatboxcontent")[0].scrollHeight});

		return true;
	},

};
// End chat function



jQuery(document).ready(function(){
   // effect
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
   //
   // Chat
   if(jQuery("#chat").length > 0){
    $(document).trigger("connect", {
                user: 'test1',
                company: 'company1'
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
