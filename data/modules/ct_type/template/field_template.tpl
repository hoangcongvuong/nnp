<!-- BEGIN: main -->
<div class="panel panel-default" id="fields-{FIELD.ct_field_id}">
	<div class="panel-heading">
    	<span class="item-title">{LABEL}</span>
        <span class="item-controls">
			<span class="item-type">{FIELD.ct_field_type} field</span>
			<span class="item-edit opened">Edit Field Item</span>
		</span>
    </div>
    <input type="hidden" name="field[{FIELD.ct_field_id}][ct_field_type]" value="{FIELD.ct_field_type}">
    <div class="panel-body clearfix">
    	<div class="vnp-field">
            <label class="vnp-label" for="field_field_name_{FIELD.ct_field_id}">Field name</label>
            <input class="vnp-input " type="text" name="field[{FIELD.ct_field_id}][ct_field_name]" id="field_field_name_{FIELD.ct_field_id}" value="{FIELD.ct_field_name}">
        </div>
        <div class="vnp-field">
            <label class="vnp-label" for="field_field_label_{FIELD.ct_field_id}">Field label</label>
            <input class="vnp-input " type="text" name="field[{FIELD.ct_field_id}][ct_field_label]" id="field_field_label_{FIELD.ct_field_id}" value="{FIELD.ct_field_label}">
        </div>
        <div class="clear"></div>
        <div class="vnp-field">  
            <label class="vnp-label" for="field_require_{FIELD.ct_field_id}">Require</label>
            <select name="field[{FIELD.ct_field_id}][require]" id="field_require_{FIELD.ct_field_id}">
                <option value="0" selected="selected">No</option>
                <option value="1">Yes</option>
            </select>
		</div>
    	<!-- BEGIN: text -->
        <div class="vnp-field">
            <label class="vnp-label" for="field_validation_{FIELD.ct_field_id}">Validation</label>
            <select name="field[{FIELD.ct_field_id}][validation]" id="field_validation_{FIELD.ct_field_id}">
                <option value="none" selected="selected">None</option>
                <option value="email">Email</option>
                <option value="url">URL</option>
                <option value="date">Date</option>
                <option value="number">Number</option>
                <option value="digits">Digits</option>
                <option value="phone">Phone</option>
            </select>
       	</div>
        <div class="vnp-field">
            <label class="vnp-label" for="field_default_value_{FIELD.ct_field_id}">Default value</label>
            <input class="vnp-input " type="text" name="field[{FIELD.ct_field_id}][default_value]" id="field_default_value_{FIELD.ct_field_id}" value="{FIELD.default_value}">
        </div>
    	<!-- END: text -->
        
        <!-- BEGIN: password -->
        <div class="vnp-field">
            <label class="vnp-label" for="field_validation_{FIELD.ct_field_id}">Validation</label>
            <select name="field[{FIELD.ct_field_id}][validation]" id="field_validation_{FIELD.ct_field_id}">
                <option value="none" selected="selected">None</option>
                <option value="email">Email</option>
                <option value="url">URL</option>
                <option value="date">Date</option>
                <option value="number">Number</option>
                <option value="digits">Digits</option>
                <option value="phone">Phone</option>
            </select>
       	</div>
        <div class="vnp-field">
            <label class="vnp-label" for="field_default_value_{FIELD.ct_field_id}">Default value</label>
            <input class="vnp-input " type="text" name="field[{FIELD.ct_field_id}][default_value]" id="field_default_value_{FIELD.ct_field_id}" value="{FIELD.default_value}">
        </div>
    	<!-- END: password -->
        
        <!-- BEGIN: referer -->
        <div class="vnp-field">
            <label class="vnp-label" for="field_referer_{FIELD.ct_field_id}">Referer content type</label>
            <select name="field[{FIELD.ct_field_id}][referer_ct_type]" onchange="getCTfield(this, '#field_referer_title_field_{FIELD.ct_field_id}')" id="field_referer_{FIELD.ct_field_id}">
            	<option value="">Select content type</option>
                <!-- BEGIN: cttype -->
                <option {CTTYPE.selected} value="{CTTYPE.ct_type_id}">{CTTYPE.ct_type_title}</option>
                <!-- END: cttype -->
            </select>
      	</div>
        <div class="vnp-field">
            <label class="vnp-label" for="field_referer_title_field_{FIELD.ct_field_id}">Referer title field</label>
            <select name="field[{FIELD.ct_field_id}][referer_title_field]" id="field_referer_title_field_{FIELD.ct_field_id}">
            	{REF_TITLE_FIELD}
            </select>
        </div>
        <div class="vnp-field">
            <label class="vnp-label" for="field_referer_display_{FIELD.ct_field_id}">Display</label>
            <select name="field[{FIELD.ct_field_id}][referer_display]" id="field_referer_display_{FIELD.ct_field_id}">
            	<option {DISPLAY.select} value="select">Selectbox</option>
            	<option {DISPLAY.radio} value="radio">Radio</option>
                <option {DISPLAY.checkbox} value="checkbox">Checkbox</option>
                <option {DISPLAY.checklist} value="checklist">Checklist</option>
            </select>
        </div>
    	<!-- END: referer -->
        
        <!-- BEGIN: number -->
        <div class="vnp-field">
            <label class="vnp-label" for="field_default_value_{FIELD.ct_field_id}">Default value</label>
            <input class="vnp-input " type="number" name="field[{FIELD.ct_field_id}][default_value]" id="field_default_value_{FIELD.ct_field_id}" value="{FIELD.default_value}">
        </div>
    	<!-- END: number -->
        
        <!-- BEGIN: image -->
        <div class="vnp-field">
            <label class="vnp-label" for="field_default_value_{FIELD.ct_field_id}">Default value</label>
            <input class="vnp-input" type="text" name="field[{FIELD.ct_field_id}][default_value]" id="field_default_value_{FIELD.ct_field_id}" value="{FIELD.default_value}">
        </div>
    	<!-- END: image -->
        
        <!-- BEGIN: file -->
        <div class="vnp-field">
            <label class="vnp-label" for="field_default_value_{FIELD.ct_field_id}">Default value</label>
            <input class="vnp-input" type="text" name="field[{FIELD.ct_field_id}][default_value]" id="field_default_value_{FIELD.ct_field_id}" value="{FIELD.default_value}">
        </div>
    	<!-- END: file -->
        
        <!-- BEGIN: hidden -->
        <div class="vnp-field">
            <label class="vnp-label" for="field_default_value_{FIELD.ct_field_id}">Default value</label>
            <input class="vnp-input" type="text" name="field[{FIELD.ct_field_id}][default_value]" id="field_default_value_{FIELD.ct_field_id}" value="{FIELD.default_value}">
        </div>
    	<!-- END: hidden -->
    
    	<!-- BEGIN: textarea -->
        <div class="vnp-field">
            <label class="vnp-label" for="field_default_value_{FIELD.ct_field_id}">Default value</label>
            <textarea class="vnp-input" style="width: 355px; padding: 10px; height: 50px" type="text" name="field[{FIELD.ct_field_id}][default_value]" id="field_default_value_{FIELD.ct_field_id}">{FIELD.default_value}</textarea>
        </div>
    	<!-- END: textarea -->
        
        <!-- BEGIN: html -->
        <div class="vnp-field">
            <label class="vnp-label" for="field_default_value_{FIELD.ct_field_id}">Default value</label>
            <textarea class="vnp-input" style="width: 355px; padding: 10px; height: 50px" type="text" name="field[{FIELD.ct_field_id}][default_value]" id="field_default_value_{FIELD.ct_field_id}">{FIELD.default_value}</textarea>
        </div>
    	<!-- END: html -->
        
        <!-- BEGIN: option -->
        <h2>Options list </h2><i>Default / Value / Label</i>
        <div class="clear"></div>
        <div class="vnp-field" id="field-option-{FIELD.ct_field_id}">
        	<div class="input-group" id="option-template-{FIELD.ct_field_id}">
            	<span class="input-group-addon">
              		<input type="radio" class="f-df" {OPTION1.checked} value="{OPTION1.key}" name="field[{FIELD.ct_field_id}][default_value]">
            	</span>
            	<input value="{OPTION1.value}" type="text" name="field[{FIELD.ct_field_id}][option][option_0][value]" class="vnp-input f-value">
                <input value="{OPTION1.title}" type="text" name="field[{FIELD.ct_field_id}][option][option_0][title]" class="vnp-input f-title">
                <span class="vnp-remove-option" id="remove-opt-{FIELD.ct_field_id}"><span class="glyphicon glyphicon-remove"></span></span>
          	</div>
            <!-- BEGIN: other -->
            <div class="input-group">
            	<span class="input-group-addon">
              		<input type="radio" class="f-df" {OPTION.checked} value="{OPTION.key}" name="field[{FIELD.ct_field_id}][default_value]">
            	</span>
            	<input value="{OPTION.value}" type="text" name="field[{FIELD.ct_field_id}][option][{OPTION.key}][value]" class="vnp-input f-value">
                <input value="{OPTION.title}" type="text" name="field[{FIELD.ct_field_id}][option][{OPTION.key}][title]" class="vnp-input f-title">
                <span class="vnp-remove-option" id="remove-opt-{FIELD.ct_field_id}"><span class="glyphicon glyphicon-remove"></span></span>
          	</div>
            <!-- END: other -->
     	</div>
        <span class="vnp-add-option btn btn-success" id="add-option-{FIELD.ct_field_id}">
            <span class="glyphicon glyphicon-plus-sign"></span>&nbsp;
            Add option
        </span>
		<!-- END: option -->
        
        <!-- BEGIN: invalid -->
        <p>Invalid field type</p>
        <!-- END: invalid -->
        
        <span class="vnp-remove-field btn btn-danger" id="remove-field-{FIELD.ct_field_id}">
            <span class="glyphicon glyphicon-remove"></span>&nbsp;
            Remove field
        </span>

 	</div>
</div>
<!-- BEGIN: main -->