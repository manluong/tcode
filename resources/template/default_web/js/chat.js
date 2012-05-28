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
		leo9x.connection.disconnect();
		jQuery(".chatbox").hide();
		jQuery(".chatboxcontent").html('');
	},
	status: function(status){
		if(status != 'offline'){
			var status = $pres().c('show').t(status);
			leo9x.connection.send(status);
		}
		else {
			jQuery("#chatbox_List .chatboxtitle select").val('chat');
			if(confirm("Are you sure you want to logoff ? ")){
				leo9x.logoff();
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
		var pres = leo9x.presence_value(elem.find(".roster-contact"));
		var contacts = $("#roster-area li");
		if (contacts.length > 0) {
			var inserted = false;
			contacts.each(function () {
				var cmp_pres = leo9x.presence_value(
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
		$(iq).find("item").each(function () {
			var jid = $(this).attr("jid");
			var name = $(this).attr("name") || jid;
			var jid_id = leo9x.jid_to_id(jid);
			var contact = $('<div id="' + jid_id + '" class="offline" onclick="chatWith(\''+jid_id+'\',\''+name+'\')">'+name+'</div>');
			jQuery("#chatbox_List .chatboxcontent").append(contact);
			//leo9x.insert_contact(contact)
				});
		// set up presence handler and send initial presence
		leo9x.connection.addHandler(leo9x.on_presence, null, 'presence');
		leo9x.connection.send($pres());
	},
	on_presence: function (presence) {
		var ptype = $(presence).attr('type');
		var from = $(presence).attr('from');
		var to = $(presence).attr('to');
		if (ptype !== 'error') {
			var contact = $('#' + leo9x.jid_to_id(from))
				.removeClass('online')
				.removeClass('away')
				.removeClass('offline');
			jQuery("#chatbox_"+leo9x.jid_to_id(from)+" .chatboxtitle").removeClass('online2').removeClass('away2')
				.removeClass('offline2');
			if (ptype === 'unavailable') {
				contact.addClass('offline');
				jQuery("#chatbox_"+leo9x.jid_to_id(from)+" .chatboxtitle").addClass('offline2');
			} else {
				var show = $(presence).find('show').text();
				if (show === '' || show === 'chat') {
					contact.addClass('online');
					jQuery("#chatbox_"+leo9x.jid_to_id(from)+" .chatboxtitle").addClass('online2');
				} else {
					contact.addClass('away');
					jQuery("#chatbox_"+leo9x.jid_to_id(from)+" .chatboxtitle").addClass('away2');
				}
			}
			//var li = contact.parent();
			//li.remove();


			//leo9x.insert_contact(li);
		}
		return true;
	},
	on_message: function (message) {
		var jid = leo9x.jid_to_id(Strophe.getBareJidFromJid($(message).attr("from")));

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
   $(document).trigger("connect", {
                    user: 'test1',
                    company: 'company1'
                });

   //



});
$(document).bind("connect", function (ev, data) {
    var conn = new Strophe.Connection("/welcome/forward");
	//var conn = new Strophe.Connection("http://proxy.leo9x.co.cc:5280/http-bind");
	conn.connect(data.user, data.company, function (status) {
        if (status === Strophe.Status.CONNECTED) {
            $(document).trigger("connected");
        } else if (status === Strophe.Status.DISCONNECTED) {
            $(document).trigger("disconnected");
        }
    });
    telcoson.connection = conn;
});
