jQuery(function($) {

setTimeout(function() {
	var setWidth = $('html').outerWidth() + (window.outerWidth - window.innerWidth);
	var setHeight = $('html').outerHeight() + (window.outerHeight - window.innerHeight);
	window.resizeTo(setWidth, setHeight);
	$('.x.popup').css({
		'width' : 'auto',
		'min-width' : 'auto',
		'max-width' : 'none'
	});
}, 600);

var skin = $('form').attr('class').split(' ')[1],
	editor_obj = opener.editorGetIFrame(opener.editorPrevSrl),
	name = 'input[name="template"]';

if ( skin !== 'ckeditor' && skin !== 'froalaeditor' && skin !== 'editorxe' )
{
	alert(msg_not_support);
	winClose();
}

if ( is_mobile )
{
	$('.template').on('click', function() {
		$('.template:not(this)').removeClass('hover');
		$('.template:not(this)').children('.desc').removeClass('hover');
		$(this).addClass('hover');
		$(this).children('.desc').addClass('hover');
		$(name).val($(this).attr('id'));
	});
	
	$('.template .desc').find('a.btn').on('click', function(e) {
		e.stopImmediatePropagation();
		if ( $(this).index() === 0 )
		{
			var val = $(name).val();
			if ( val != '' )
			{
				getTemplate(val, 'insert');
			}
			else
			{
				alert(msg_select);
			}
		}
		else
		{
			$(this).parents('.desc').removeClass('hover');
			$(this).parents('.template').removeClass('hover');
			$(name).val('');
		}
	});
}
else if (!is_mobile)
{
	$('.template')
	.on('mouseenter', function() {
		$(this).addClass('hover');
	})
	.on('mouseleave', function() {
		if ( $(name).val() !== $(this).attr('id') )
		{
			$(this).removeClass('hover');
		}
	})
	.on('click', function() {
		$('.template:not(this)').removeClass('hover');
		$(this).addClass('hover');
		$(name).val($(this).attr('id'));
	})
	.on('dblclick', function() {
		getTemplate($(name).val(), 'insert');
	});
}

$('.template_command a.btn').on('click', function() {
	var val = $(name).val();
	if ( $(this).index() === 0 )
	{
		if ( val != '' )
		{
			getTemplate(val, 'insert');
		}
		else
		{
			alert(msg_select);
		}
	}
	else if ( $(this).index() === 1 )
	{
		if ( val != '' )
		{
			getTemplate(val, 'overwtite');
		}
		else
		{
			alert(msg_select);
		}
	}
	else
	{
		winClose();
	}
	return false;
});

function getTemplate(template, type) {
	var params = {
        component : 'ap_template',
        method : 'getTemplate',
        template : template
    };
    exec_json('editor.procEditorCall', params, function(ret_obj) {
		var result = '<p>&nbsp;</p>' + ret_obj.template.replace(/<!-- Template.*?-->/gi, '') + '<p>&nbsp;</p>';

		opener.editorFocus(opener.editorPrevSrl);
		if ( type === 'overwtite' )
		{
			if ( skin == 'ckeditor' )
			{
				$(editor_obj).find('iframe').contents().find('.xe_content').html('');
			}
			else if ( skin == 'froalaeditor' )
			{
				window.opener.$(editor_obj).froalaEditor('html.set', '');
			}
		}
		opener.editorReplaceHTML(editor_obj, result);

		winClose();
	});
}

function winClose() {
	setTimeout(function() {
		window.close();
	}, 200);
}

});