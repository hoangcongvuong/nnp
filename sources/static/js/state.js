var init = true,
    state = window.history.pushState !== undefined;

// Handles response
var ajax_state_handler = function(respondedObj){
	$('#analytic-code').remove();
    $('title').html(respondedObj.title);
	//$('#hook-header').html(respondedObj.hook.header);
	$('#' + ajaxMarker.main_content).fadeOut(0);
   	$('#' + ajaxMarker.main_content).html(respondedObj.data).fadeIn(000);
	$('html,body').animate({scrollTop: $('body').offset().top},'fast');
	//document.getElementById('hook-header').innerHTML = respondedObj.hook.header;
	//$('#hook-header').append(respondedObj.hook.header);
	hideLoading();
}

var ajax_error_handler = function(error_str)
{
	$('#' + ajaxMarker.main_content).fadeOut(0);
   	$('#' + ajaxMarker.main_content).html(error_str).fadeIn(300);
	hideLoading();
}

var showLoading = function(){
	loadingArea = 'body';
	var loader = '<div id="vnp-loading" class="vnp-loading"></div>';
	$(loadingArea).append(loader);
}

var hideLoading = function(){
	$('.vnp-loading').each(function(index, element) {
        $(this).fadeOut(600, 'swing', function(){
			$(this).remove();
		});
    })
}

$.address.state(base).init(function () {
    // Initializes the plugin
   $('a:not(.noajax)[href!="#"]').address();

}).change(function (event) {
    // Selects the proper navigation link
    $('a:not([noajax])').each(function () {
        if( $(this).attr('href') == ($.address.state() + $.address.path()) )
		{
            $(this).parent().addClass('active-menu-item');
        }
		else
		{
            $(this).parent().removeClass('active-menu-item');
        }
    });

    if (state && init) {

        init = false;

    } else {

        // Loads the page content and inserts it into the content area
		var ajaxPath = $.address.state() + $.address.path();
        $.ajax({
            url: ajaxPath,
			type: 'POST',
			data: {ajax: 'state-main'},
			dataType: 'json',
			beforeSend: showLoading,
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                ajax_error_handler(XMLHttpRequest.responseText);
            },
            success: function (data, textStatus, XMLHttpRequest) {
                ajax_state_handler(data);
            }
        });
    }

});

if (!state) {

    // Hides the page during initialization
    //document.write('<style type="text/css"> .page { display: none; } </style>');
}