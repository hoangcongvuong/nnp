// JavaScript Document
$(document).ready(function(e) {
    adminBarTemplate();
	
	$('.template-block-area-handler').hover(function(){
		$(this).addClass('active-area');
	},
	function(){
		$(this).removeClass('active-area');
	})
	
	$('.template-block-portal').hover(function(){
		$(this).children().addClass('active-block');
		
		var blockFunction = '<div class="template-edit-block">Edit</div>';
		$(this).prepend(blockFunction);
	},
	function(){
		$(this).children().removeClass('active-block');
		$('.template-edit-block', $(this)).remove();
	})
	
});

function adminBarTemplate()
{	
	$.get(base + '/admin.php?module=theme&op=loadAdminBar', function(data) {
		$(data).prependTo('body').fadeIn('slow');
	});
}

function enableDesignMode()
{
	$.ajax({
		type: 'POST',
		url: base + '/admin.php?module=theme&op=enableDesignMod',
		success: function(data) {
			window.location = window.location;
		}
	})
}
	