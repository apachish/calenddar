var imgSelect = {
	
	php: '',  //Ajax url
	alert: '.imgSelect-alert', //Response selector
	setImg: '.img',            //Image src to be replaced 
	setInput: '.input', 
	crop: {
		width:  200,  // Default selection width (in pixels)
		height: 200,  // Default selection height (in pixels)
		aspectRatio: '1:1', // A string of the form "width:height" which represents the aspect ratio to maintain (http://andrew.hedges.name/experiments/aspect_ratio/)
		//maxHeight:,	 // Maximum selection height (in pixels)
		//maxWidth:,	 // Maximum selection width (in pixels)
		//minHeight:,	 // Minimum selection height (in pixels)
		//minWidth:,	 // Minimum selection width (in pixels)
	},
	webcamWidth:  350,   //Webcam width
	webcamHeight: 262,   //Webcam height
	webcamFlv: '',
	
	//Upload image
	upload: function() {
		var alert = jQuery(imgSelect.alert);
		
		if (!jQuery('#ajax-uploadimage').length)
		new AjaxUpload( jQuery('.ajaxupload') , {
			action: imgSelect.php,
			data: {'action': 'upload'},
			name: 'ajax-uploadimage',
			responseType: 'json',
			onSubmit: function(file, ext) {
				imgSelect.removeSelection();
				alert.removeClass('alert-success alert-danger').text('Uploading image...').show();
			},
			onComplete: function(img, data) {
				if (data.success) {
					imgSelect.cancel();
					jQuery('.imgSelect-container, .imgSelect-upload, .imgSelect-actions').show();
					jQuery('.imgSelect-upload').append( jQuery('<img/>', {src: data.data}) );
					imgSelect.setSelection( jQuery('.imgSelect-upload img') );
					alert.hide();
				} 
				else 
					alert.removeClass('alert-success').addClass('alert-danger').text(data.data);
			},
			onChange: function() {
				imgSelect.cancel();
				jQuery('.imgSelect-container, .imgSelect-upload').show();
			}
		});
	},

	//Webcam snapshot
	webcam: function() {
		imgSelect.cancel();
		jQuery('.imgSelect-container, .imgSelect-webcam, .imgSelect-actions').show();
		jQuery('.imgSelect-actions .save').hide();
		webcam.set_api_url(imgSelect.php);
		webcam.set_swf_url(imgSelect.webcamFlv);
		webcam.set_shutter_sound(false);
		webcam.set_quality(90);
		jQuery('.imgSelect-webcam .preview').html( webcam.get_html(imgSelect.webcamWidth, imgSelect.webcamHeight) );
		jQuery('.imgSelect-actions .capture').show().css('marginLeft', ((imgSelect.webcamWidth-105)/2)+'px');

		webcam.set_hook( 'onComplete', function(data) {
			if (data != 0) {
				jQuery('.imgSelect-actions .capture').hide();
				jQuery('.imgSelect-webcam .preview').html('');
				jQuery('.imgSelect-webcam .crop').append( jQuery('<img/>', {src: data}) );
				jQuery('.imgSelect-container .new-snap, .imgSelect-actions .save').show();
				imgSelect.setSelection( jQuery('.imgSelect-webcam img') );
			}
			else {
				imgSelect.webcam();
				alert.text('Webcam Error. Try again.');
			}
		});
	},

	webcamSnap: function() {
		webcam.snap();
	},

	//Save image after crop
	save: function() {
		var data,
			img = jQuery('.imgSelect-container img'),
			crop = imgSelect.crop,
			alert = jQuery(imgSelect.alert);

		if (img.length) {
			var x1 = jQuery('#x1').val(),
				y1 = jQuery('#y1').val(),
				w  = jQuery('#w').val(),
				h  = jQuery('#h').val();

			if (w == "" || w == 0)
				w  = crop.width;
			if (h == "" || h == 0)
				h  = crop.height;
			
			data = {action:'save', 'x1': x1, 'y1': y1, 'w': w, 'h': h};
			
			jQuery.ajax({
				url: imgSelect.php,
				type: 'POST',
				dataType: 'json',
				data: data,
				beforeSend: function() {
					alert.removeClass('alert-success alert-danger').text('Saving image...').show();
					jQuery('.imgSelect-actions .save').prop('disabled', true);
				},
				success: function(data) {
					if (data.success) {
						jQuery(imgSelect.setImg).attr('src', data.data).css('display','block');
						jQuery(imgSelect.setInput).val(data.data);
						imgSelect.cancel();
						alert.addClass('alert-success').text('Successfully saved.');
						setTimeout(function(){ alert.fadeOut(); }, 2000);
					}
					else 
						alert.addClass('alert-danger').text(data.data);
				},
				complete: function() { jQuery('.imgSelect-actions .save').prop('disabled', false); },
				error: function() { alert.addClass('alert-danger').text('Ajax Error. Try again.'); }
			});
		}
	},
	setSelection: function(img) {
		img.load(function(){
			var w, h, x1, y1, x2, y2, settings, crop = imgSelect.crop;

			w = img.width();
			h = img.height();
			x1 = Math.round(w/2) - crop.width/2;
			x2 = x1 + crop.width;
			y1 = Math.round(h/2) - crop.height/2;
			y2 = y1 + crop.height;

			settings = {
				aspectRatio: crop.aspectRatio, 
				onSelectChange: imgSelect.updateSelection,
				x1: x1, y1: y1, x2: x2, y2: y2
			};

			if (crop.maxWidth)
				settings.maxWidth = crop.maxWidth;
			if (crop.maxHeight)
				settings.maxHeight = crop.maxHeight;
			if (crop.minWidth)
				settings.minWidth = crop.minWidth;
			if (crop.minHeight)
				settings.minHeight = crop.minHeight;

			img.imgAreaSelect(settings);
			
			jQuery('#x1').val(x1);
			jQuery('#y1').val(y1);
		});
	},
	updateSelection: function(img, selection) {
		jQuery('#x1').val(selection.x1);
		jQuery('#y1').val(selection.y1);
		jQuery('#w').val(selection.width);
		jQuery('#h').val(selection.height);
	},
	cancel: function() {
		imgSelect.removeSelection();
		jQuery('.imgSelect-container, .imgSelect-webcam, .imgSelect-upload, .imgSelect-actions').hide();
		jQuery('.imgSelect-upload, .imgSelect-webcam .crop').html('');
		jQuery('.imgSelect-container .new-snap,.imgSelect-container .capture').hide();
		jQuery('.imgSelect-actions .save').show();
	},
	removeSelection: function () {
		if ( jQuery('[class^="imgareaselect-"]').length )
			jQuery('[class^="imgareaselect-"]').remove();
	}
};
/*$(function() {
	if (!$('#ajax-uploadimage').length)
		imgSelect.upload();	
});*/


/* imgAreaSelect by Michal Wojciechowski (odyniec.net) http://odyniec.net/projects/imgareaselect/ */
jQuery.imgAreaSelect={onKeyPress:null};jQuery.imgAreaSelect.init=function(img,options){var $area=jQuery('<div></div>'),$border1=jQuery('<div></div>'),$border2=jQuery('<div></div>'),$outLeft=jQuery('<div></div>'),$outTop=jQuery('<div></div>'),$outRight=jQuery('<div></div>'),$outBottom=jQuery('<div></div>'),left,top,imgOfs,imgWidth,imgHeight,parent,parOfs,parScroll,adjusted,zIndex=0,fixed,$p,startX,startY,moveX,moveY,resizeMargin=10,resize=[],V=0,H=1,keyDown,d,aspectRatio,x1,x2,y1,y2,x,y,selection={x1:0,y1:0,x2:0,y2:0,width:0,height:0};var $a=$area.add($border1).add($border2);var $o=$outLeft.add($outTop).add($outRight).add($outBottom);function viewX(x){return x+imgOfs.left+parScroll.left-parOfs.left}function viewY(y){return y+imgOfs.top+parScroll.top-parOfs.top}function selX(x){return x-imgOfs.left-parScroll.left+parOfs.left}function selY(y){return y-imgOfs.top-parScroll.top+parOfs.top}function evX(event){return event.pageX+parScroll.left-parOfs.left}function evY(event){return event.pageY+parScroll.top-parOfs.top}function adjust(){imgOfs=jQuery(img).offset();imgWidth=jQuery(img).width();imgHeight=jQuery(img).height();if(jQuery(parent).is('body'))parOfs=parScroll={left:0,top:0};else{parOfs=jQuery(parent).offset();parScroll={left:parent.scrollLeft,top:parent.scrollTop}}left=viewX(0);top=viewY(0)}function update(resetKeyPress){$a.css({left:viewX(selection.x1)+'px',top:viewY(selection.y1)+'px',width:Math.max(selection.width-options.borderWidth*2,0)+'px',height:Math.max(selection.height-options.borderWidth*2,0)+'px'});$outLeft.css({left:left+'px',top:top+'px',width:selection.x1+'px',height:imgHeight+'px'});$outTop.css({left:left+selection.x1+'px',top:top+'px',width:selection.width+'px',height:selection.y1+'px'});$outRight.css({left:left+selection.x2+'px',top:top+'px',width:imgWidth-selection.x2+'px',height:imgHeight+'px'});$outBottom.css({left:left+selection.x1+'px',top:top+selection.y2+'px',width:selection.width+'px',height:imgHeight-selection.y2+'px'});if(resetKeyPress!==false){if(jQuery.imgAreaSelect.keyPress!=docKeyPress)jQuery(document).unbind(jQuery.imgAreaSelect.keyPress,jQuery.imgAreaSelect.onKeyPress);if(options.keys)jQuery(document).bind(jQuery.imgAreaSelect.keyPress,jQuery.imgAreaSelect.onKeyPress=docKeyPress)}}function areaMouseMove(event){if(!adjusted){adjust();adjusted=true;$a.one('mouseout',function(){adjusted=false})}x=selX(evX(event))-selection.x1;y=selY(evY(event))-selection.y1;resize=[];if(options.resizable){if(y<=resizeMargin)resize[V]='n';else if(y>=selection.height-resizeMargin)resize[V]='s';if(x<=resizeMargin)resize[H]='w';else if(x>=selection.width-resizeMargin)resize[H]='e'}$border2.css('cursor',resize.length?resize.join('')+'-resize':options.movable?'move':'')}function areaMouseDown(event){if(event.which!=1)return false;adjust();if(options.resizable&&resize.length>0){jQuery('body').css('cursor',resize.join('')+'-resize');x1=viewX(resize[H]=='w'?selection.x2:selection.x1);y1=viewY(resize[V]=='n'?selection.y2:selection.y1);jQuery(document).mousemove(selectingMouseMove);$border2.unbind('mousemove',areaMouseMove);jQuery(document).one('mouseup',function(){resize=[];jQuery('body').css('cursor','');if(options.autoHide)$a.add($o).hide();options.onSelectEnd(img,selection);jQuery(document).unbind('mousemove',selectingMouseMove);$border2.mousemove(areaMouseMove)})}else if(options.movable){moveX=selection.x1+left;moveY=selection.y1+top;startX=evX(event);startY=evY(event);jQuery(document).mousemove(movingMouseMove).one('mouseup',function(){options.onSelectEnd(img,selection);jQuery(document).unbind('mousemove',movingMouseMove)})}else jQuery(img).mousedown(event);return false}function aspectRatioXY(){x2=Math.max(left,Math.min(left+imgWidth,x1+Math.abs(y2-y1)*aspectRatio*(x2>=x1?1:-1)));y2=Math.round(Math.max(top,Math.min(top+imgHeight,y1+Math.abs(x2-x1)/aspectRatio*(y2>=y1?1:-1))));x2=Math.round(x2)}function aspectRatioYX(){y2=Math.max(top,Math.min(top+imgHeight,y1+Math.abs(x2-x1)/aspectRatio*(y2>=y1?1:-1)));x2=Math.round(Math.max(left,Math.min(left+imgWidth,x1+Math.abs(y2-y1)*aspectRatio*(x2>=x1?1:-1))));y2=Math.round(y2)}function doResize(newX2,newY2){x2=newX2;y2=newY2;if(options.minWidth&&Math.abs(x2-x1)<options.minWidth){x2=x1-options.minWidth*(x2<x1?1:-1);if(x2<left)x1=left+options.minWidth;else if(x2>left+imgWidth)x1=left+imgWidth-options.minWidth}if(options.minHeight&&Math.abs(y2-y1)<options.minHeight){y2=y1-options.minHeight*(y2<y1?1:-1);if(y2<top)y1=top+options.minHeight;else if(y2>top+imgHeight)y1=top+imgHeight-options.minHeight}x2=Math.max(left,Math.min(x2,left+imgWidth));y2=Math.max(top,Math.min(y2,top+imgHeight));if(aspectRatio)if(Math.abs(x2-x1)/aspectRatio>Math.abs(y2-y1))aspectRatioYX();else aspectRatioXY();if(options.maxWidth&&Math.abs(x2-x1)>options.maxWidth){x2=x1-options.maxWidth*(x2<x1?1:-1);if(aspectRatio)aspectRatioYX()}if(options.maxHeight&&Math.abs(y2-y1)>options.maxHeight){y2=y1-options.maxHeight*(y2<y1?1:-1);if(aspectRatio)aspectRatioXY()}selection.x1=selX(Math.min(x1,x2));selection.x2=selX(Math.max(x1,x2));selection.y1=selY(Math.min(y1,y2));selection.y2=selY(Math.max(y1,y2));selection.width=Math.abs(x2-x1);selection.height=Math.abs(y2-y1);update();options.onSelectChange(img,selection)}function selectingMouseMove(event){x2=!resize.length||resize[H]||aspectRatio?evX(event):viewX(selection.x2);y2=!resize.length||resize[V]||aspectRatio?evY(event):viewY(selection.y2);doResize(x2,y2);return false}function doMove(newX1,newY1){x2=(x1=newX1)+selection.width;y2=(y1=newY1)+selection.height;selection.x1=selX(x1);selection.y1=selY(y1);selection.x2=selX(x2);selection.y2=selY(y2);update();options.onSelectChange(img,selection)}function movingMouseMove(event){var newX1=Math.max(left,Math.min(moveX+evX(event)-startX,left+imgWidth-selection.width));var newY1=Math.max(top,Math.min(moveY+evY(event)-startY,top+imgHeight-selection.height));doMove(newX1,newY1);event.preventDefault();return false}function startSelection(event){adjust();selection.x1=selection.x2=selX(startX=x1=x2=evX(event));selection.y1=selection.y2=selY(startY=y1=y2=evY(event));selection.width=0;selection.height=0;resize=[];update();$a.add($o).show();jQuery(document).unbind('mouseup',cancelSelection).mousemove(selectingMouseMove);$border2.unbind('mousemove',areaMouseMove);options.onSelectStart(img,selection);jQuery(document).one('mouseup',function(){if(options.autoHide||(selection.width*selection.height==0))$a.add($o).hide();options.onSelectEnd(img,selection);jQuery(document).unbind('mousemove',selectingMouseMove);$border2.mousemove(areaMouseMove)})}function cancelSelection(){jQuery(document).unbind('mousemove',startSelection);$a.add($o).hide()}function imgMouseDown(event){if(event.which!=1)return false;jQuery(document).one('mousemove',startSelection).one('mouseup',cancelSelection);return false}function windowResize(){adjust();update(false);x1=viewX(selection.x1);y1=viewY(selection.y1);x2=viewX(selection.x2);y2=viewY(selection.y2)}var docKeyPress=function(event){var k=options.keys,d=10,t,key=event.keyCode||event.which;if(!isNaN(k.arrows))d=k.arrows;if(!isNaN(k.shift)&&event.shiftKey)d=k.shift;if(!isNaN(k.ctrl)&&event.ctrlKey)d=k.ctrl;if(!isNaN(k.alt)&&(event.altKey||event.originalEvent.altKey))d=k.alt;if(k.arrows=='resize'||(k.shift=='resize'&&event.shiftKey)||(k.ctrl=='resize'&&event.ctrlKey)||(k.alt=='resize'&&(event.altKey||event.originalEvent.altKey))){switch(key){case 37:d=-d;case 39:t=Math.max(x1,x2);x1=Math.min(x1,x2);x2=Math.max(t+d,x1);if(aspectRatio)aspectRatioYX();break;case 38:d=-d;case 40:t=Math.max(y1,y2);y1=Math.min(y1,y2);y2=Math.max(t+d,y1);if(aspectRatio)aspectRatioXY();break;default:return}doResize(x2,y2)}else{x1=Math.min(x1,x2);y1=Math.min(y1,y2);switch(key){case 37:doMove(Math.max(x1-d,left),y1);break;case 38:doMove(x1,Math.max(y1-d,top));break;case 39:doMove(x1+Math.min(d,imgWidth-selX(x2)),y1);break;case 40:doMove(x1,y1+Math.min(d,imgHeight-selY(y2)));break;default:return}}return false};this.setOptions=function(newOptions){options=jQuery.extend(options,newOptions);if(newOptions.x1!=null){selection.x1=newOptions.x1;selection.y1=newOptions.y1;selection.x2=newOptions.x2;selection.y2=newOptions.y2;newOptions.show=true}if(newOptions.keys)options.keys=jQuery.extend({shift:1,ctrl:'resize'},newOptions.keys===true?{}:newOptions.keys);parent=jQuery(options.parent).get(0);adjust();$p=jQuery(img);while($p.length&&!$p.is('body')){if(!isNaN($p.css('z-index'))&&$p.css('z-index')>zIndex)zIndex=$p.css('z-index');if($p.css('position')=='fixed')fixed=true;$p=$p.parent()}x1=viewX(selection.x1);y1=viewY(selection.y1);x2=viewX(selection.x2);y2=viewY(selection.y2);selection.width=x2-x1;selection.height=y2-y1;update();if(newOptions.hide)$a.add($o).hide();else if(newOptions.show)$a.add($o).show();$o.addClass(options.classPrefix+'-outer');$area.addClass(options.classPrefix+'-selection');$border1.addClass(options.classPrefix+'-border1');$border2.addClass(options.classPrefix+'-border2');$a.css({borderWidth:options.borderWidth+'px'});$area.css({backgroundColor:options.selectionColor,opacity:options.selectionOpacity});$border1.css({borderStyle:'solid',borderColor:options.borderColor1});$border2.css({borderStyle:'dashed',borderColor:options.borderColor2});$o.css({opacity:options.outerOpacity,backgroundColor:options.outerColor});aspectRatio=options.aspectRatio&&(d=options.aspectRatio.split(/:/))?d[0]/d[1]:null;if(options.disable||options.enable===false){$a.unbind('mousemove',areaMouseMove).unbind('mousedown',areaMouseDown);jQuery(img).add($o).unbind('mousedown',imgMouseDown);jQuery(window).unbind('resize',windowResize)}else if(options.enable||options.disable===false){if(options.resizable||options.movable)$a.mousemove(areaMouseMove).mousedown(areaMouseDown);if(!options.persistent)jQuery(img).add($o).mousedown(imgMouseDown);jQuery(window).resize(windowResize)}jQuery(options.parent).append($o.add($a));options.enable=options.disable=undefined};var ua=navigator.userAgent,msie=(/msie ([\w.]+)/i.exec(ua)||[])[1],safari=/webkit/i.test(ua)&&!/chrome/i.test(ua);if(msie)jQuery(img).attr('unselectable','on');jQuery.imgAreaSelect.keyPress=msie||safari?'keydown':'keypress';$a.add($o).css({display:'none',position:fixed?'fixed':'absolute',overflow:'hidden',zIndex:zIndex>0?zIndex:'0'});$area.css({borderStyle:'solid'});initOptions={borderColor1:'#000',borderColor2:'#fff',borderWidth:1,classPrefix:'imgareaselect',movable:true,resizable:true,selectionColor:'#fff',selectionOpacity:0.2,outerColor:'#000',outerOpacity:0.2,parent:'body',onSelectStart:function(){},onSelectChange:function(){},onSelectEnd:function(){}};options=jQuery.extend(initOptions,options);this.setOptions(options)};jQuery.fn.imgAreaSelect=function(options){options=options||{};this.each(function(){if(jQuery(this).data('imgAreaSelect'))jQuery(this).data('imgAreaSelect').setOptions(options);else{if(options.enable===undefined&&options.disable===undefined)options.enable=true;jQuery(this).data('imgAreaSelect',new jQuery.imgAreaSelect.init(this,options))}});return this};
/* AjaxUpload by Andris Valums, http://valums.com */
(function(){var d=document,w=window;function get(element){if(typeof element=="string")element=d.getElementById(element);return element}function addEvent(el,type,fn){if(w.addEventListener)el.addEventListener(type,fn,false);else if(w.attachEvent){var f=function(){fn.call(el,w.event)};el.attachEvent("on"+type,f)}}var toElement=function(){var div=d.createElement("div");return function(html){div.innerHTML=html;var el=div.childNodes[0];div.removeChild(el);return el}}();function hasClass(ele,cls){return ele.className.match(new RegExp("(\\s|^)"+cls+"(\\s|$)"))}function addClass(ele,cls){if(!hasClass(ele,cls))ele.className+=" "+cls}function removeClass(ele,cls){var reg=new RegExp("(\\s|^)"+cls+"(\\s|$)");ele.className=ele.className.replace(reg," ")}if(document.documentElement["getBoundingClientRect"])var getOffset=function(el){var box=el.getBoundingClientRect(),doc=el.ownerDocument,body=doc.body,docElem=doc.documentElement,clientTop=docElem.clientTop||body.clientTop||0,clientLeft=docElem.clientLeft||body.clientLeft||0,zoom=1;if(body.getBoundingClientRect){var bound=body.getBoundingClientRect();zoom=(bound.right-bound.left)/body.clientWidth}if(zoom>1){clientTop=0;clientLeft=0}var top=box.top/zoom+(window.pageYOffset||docElem&&docElem.scrollTop/zoom||body.scrollTop/zoom)-clientTop,left=box.left/zoom+(window.pageXOffset||docElem&&docElem.scrollLeft/zoom||body.scrollLeft/zoom)-clientLeft;return{top:top,left:left}};else var getOffset=function(el){if(w.jQuery)return jQuery(el).offset();var top=0,left=0;do{top+=el.offsetTop||0;left+=el.offsetLeft||0}while(el=el.offsetParent);return{left:left,top:top}};function getBox(el){var left,right,top,bottom;var offset=getOffset(el);left=offset.left;top=offset.top;right=left+el.offsetWidth;bottom=top+el.offsetHeight;return{left:left,right:right,top:top,bottom:bottom}}function getMouseCoords(e){if(!e.pageX&&e.clientX){var zoom=1;var body=document.body;if(body.getBoundingClientRect){var bound=body.getBoundingClientRect();zoom=(bound.right-bound.left)/body.clientWidth}return{x:e.clientX/zoom+d.body.scrollLeft+d.documentElement.scrollLeft,y:e.clientY/zoom+d.body.scrollTop+d.documentElement.scrollTop}}return{x:e.pageX,y:e.pageY}}var getUID=function(){var id=0;return function(){return"ValumsAjaxUpload"+id++}}();function fileFromPath(file){return file.replace(/.*(\/|\\)/,"")}function getExt(file){return/[.]/.exec(file)?/[^.]+$/.exec(file.toLowerCase()):""}Ajax_upload=AjaxUpload=function(button,options){if(button.jquery)button=button[0];else if(typeof button=="string"&&/^#.*/.test(button))button=button.slice(1);button=get(button);this._input=null;this._button=button;this._disabled=false;this._submitting=false;this._justClicked=false;this._parentDialog=d.body;if(window.jQuery&&jQuery.ui&&jQuery.ui.dialog){var parentDialog=jQuery(this._button).parents(".ui-dialog");if(parentDialog.length)this._parentDialog=parentDialog[0]}this._settings={action:"upload.php",name:"userfile",data:{},autoSubmit:true,responseType:false,onChange:function(file,extension){},onSubmit:function(file,extension){},onComplete:function(file,response){}};for(var i in options)this._settings[i]=options[i];this._createInput();this._rerouteClicks()};AjaxUpload.prototype={setData:function(data){this._settings.data=data},disable:function(){this._disabled=true},enable:function(){this._disabled=false},destroy:function(){if(this._input){if(this._input.parentNode)this._input.parentNode.removeChild(this._input);this._input=null}},_createInput:function(){var self=this;var input=d.createElement("input");input.setAttribute("type","file");input.setAttribute("name",this._settings.name);input.setAttribute("id",this._settings.name);var styles={"position":"absolute","margin":"-5px 0 0 -175px","padding":0,"width":"220px","height":"30px","fontSize":"14px","opacity":0,"cursor":"pointer","display":"none","zIndex":2147483583};for(var i in styles)input.style[i]=styles[i];if(!(input.style.opacity==="0"))input.style.filter="alpha(opacity=0)";this._parentDialog.appendChild(input);addEvent(input,"change",function(){var file=fileFromPath(this.value);if(self._settings.onChange.call(self,file,getExt(file))==false)return;if(self._settings.autoSubmit)self.submit()});addEvent(input,"click",function(){self.justClicked=true;setTimeout(function(){self.justClicked=false},3E3)});this._input=input},_rerouteClicks:function(){var self=this;var box,dialogOffset={top:0,left:0},over=false;addEvent(self._button,"mouseover",function(e){if(!self._input||over)return;over=true;box=getBox(self._button);if(self._parentDialog!=d.body)dialogOffset=getOffset(self._parentDialog)});addEvent(document,"mousemove",function(e){var input=self._input;if(!input||!over)return;if(self._disabled){removeClass(self._button,"hover");input.style.display="none";return}var c=getMouseCoords(e);if(c.x>=box.left&&c.x<=box.right&&c.y>=box.top&&c.y<=box.bottom){input.style.top=c.y-dialogOffset.top+"px";input.style.left=c.x-dialogOffset.left+"px";input.style.display="block";addClass(self._button,"hover")}else{over=false;if(!self.justClicked)input.style.display="none";removeClass(self._button,"hover")}})},_createIframe:function(){var id=getUID();var iframe=toElement('<iframe src="javascript:false;" name="'+id+'" />');iframe.id=id;iframe.style.display="none";d.body.appendChild(iframe);return iframe},submit:function(){var self=this,settings=this._settings;if(this._input.value==="")return;var file=fileFromPath(this._input.value);if(!(settings.onSubmit.call(this,file,getExt(file))==false)){var iframe=this._createIframe();var form=this._createForm(iframe);form.appendChild(this._input);form.submit();d.body.removeChild(form);form=null;this._input=null;this._createInput();var toDeleteFlag=false;addEvent(iframe,"load",function(e){if(iframe.src=="javascript:'%3Chtml%3E%3C/html%3E';"||iframe.src=="javascript:'<html></html>';"){if(toDeleteFlag)setTimeout(function(){d.body.removeChild(iframe)},0);return}var doc=iframe.contentDocument?iframe.contentDocument:frames[iframe.id].document;if(doc.readyState&&doc.readyState!="complete")return;if(doc.body&&doc.body.innerHTML=="false")return;var response;if(doc.XMLDocument)response=doc.XMLDocument;else if(doc.body){response=doc.body.innerHTML;if(settings.responseType&&settings.responseType.toLowerCase()=="json"){if(doc.body.firstChild&&doc.body.firstChild.nodeName.toUpperCase()=="PRE")response=doc.body.firstChild.firstChild.nodeValue;if(response)response=window["eval"]("("+response+")");else response={}}}else var response=doc;settings.onComplete.call(self,file,response);toDeleteFlag=true;iframe.src="javascript:'<html></html>';"})}else{d.body.removeChild(this._input);this._input=null;this._createInput()}},_createForm:function(iframe){var settings=this._settings;var form=toElement('<form method="post" enctype="multipart/form-data"></form>'); form.style.display="none";form.action=settings.action;form.target=iframe.name;d.body.appendChild(form);for(var prop in settings.data){var el=d.createElement("input");el.type="hidden";el.name=prop;el.value=settings.data[prop];form.appendChild(el)}return form}}})();
/* JPEGCam by Joseph Huckaby <jhuckaby@goldcartridge.com> */
window.webcam={version:"1.0.9",ie:!!navigator.userAgent.match(/MSIE/),protocol:location.protocol.match(/https/i)?"https":"http",callback:null,swf_url:"webcam.swf",shutter_url:"shutter.mp3",api_url:"",loaded:false,quality:90,shutter_sound:true,stealth:false,hooks:{onLoad:null,onComplete:null,onError:null},set_hook:function(e,t){if(typeof this.hooks[e]=="undefined")return alert("Hook type not supported: "+e);this.hooks[e]=t},fire_hook:function(e,t){if(this.hooks[e]){if(typeof this.hooks[e]=="function"){this.hooks[e](t)}else if(typeof this.hooks[e]=="array"){this.hooks[e][0][this.hooks[e][1]](t)}else if(window[this.hooks[e]]){window[this.hooks[e]](t)}return true}return false},set_api_url:function(e){this.api_url=e},set_swf_url:function(e){this.swf_url=e},get_html:function(e,t,n,r){if(!n)n=e;if(!r)r=t;var i="";var s="shutter_enabled="+(this.shutter_sound?1:0)+"&shutter_url="+escape(this.shutter_url)+"&width="+e+"&height="+t+"&server_width="+n+"&server_height="+r;if(this.ie){i+='<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="'+this.protocol+'://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" width="'+e+'" height="'+t+'" id="webcam_movie" align="middle"><param name="allowScriptAccess" value="always" /><param name="allowFullScreen" value="false" /><param name="movie" value="'+this.swf_url+'" /><param name="loop" value="false" /><param name="menu" value="false" /><param name="quality" value="best" /><param name="bgcolor" value="#ffffff" /><param name="flashvars" value="'+s+'"/></object>'}else{i+='<embed id="webcam_movie" src="'+this.swf_url+'" loop="false" menu="false" quality="best" bgcolor="#ffffff" width="'+e+'" height="'+t+'" name="webcam_movie" align="middle" allowScriptAccess="always" allowFullScreen="false" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" flashvars="'+s+'" />'}this.loaded=false;return i},get_movie:function(){if(!this.loaded)return alert("ERROR: Movie is not loaded yet");var e=document.getElementById("webcam_movie");if(!e)alert("ERROR: Cannot locate movie 'webcam_movie' in DOM");return e},set_stealth:function(e){this.stealth=e},snap:function(e,t,n){if(t)this.set_hook("onComplete",t);if(e)this.set_api_url(e);if(typeof n!="undefined")this.set_stealth(n);this.get_movie()._snap(this.api_url,this.quality,this.shutter_sound?1:0,this.stealth?1:0)},freeze:function(){this.get_movie()._snap("",this.quality,this.shutter_sound?1:0,0)},upload:function(e,t){if(t)this.set_hook("onComplete",t);if(e)this.set_api_url(e);this.get_movie()._upload(this.api_url)},reset:function(){this.get_movie()._reset()},configure:function(e){if(!e)e="camera";this.get_movie()._configure(e)},set_quality:function(e){this.quality=e},set_shutter_sound:function(e,t){this.shutter_sound=e;this.shutter_url=t?t:"shutter.mp3"},flash_notify:function(e,t){switch(e){case"flashLoadComplete":this.loaded=true;this.fire_hook("onLoad");break;case"error":if(!this.fire_hook("onError",t)){alert("JPEGCam Flash Error: "+t)}break;case"success":this.fire_hook("onComplete",t.toString());break;default:alert("jpegcam flash_notify: "+e+": "+t);break}}}