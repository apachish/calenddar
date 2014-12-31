jQuery(document).ready(function($){
	
	$('input.privacy').each(function(){
		$(this).parents('.control-group').addClass('privacy');
		$(this).parents('.controls').addClass('dropdown');
		var id=$(this).attr('id');
		if($(this).val()=='0') $('#btn_'+id+' > i').addClass('green icon icon-eye-open');
		if($(this).val()=='1') $('#btn_'+id+' > i').addClass('orange icon icon-user');
		if($(this).val()=='99') $('#btn_'+id+' > i').addClass('red icon icon-eye-close');
		
		/*$('#btn_'+id).click(function(event){
			event.preventDefault();
			$('#opt_'+id).toggle();//toggleClass('privacy_menu_show');
		})*/
		
		$('#opt_'+id+' a').click(function(event){
			event.preventDefault();
			$('#'+id).val($(this).attr('rel'));
			$('#btn_'+id+' > i').attr('class','');
			if($('#'+id).val()=='0') $('#btn_'+id+' > i').addClass('green icon icon-eye-open');
			if($('#'+id).val()=='1') $('#btn_'+id+' > i').addClass('orange icon icon-user');
			if($('#'+id).val()=='99') $('#btn_'+id+' > i').addClass('red icon icon-eye-close');
			$('#opt_'+id).hide();//removeClass('privacy_menu_show');
			$('#btn_'+id+' + ul').attr('style','');// JS conflict
			$('#btn_'+id).focus(); // Workaround for IE8
		});
	});
	
});