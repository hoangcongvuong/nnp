var init = true,
    state = window.history.pushState !== undefined;

$.address.state(base).init(function () {
    // Initializes the plugin
	$('a:not(.noajax)[href!="#"]').address();
	//$('a[href!="#"]')
    //$('a[href!="#"]').address();
}).change(function (event) {
    // Selects the proper navigation link
    $('a:not([noajax])').each(function () {
        if( $(this).attr('href') == ($.address.state() + $.address.path() + '?' + $.address.queryString()) )
		{
            $(this).parent().addClass('active');
        }
		else
		{
            $(this).parent().removeClass('active');
        }
    });

    if (state && init) {

        init = false;

    } else {

        // Loads the page content and inserts it into the content area
		var ajaxPath = $.address.state() + $.address.path() + '?&ajax=state-main&' + $.address.queryString();
        $.ajax({
            url: ajaxPath,
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