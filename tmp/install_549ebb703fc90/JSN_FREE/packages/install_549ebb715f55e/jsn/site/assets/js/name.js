jQuery(document).ready(function($){
	$('#jform_firstname,#jform_secondname,#jform_lastname').change(function(){
		var name='';
		if($('#jform_firstname').length && $('#jform_firstname').val()!='') name=name+$('#jform_firstname').val();
		if($('#jform_secondname').length && $('#jform_secondname').val()!='') name=name+' '+$('#jform_secondname').val();
		if($('#jform_lastname').length && $('#jform_lastname').val()!='') name=name+' '+$('#jform_lastname').val();
		$('#jform_name').val(name);
	});
	$('input[type="url"]').blur(function(){
		if($(this).val()!='' && !$(this).val().contains('http://') && !$(this).val().contains('https://')) $(this).val('http://'+$(this).val());
	})
});