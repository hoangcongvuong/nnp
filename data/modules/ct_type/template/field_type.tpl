<!-- BEGIN: main -->
<div class="vnp-box vnp-ct-field-type">
    <ul class="vnp-fields" id="vnp-form-builder">
        <li><a href="#" class="vnp-form-field-type" id="field-type-fieldset">Fieldset</a></li>
        <li><a href="#" class="vnp-form-field-type" id="field-type-section">Section</a></li>
        <li><a href="#" class="vnp-form-field-type" id="field-type-text"><b></b>Text</a></li>
        <li><a href="#" class="vnp-form-field-type" id="field-type-textarea"><b></b>Textarea</a></li>
        <li><a href="#" class="vnp-form-field-type" id="field-type-checkbox"><b></b>Checkbox</a></li>
        <li><a href="#" class="vnp-form-field-type" id="field-type-select"><b></b>Select</a></li>
        <li><a href="#" class="vnp-form-field-type" id="field-type-radio"><b></b>Radio</a></li>
        <li><a href="#" class="vnp-form-field-type" id="field-type-html"><b></b>HTML</a></li>
        <li><a href="#" class="vnp-form-field-type" id="field-type-file"><b></b>File Upload</a></li>
        <li><a href="#" class="vnp-form-field-type" id="field-type-hidden"><b></b>Hidden</a></li>
        <li><a href="#" class="vnp-form-field-type" id="field-type-image"><b></b>Image</a></li>
        <li><a href="#" class="vnp-form-field-type" id="field-type-number"><b></b>Number</a></li>
        
        <li style="width: 100%;height: 3px;margin: 10px 0 5px 0"></li>
        <li><a href="#" class="vnp-form-field-type" id="field-type-referer"><b></b>Referer</a></li>
        <li><a href="#" class="vnp-form-field-type" id="field-type-password"><b></b>Password</a></li>
        <li style="width: 100%;height: 3px;margin: 10px 0 5px 0"></li>
        
        <li><a href="#" class="vnp-form-field-type" id="field-type-button"><b></b>Button</a></li>
        <!--
        <li><a href="#" class="vnp-form-field-type" id="field-type-datepicker"><b></b>Date</a></li>
        <li><a href="#" class="vnp-form-field-type" id="field-type-url"><b></b>URL</a></li>
        <li><a href="#" class="vnp-form-field-type" id="field-type-digits"><b></b>Number</a></li>
        <li><a href="#" class="vnp-form-field-type" id="field-type-phone"><b></b>Phone</a></li>
        <li><a href="#" class="vnp-form-field-type" id="field-type-address"><b></b>Address</a></li>
        <li><a href="#" class="vnp-form-field-type" id="field-type-email"><b></b>Email</a></li>
        <li><a href="#" class="vnp-form-field-type" id="field-type-currency"><b></b>Currency</a></li>
        -->
        <li><a href="#" class="vnp-form-field-type" id="field-type-time"><b></b>Time</a></li>
        <li><a href="#" class="vnp-form-field-type" id="field-type-instructions"><b></b>Instructions</a></li>
        <li><a href="#" class="vnp-form-field-type" id="field-type-username"><b></b>Username</a></li>
        <li><a href="#" class="vnp-form-field-type" id="field-type-autocomplete"><b></b>Autocomplete</a></li>
        <li><a href="#" class="vnp-form-field-type" id="field-type-min"><b></b>Min</a></li>
        <li><a href="#" class="vnp-form-field-type" id="field-type-range"><b></b>Range</a></li>
        <li><a href="#" class="vnp-form-field-type" id="field-type-name"><b></b>Name</a></li>
        
        <li><a href="#" class="vnp-form-field-type" id="field-type-color"><b></b>Color Picker</a></li>
        <li><a href="#" class="vnp-form-field-type" id="field-type-ip"><b></b>IP Address</a></li>
        <li><a href="#" class="vnp-form-field-type" id="field-type-max"><b></b>Max</a></li>
        <li><a href="#" class="vnp-form-field-type" id="field-type-pagebreak"><b></b>Page Break</a></li>
    </ul>
</div>

<div class="vnp-box w400 ctfield-ctner">
	<h1>{LABEL}</h1>
    <form method="post" id="form-field" action="{ACTION}">
    	<input type="hidden" name="save_ct_type" value="1" />
        <input type="hidden" name="ct_type_id" value="{CT_TYPE_ID}" />
        <div id="ct_field-container">
            <!-- BEGIN: ct_field -->
            {FIELD}
            <!-- END: ct_field -->
        </div>
        <input type="submit" class="vnp-input btn btn-primary" value="Save" />
  	</form>
</div>
<!-- END: main -->