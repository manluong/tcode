var tpl_content_viewwarp = '<div class="form-horizontal"><fieldset>{{{content}}}</fieldset></div>';
var tpl_content_view = '<div class="control-group"><label class="control-label" for="input01">{{label}}</label><div class="controls">{{value}}</div></div>';

var tpl_form = [];
tpl_form.text = '<input type="text" class="{{addclass}}" id="form_{{name}}" name="{{name}}" value="{{value}}"{{#required}} required="required"{{/required}}>';
tpl_form.email = '<input type="email" class="{{addclass}}" id="form_{{name}}" name="{{name}}" value="{{value}}"{{required}}>';
tpl_form.number = '<input type="number" class="{{addclass}}" id="form_{{name}}" name="{{name}}" value="{{value}}">';
tpl_form.url = '<input type="url" class="{{addclass}}" id="form_{{name}}" name="{{name}}" value="{{value}}">';
tpl_form.checkbox = '<input type="checkbox" id="form_{{name}}" name="{{name}}" value="{{value}}">';
tpl_form.textarea = '<textarea class="{{addclass}}" id="form_{{name}}" name="{{name}}" rows="3">{{value}}</textarea>';

tpl_form.phone = '<input type="number" class="{{addclass}}" id="form_{{name}}" name="{{name}}" value="{{value}}">';

tpl_form.date = '<input type="date" class="{{addclass}} form-dateinput" id="form_{{name}}" name="{{name}}" value="{{value}}">';
tpl_form.datetime = '<input type="date" class="{{addclass}} form-dateinput" id="form_{{name}}" name="{{name}}" value="{{value}}">';
tpl_form['time'] = '<input type="time" class="{{addclass}} form-dateinput" id="form_{{name}}" name="{{name}}" value="{{value}}">';

tpl_form.select = '<select id="select01" name="{{name}}">{{#select_options}}<option value="{{value}}"{{selected}}>{{value}}</option>{{/select_options}}</select>';              
tpl_form.select_switch = '';

tpl_form.password = '<input type="password" class="{{addclass}}" id="form_{{name}}" name="{{name}}" value="{{value}}" autocomplete="off">';
tpl_form.file = '<input class="input-file" id="form_{{name}}" name="{{name}}" type="file">';
tpl_form.hidden = '<input type="hidden" id="form_{{name}}" name="{{name}}" value="{{value}}">';


var tpl_form_ctlgroup = '<div class="formview"><form class="form-horizontal" id="formid_{{divid}}" name="formid_{{divid}}" novalidate>{{#items}}<div class="control-group"><label class="control-label" for="form_{{fieldname}}">{{label}}</label><div class="controls">{{{control}}}<p class="help-block">{{helptext}}</p></div></div>{{/items}}{{{links}}}</form></div>';

var tpl_link = [];
tpl_link.submit = ' <button type="submit" class="btn btn-primary">{{text}}</button>';
tpl_link.reset = ' <button type="reset" class="btn">{{text}}</button>';
tpl_link.ajax = ' <a class="btn{{#style}} btn-{{style}}{{/style}}" href="#" onclick="ajax_content(\'{{url}}\',\'{{target}}\');">{{text}}</a>';
tpl_link.page = ' <a class="btn{{#style}} btn-{{style}}{{/style}}" href="{{url}}">{{text}}</a>';
tpl_link.warp = '<div class="form-actions">{{{links}}}</div>';


          
          