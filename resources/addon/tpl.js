var tpl_content_viewwarp = '<div class="form-horizontal"><fieldset>{{{content}}}</fieldset></div>';
var tpl_content_view = '<div class="control-group"><label class="control-label" for="input01">{{label}}</label><div class="controls">{{value}}</div></div>';

var tpl_form = [];
tpl_form.text = '<input type="text" class="{{addclass}}" id="form_{{name}}" name="{{name}}" value="{{value}}"{{#required}} required="required"{{/required}}>';
tpl_form.email = '<input type="email" class="{{addclass}}" id="form_{{name}}" name="{{name}}" value="{{value}}"{{#required}} required="required"{{/required}}>';
tpl_form.number = '<input type="number" class="{{addclass}}" id="form_{{name}}" name="{{name}}" value="{{value}}"{{#required}} required="required"{{/required}}>';
tpl_form.url = '<input type="url" class="{{addclass}}" id="form_{{name}}" name="{{name}}" value="{{value}}"{{#required}} required="required"{{/required}}>';
tpl_form.checkbox = '<input type="checkbox" id="form_{{name}}" name="{{name}}" value="{{value}}"{{#required}} required="required"{{/required}}>';
tpl_form.checkbox_switch = '';
tpl_form.textarea = '<textarea class="{{addclass}}" id="form_{{name}}" name="{{name}}" rows="3"{{#required}} required="required"{{/required}}>{{value}}</textarea>';
tpl_form.textarea_html = '';

tpl_form.phone = '<input type="number" class="{{addclass}}" id="form_{{name}}" name="{{name}}" value="{{value}}"{{#required}} required="required"{{/required}}>';

tpl_form.date = '<input type="date" class="{{addclass}} form-dateinput" id="form_{{name}}" name="{{name}}" value="{{value}}"{{#required}} required="required"{{/required}}>';
tpl_form.datetime = '<input type="date" class="{{addclass}} form-dateinput" id="form_{{name}}" name="{{name}}" value="{{value}}"{{#required}} required="required"{{/required}}>';
tpl_form['time'] = '<input type="time" class="{{addclass}} form-dateinput" id="form_{{name}}" name="{{name}}" value="{{value}}"{{#required}} required="required"{{/required}}>';

tpl_form.select = '<select id="select01" name="{{name}}">{{#select_options}}<option value="{{value}}"{{selected}}>{{value}}</option>{{/select_options}}</select>';
tpl_form.select_switch = '';
tpl_form.select_multi = '';

tpl_form.password = '<input type="password" class="{{addclass}}" id="form_{{name}}" name="{{name}}" value="{{value}}" autocomplete="off"{{#required}} required="required"{{/required}}>';
tpl_form.file = '<input class="input-file" id="form_{{name}}" name="{{name}}" type="file">';
tpl_form.hidden = '<input type="hidden" id="form_{{name}}" name="{{name}}" value="{{value}}">';


var tpl_form_ctlgroup = '<div class="formview"><form class="form-horizontal" id="formid_{{divid}}" name="formid_{{divid}}" novalidate>{{#items}}<div class="control-group"><label class="control-label" for="form_{{fieldname}}">{{label}}</label><div class="controls">{{{control}}}<p class="help-block">{{helptext}}</p></div></div>{{/items}}{{{links}}}</form></div>';

var tpl_link = [];
tpl_link.submit = ' <button type="submit" class="btn btn-primary">{{text}}</button>';
tpl_link.reset = ' <button type="reset" class="btn">{{text}}</button>';
tpl_link.ajax = ' <a class="btn{{#style}} btn-{{style}}{{/style}}" href="#" onclick="ajax_content(\'{{url}}\',\'{{target}}\');">{{#icon}}<i class="icon-{{icon}} icon-white"></i> {{/icon}}{{text}}</a>';
tpl_link.page = ' <a class="btn{{#style}} btn-{{style}}{{/style}}" href="{{url}}">{{text}}</a>';
tpl_link.warp = '<div class="form-actions">{{{links}}}</div>';

tpl_c_stdwidget = '<div class="widget">{{#title}}<div class="widget-header"><h4>{{title}}</h4></div>{{/title}}<div class="widget-body">{{{content}}}</div></div>';

var custom_viewcard = "My {{#content}}{{card_fname_label}} is {{card_fname_value}}!{{{links}}}{{/content}}<br>";
var custom_editcard = "My {{#content}}{{#card_fname}}{{label}} is {{{control}}}{{/card_fname}}!{{{links}}}{{/content}}<br>";

var tpl_comments = [];
tpl_comments.reply = '<div class="{{reply_class}} reply row-fluid">{{#card_info}}'+
							'<div class="span1"><img class="avatar" src="{{avatar}}" /></div>'+
							'<div class="span11">'+
								'<div class="name">{{card_fname}} {{card_lname}}</div>{{/card_info}}'+
								'<div class="text">{{text}}</div>'+
								'<span class="displaydate" title="{{created_stamp_iso8601}}">{{created_stamp_iso}}</span>'+
							'</div>'+
						'</div>';

tpl_comments.comment = '<div class="comment row-fluid" id="comment_{{id}}">{{#card_info}}'+
							'<div class="span1"><img class="avatar" src="/resources/template/default_web/img/placeholder-image.jpg" /></div>'+
							'<div class="span11">'+
								'<div class="name">{{card_fname}} {{card_lname}}</div>{{/card_info}}'+
								'<div class="text">{{text}}</div>'+
								'<div class="controls">'+
									'<span class="displaydate" title="{{created_stamp_iso8601}}">{{created_stamp_iso}}</span>'+
								'</div>'+
								'{{{show_more_replies}}}<div class="replies">'+
									'{{{replies_html}}}<div class="new_reply row-fluid">'+
										'<input type="text" name="text" class="comment_input span12" value="" placeholder="reply..." autocomplete="off" data-app_id="{{app_id}}" data-app_data_id="{{app_data_id}}" data-parent_id="{{id}}" />'+
									'</div>'+
								'</div>'+
							'</div>'+
						'</div>';

var tpl_dashboard = [];
tpl_dashboard.post = '<div class="post">{{card_name}} - {{text}} - {{furi}}</div>';