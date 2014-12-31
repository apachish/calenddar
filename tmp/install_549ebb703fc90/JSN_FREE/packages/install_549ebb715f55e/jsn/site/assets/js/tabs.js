jQuery(document).ready(function($){
	$('#member-registration,#member-profile').wrapInner('<div id="profile_content" class="tab-content" />').before('<ul id="profile_tabs" class="nav nav-tabs"></ul>');
	$('#profile_content > fieldset').wrap('<div class="tab-pane" />').children('legend').hide();
	$('#profile_content > .control-group .controls').addClass('jsn_registration_controls');
	$('#member-registration .form-actions a,#profile_content > .control-group a').hide();
	
	$('#profile_tabs > li:first,#profile_content > div:first').addClass('active');
	
	$('#member-registration [type="submit"],#member-profile [type="submit"]').click(function(){
		var first=false;
		var valid=false;
		first=false;
		valid=false;
		$('#member-registration [aria-required="true"],#member-profile [aria-required="true"]').each(function(){
			if(!first && $(this).is('input[type="radio"]')){
				$(this).parents('.controls').find('input[type="radio"]').each(function(){
					if($(this).is(':checked')) valid=true;
				});
				if(!valid){
					$('a[href="#'+$(this).parents('.tab-pane').attr('id')+'"]').click();
					first=true;
				}
			}
			else if(!first && $(this).val()=='' && ($(this).is('input') || $(this).is('select'))){
				$('a[href="#'+$(this).parents('.tab-pane').attr('id')+'"]').click();
				first=true;
			}
		});	
	});
	
	tabs($);
});
function tabs($){
	var id=0;
	$('#member-registration .tab-pane,#member-profile .tab-pane').attr('id','');
	$('#profile_tabs li').remove();
	$('.next-button').remove();
	$('.prev-button').remove();
	$('#profile_content .tab-pane > fieldset').not('.hide').each(function(){
		if($(this).parent().is('.active')) $('#profile_tabs').append('<li class="active"><a href="#tab'+id+'">'+$(this).children('legend').text()+'</a></li>');
		else $('#profile_tabs').append('<li><a href="#tab'+id+'">'+$(this).children('legend').text()+'</a></li>');
		$(this).parent().attr('id','tab'+id);
		id+=1;
	});

	if($('#member-registration').length && $("#profile_tabs li:last-child").is('.active')){
		$('#member-registration .form-actions button[type="submit"],#profile_content > .control-group button[type="submit"]').show().addClass('pull-right');
		if(id>1) $('#member-registration .form-actions button[type="submit"],#profile_content > .control-group button[type="submit"]').before(' <a class="btn btn-default prev-button pull-left" href="#">'+jsn_prev_button+'</a> ');
	}
	else if($('#member-registration').length && $("#profile_tabs li:first-child").is('.active')){
		$('#member-registration .form-actions button[type="submit"],#profile_content > .control-group button[type="submit"]').hide().addClass('pull-right');
		if(id>1) $('#member-registration .form-actions button[type="submit"],#profile_content > .control-group button[type="submit"]').before(' <a class="btn btn-default next-button pull-right" href="#">'+jsn_next_button+'</a> ');
	}
	else if($('#member-registration').length){
		$('#member-registration .form-actions button[type="submit"],#profile_content > .control-group button[type="submit"]').hide().addClass('pull-right')
			.before(' <a class="btn btn-default prev-button pull-left" href="#">'+jsn_prev_button+'</a> ')
			.before(' <a class="btn btn-default next-button pull-right" href="#">'+jsn_next_button+'</a> ');
	}

	$('#profile_tabs a').click(function(){
		$(this).tab('show');
		tabs($);
		return false;
	});
	$('.next-button').click(function(){
		$('#profile_tabs li.active').next().children('a').click();
		return false;
	});
	$('.prev-button').click(function(){
		$('#profile_tabs li.active').prev().children('a').click();
		return false;
	});

	return;
}