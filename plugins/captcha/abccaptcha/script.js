ABCCaptchaGetImage = function (imageurl) {
	
	// set url
	var url = 'index.php?option=com_ajax&plugin=abccaptcha&format=raw';
	var data = '';
	
	var x = new Request({
		url: url, 
		method: 'post',
		data: data,
		onRequest: function(){
			$('abccaptcha_message').setAttribute('style', 'display:none;');
			$('abccaptcha_newcode').setAttribute('class', 'ajaxrefresh');
		},			
		onSuccess: function(response){
			$('abccaptcha_newcode').setAttribute('class', 'refresh');
			if (response == '') {
				$('abccaptcha_message').setAttribute('style', 'display:block;');
				return false;
			}
			//console.log(response);
			response = JSON.decode(response);
			if (response.error == 0) {
				$('abccaptcha_image').set('src', imageurl + response.filename);
			} 
		},
		onFailure: function(){			
		}			
	}).send();	
}
	
