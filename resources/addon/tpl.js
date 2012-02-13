var tpl_content_viewwarp = '<div class="form-horizontal"><fieldset>{{{content}}}</fieldset></div>';
var tpl_content_view = '<div class="control-group"><label class="control-label" for="input01">{{label}}</label><div class="controls">{{value}}</div></div>';

var tpl_form = [];
tpl_form.text = '<input type="text" class="{{addclass}}" id="form_{{name}}" value="{{value}}">';
tpl_form.email = '<input type="email" class="{{addclass}}" id="form_{{name}}" value="{{value}}">';
tpl_form.number = '<input type="number" class="{{addclass}}" id="form_{{name}}" value="{{value}}">';
tpl_form.url = '<input type="url" class="{{addclass}}" id="form_{{name}}" value="{{value}}">';
tpl_form.date = '<input type="email" class="{{addclass}}" id="form_{{name}}" value="{{value}}">';
tpl_form.datetime = '<input type="email" class="{{addclass}}" id="form_{{name}}" value="{{value}}">';
tpl_form['time'] = '<input type="email" class="{{addclass}}" id="form_{{name}}" value="{{value}}">';
tpl_form.hidden = '<input type="email" class="{{addclass}}" id="form_{{name}}" value="{{value}}">';
tpl_form.file = '<input type="email" class="{{addclass}}" id="form_{{name}}" value="{{value}}">';
tpl_form.checkbox = '<input type="email" class="{{addclass}}" id="form_{{name}}" value="{{value}}">';
tpl_form.select = '<input type="email" class="{{addclass}}" id="form_{{name}}" value="{{value}}">';
tpl_form.select_switch = '<input type="email" class="{{addclass}}" id="form_{{name}}" value="{{value}}">';

var tpl_form_ctlgroup = '{{#items}}<div class="control-group"><label class="control-label" for="form_{{fieldname}}">{{label}}</label><div class="controls">{{{control}}}<p class="help-block">{{helptext}}</p></div></div>{{/items}}';



