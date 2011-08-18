////////////////////////Exclude Sellers Section JS//////////////////////////////////
function SelectSeller(seller_id)
{
	var div_class = $('#div_'+seller_id).attr('class');
	var url = base_url+'rules/sellers/';
	var new_class = '';
	var data = '';
	if(div_class == 'selected')
	{
		url += "exclude/"+seller_id;
		new_class = '';
	}else
	{
		url += "include/"+seller_id;
		new_class = 'selected';
	}
	$.ajax({
		type:'POST',
		data: data,
		url: url,
		cache: false,
		success: function(html)
		{
			$('#div_'+seller_id).removeClass('selected').addClass(new_class);
		}
	});
	return false;
}
////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////Product Management///////////////////////////////////
function changeHighlight()
{
	var url = base_url+'prod_management/showFlooredItems';
	var data = '';
	$.ajax({
		type:'POST',
		data: data,
		url: url,
		cache: false,
		success: function(html)
		{
			if(html != 0)
			{
				var splitted = html.split(',');
				for(var i=0;i<splitted.length;i++)
				{
					if(splitted[i]!='')
					{
						highlight(splitted[i]);
					}
				}
			}
		}
	});
}
function highlight(id) {
	$('#'+id).children().addClass('datahighlight');
}

function non_exclude_add()
{
	$('#message').hide();
	var gr = jQuery("#list2").getGridParam('selarrrow');
	if( gr != '' ) 
	{
		var data = "id="+gr+'&oper=nonexclude';
		var url = base_url+"prod_management/deleteRow";
		$.ajax({
			type: 'POST',
			data: data,
			url: url,
			cache: false,
			dataType: 'json',
			success: function(data)
			{
				$('#message').show();
				$('#message').html(data.html);
				$('#message').removeClass('error').removeClass('error').addClass(data.div_class);
				$("#list2").trigger("reloadGrid"); 
			}
		});
	}
	else 
		alert("Please Select Row");
}
function non_exclude_delete()
{
	$('#message').hide();
	var gr = jQuery("#list2").getGridParam('selarrrow');
	if( gr != '' ) 
	{
		var data = "id="+gr+'&oper=delnonexclude';
		var url = base_url+"prod_management/deleteRow";
		$.ajax({
			type: 'POST',
			data: data,
			url: url,
			cache: false,
			dataType: 'json',
			success: function(data)
			{
				$('#message').show();
				$('#message').html(data.html);
				$('#message').removeClass('error').removeClass('error').addClass(data.div_class);
				$("#list2").trigger("reloadGrid"); 
			}
		});
	}
	else 
		alert("Please Select Row");
}

////////////////////////////////////////////////////////////////////////////////////
function save_emails(form)
{
	$("#message_emails").hide();
	if(validate_sec_emails()==true)
	{
		var data = $("#new_form_emails").serializeArray();
		var url = base_url+"account/save_emails";
		$.ajax({
			type: 'POST',
			data: data,
			url: url,
			cache: false,
			dataType: 'json',
			success: function(data)
			{
				$("#message_emails").show();
				$("#message_emails").html("<p>"+data.html+"</p>");
				$("#message_emails").removeClass("success").removeClass("error").addClass(data.div_class);
			}
		});
	}
	return false;
}
function save_notifys(form)
{
	$("#message_notify").hide();
	
		var data = $("#new_form_notify").serializeArray();
		var url = base_url+"account/save_notify";
		$.ajax({
			type: 'POST',
			data: data,
			url: url,
			cache: false,
			dataType: 'json',
			success: function(data)
			{
				$("#message_notify").show();
				$("#message_notify").html("<p>"+data.html+"</p>");
				$("#message_notify").removeClass("success").removeClass("error").addClass(data.div_class);
			}
		});
	
	return false;
}

function enable_disable_down(val, id)
{
	if(val == 1)
	{
		$("#slider_"+id ).slider({ disabled: false });
		$('#'+id).attr("disabled", false);
	}else
	{
		$("#slider_"+id ).slider({ disabled: true });
		$('#'+id).attr("disabled", true);
	}
}
function enable_disable_up(val, id)
{
	if(val == 1)
	{
		$("#slider_"+id ).slider({ disabled: false });
		$('#'+id).attr("disabled", false);
	}else
	{
		$("#slider_"+id ).slider({ disabled: true });
		$('#'+id).attr("disabled", true);
	}
}
function removeSecEmail(id, email_id,type)
{
	var ext
	if(type == 'ce')
		 ext = '_ce';
	else 
		ext ='';
		
	var idd = $('#count_rows'+ext).val();
	document.body.style.cursor = 'wait';
	$("#"+id).remove();
	var id = $('#count_rows'+ext).val();
	$('#count_rows'+ext).val(parseInt(id)-1);
	$.ajax({
		type: 'POST',
		data: 'email_id='+email_id,
		url: base_url+"account/removeSecEmail",
		cache: false,
		//dataType: 'json',
		success: function(data)
		{
			if(idd < 2)
			{
				if(type == 'ce')
					$('#first_row_data_ce').html('<div class="row_dat"><input type="hidden" name="count_rows_ce" id="count_rows_ce" value="1" /><div class="lbel">Additional Emails:</div><div class="lbl_inpuCnt" style="width:300px;"><input type="text" name="ce_email[]" id="ce_email[]" class="account_med" onblur="validate_ce_emails();" value="" />&nbsp;<a href="javascript:void(0);" onclick="add_email(\'ce\');">Add New</a></div><div  class="clear"></div></div>');
				else
					$('#first_row_data_sec').html('<div class="row_dat"><input type="hidden" name="count_rows" id="count_rows" value="1" /><div class="lbel">Additional Emails:</div><div class="lbl_inpuCnt" style="width:300px;"><input type="text" name="sec_email[]" id="sec_email[]" class="account_med" onblur="validate_sec_emails();" value="" />&nbsp;<a href="javascript:void(0);" onclick="add_email();">Add New</a></div><div  class="clear"></div></div>');
			}
			document.body.style.cursor = '';
		}
	});
}
function add_email(type)
{
	if(type == 'ce')
	{
		var id = $('#count_rows_ce').val();
		$('#first_row_data_ce').append('<div class="row_dat" id="id_'+id+'"><div class="lbel">&nbsp;</div><div class="lbl_inpuCnt" style="width:250px;"><input type="text" name="ce_email[]" id="ce_email[]" class="account_med" value="" onblur="validate_ce_emails();"  />&nbsp;<a href="javascript:void(0);" onclick="removeSecEmail(\'id_'+id+',0,ce\');">Delete</a></div><div  class="clear"></div></div>');
		$('#count_rows_ce').val(parseInt(id)+1);
	}
	else
	{
		var id = $('#count_rows').val();
		$('#first_row_data_sec').append('<div class="row_dat" id="id_'+id+'"><div class="lbel">&nbsp;</div><div class="lbl_inpuCnt" style="width:250px;"><input type="text" name="sec_email[]" id="sec_email[]" class="account_med" value="" onblur="validate_sec_emails();"  />&nbsp;<a href="javascript:void(0);" onclick="removeSecEmail(\'id_'+id+'\');">Delete</a></div><div  class="clear"></div></div>');
		$('#count_rows').val(parseInt(id)+1);
	}
}
function show_files_fb()
{
	var url = base_url+"exclude_items/ret_files";
	$.ajax({
		type:'POST',
		data: '',
		url: url,
		cache: false,
		dataType: 'json',
		success: function()
		{}
	});
	op_fb(h_txt, html.html, html.div_class);
}
function f_check_down(val)
{
	//alert(val);
	if(val == 1)
	{
		$('#show_down_percent').show();
	}else
	{
		$('#min_not_reprice_percentage').val('');
		$('#show_down_percent').hide();
	}
}
function f_check_up(val)
{
	//alert(val);
	if(val == 1)
	{
		$('#show_up_percent').show();
	}else
	{
		$('#max_not_reprice_percentage').val('');
		$('#show_up_percent').hide();
	}
}
function test_vtip()
{
	$("a[title]").tooltip();
}
/*$(function(){
$('a.clickTip').aToolTip({
		clickIt: true,
		tipContent: $(this).attr('title')
	});	
});	*/
var check = false;
function go_to_url(type, id)
{
	var mydata = type+','+id;
	var html = 'Report Name: <input type="text" name="report_name" id="report_name" class="account_med" value="" />';
	html += ' <input type="button" name="go" id="go" class="btn_next" value="" />';
	op_fb('', html, html.div_class);
	$('#go').click(function() {
		var name =  $('#report_name').val();
			if(name !='')
			{	
				$(document).trigger('close.facebox');
				window.location = base_url+"export/reports/0/"+type+"/"+id+"/"+name;
			}
		});	
}
function go_to_export(val)
{	
	if(val == 'exclude')
		window.location = base_url+"export/excludeItems";	
	else
		window.location = base_url+"export/statusBased/"+val;	
}

function export_to_excel(name,key)
{	
	if(name != '' && key !='')
		window.location = base_url+"mreports/export/"+name+"/"+key;	
}

function show_inline_edit(id)
{
	var data_2 = '';
	if($('#settings').val()!=0)
		data_2 = "&settings="+$('#settings').val();
	var url = base_url+"prod_management/show_inline_edit";
	$.ajax({
		type: 'POST',
		data: 'id='+id+data_2,
		url: url,
		cache: false,
		dataType: 'json',
		success: function(html)
		{
			$("#ret_"+id).html(html.html);
		}
	});
}

function close_inline_edit(id)
{
	var url = base_url+"prod_management/close_inline_edit";
	var data_2 = '';
	if($('#settings').val()!=0)
		data_2 = "&settings="+$('#settings').val();
	$.ajax({
		type: 'POST',
		data: 'id='+id+data_2,
		url: url,
		cache: false,
		dataType: 'json',
		success: function(html)
		{
			$("#ret_"+id).html(html.html);
		}
	});
}
function edit_product(form_id)
{
	var data = $("#"+form_id).serialize();
	var url = base_url+"prod_management/edit_prod";
	$.ajax({
		type: 'POST',
		data: data,
		url: url,
		cache: false,
		dataType: 'json',
		success: function(html)
		{
			var h_txt = '';
			if(data.div_class == 'success')
			{
				h_txt = 'Congratulations';
			}else
			{
				h_txt = 'Commiserations';
			}
			op_fb(h_txt, html.html, html.div_class);
			close_inline_edit(html.id);
		}
	});
}
function show_fields(val)
{
	//alert(val);
	if(val=='Weekly')
	{
		$("#day").attr("disabled", false);
		$("#time").attr("disabled", false);
		$("#slider" ).slider({ disabled: false });
		$("#time_type").attr("disabled", false);
		$("#freq_period").attr("disabled", true);
		$("#freq_type").attr("disabled", true);
		$("#day_div").show();
		$("#freq_div").hide();
		$("#timme_div").show();
	}else if(val=='Daily')
	{
		$("#day_div").hide();
		$("#freq_div").hide();
		$("#timme_div").show();
		$("#day").attr("disabled", true);
		$("#time").attr("disabled", false);
		$("#slider" ).slider({ disabled: false });
		$("#time_type").attr("disabled", false);
		$("#freq_period").attr("disabled", true);
		$("#freq_type").attr("disabled", true);
	}else{
		$("#day").attr("disabled", true);
		$("#time").attr("disabled", true);
		$("#slider" ).slider({ disabled: true });
		$("#time_type").attr("disabled", true);
		$("#freq_period").attr("disabled", false);
		$("#freq_type").attr("disabled", false);
		$("#day_div").hide();
		$("#freq_div").show();
		$("#timme_div").hide();
	}
}
function op_fb_short()
{
	jQuery.facebox('<h1 align="center">Add Shortcut</h1><div id="message_fb"></div><br /><form name="test" id="formID"><div class="row_dat"><div class="lbel">Shortcut Name:</div><div class="lbl_inpuCnt"><input type="text" name="shortcut_name" id="shortcut_name" class="account_med" value=""/></div><div  class="clear"></div></div><div class="row_dat"><div class="lbel"></div><div class="lbl_inpuCnt" style="margin-left:120px"><input type="button" name="add_short" id="add_short" class="btn_save" onclick="save_shortcut();" /></div><div  class="clear"></div></div></form>', 'my-groovy-style'); 
}
function go_to_home()
{
	window.location = base_url+'dashboard';	
}
function op_fb_save()
{
	var data = "save_steps=1";
	var url = base_url+"dashboard/save_step_info";
	$.ajax({
		type: 'POST',
		data: data,
		url: url,
		cache: false,
		success: function(data){
			jQuery.facebox('<h1 align="center">No Problem</h1><div class="row_dat"><p>No problem. We&rsquo;ll save the rest of the information and you can import your information the next time you visit. However, keep in mind that most of our functions here won&rsquo;t work until we bring your Amazon items into the system.</p><p align="right"><a href="'+base_url+'dashboard">Continue..</a></p></div>', 'my-groovy-style'); 		
			//setTimeout("go_to_home()", '1000');
		}
	});
}
function trim(str)
{
    return str.replace(/^[\s]+/,'').replace(/[\s]+$/,'').replace(/[\s]{2,}/,' ');
}
function save_payment_info(form_obj)
{
	$("#message_cc").hide();
	if(validate_payment_info(form_obj))
	{
		var data = 'billing_address='+$('#billing_address').val()+'&name_on_cc='+$('#name_on_cc').val()+'&cc_info='+$('#cc_info_month').val()+'/'+$('#cc_info_year').val()+'&last_charged='+$('#last_charged').val()+"&type_of_cc="+$('#type_of_cc').val()+"&cc_number="+$('#cc_number').val()+"&cvv_numb="+$('#cvv_numb').val();
		var url = base_url+'account/save_cc_info';
		$.ajax({
			type: 'POST',
			data: data,
			url: url,
			dataType: 'json',
			cache: false,
			success: function(s)
			{
				$("#message_cc").show();
				$("#message_cc").html("<p>"+s.html+"</p>");
				$("#message_cc").removeClass("success").removeClass("error").addClass(s.div_class);
			}
		});
		return false;
	}
}
function delete_shortcut(id)
{
	$('#message').hide();
	document.body.style.cursor = 'wait';
	var data = "id="+id;
	var url = base_url+"dashboard/delete_shortcut";
	$.ajax({
		type: 'POST',
		data: data,
		url: url,
		cache: false,
		dataType: 'json',
		success: function(data)
		{
			document.body.style.cursor = '';
			$('#message').show();
			$('#message').html(data.html);
			$('#message').removeClass('success').removeClass('error').addClass(data.div_class);
			$("#del_"+id).remove();
		}
	});
}
function delete_report(id)
{
	$('#message').hide();
	document.body.style.cursor = 'wait';
	var data = "id="+id;
	var url = base_url+"reports_glob/delete_report";
	$.ajax({
		type: 'POST',
		data: data,
		url: url,
		cache: false,
		dataType: 'json',
		success: function(data)
		{
			document.body.style.cursor = '';
			$('#message').show();
			$('#message').html(data.html);
			$('#message').removeClass('success').removeClass('error').addClass(data.div_class);
			$("#del_"+id).remove();
		}
	});
}
function save_shortcut()
{
	var shortcut_name = $("#shortcut_name").val();
	shortcut_name = trim(shortcut_name);
	if(shortcut_name != "")
	{
		var data = "shortcut_name="+shortcut_name;
		var url = base_url+"dashboard/save_shortcut";
		$.ajax({
			type: 'POST',
			data: data,
			url: url,
			cache: false,
			dataType: 'json',
			success: function(data)
			{
				if(data.div_class == 'success')
					$("#left_content_hid_shortcut").append("<li><a href='"+data.shortcut_url+"'>"+shortcut_name+"</a></li>");
				else if(data.div_class == 'redirect')
					window.location = base_url+'dashboard/shortcuts';
				$("#message_fb").html(data.html);
				$("#shortcut_name").val('')
				$("#message_fb").removeClass("success").removeClass("error").addClass(data.div_class);
				$(document).trigger('close.facebox')
			}
		});
	}else
	{
		alert("Please enter shortcut name.");	
		$("#shortcut_name").focus();
	}
}
function sort_val(limit, offset, sort_type, uri, sort_column){
	//alert(sort_column);
	document.body.style.cursor = 'wait';
	var url = base_url+uri+'/sort_data';
	var data_2 = '';
	if($('#settings').val()!=0)
		data_2 = "&settings="+$('#settings').val();
	var data = 'limit='+limit+'&offset='+offset+'&sort_type='+sort_type+'&sort_column='+sort_column+data_2;
	$.ajax({
		type: 'POST',
		data: data,
		url: url,
		cache: false,
		success: function(html)
		{
			document.body.style.cursor = '';
			$('#returned_data').html(html);
			test_vtip();
		},
		error:function (xhr, ajaxOptions, thrownError){
			//alert(xhr.status);
			//alert(thrownError);
		}  
	});
}
function sort_val_new(limit, offset, sort_type, uri, sort_column){
	document.body.style.cursor = 'wait';
	var url = base_url+uri+'/sort_data_new';
	var data_2 = '';
	if($('#settings').val()!=0)
		data_2 = "&settings="+$('#settings').val();
	var data = 'limit='+limit+'&offset='+offset+'&sort_type='+sort_type+'&sort_column='+sort_column+data_2;
	$.ajax({
		type: 'POST',
		data: data,
		url: url,
		cache: false,
		success: function(html)
		{
			document.body.style.cursor = '';
			$('#returned_data').html(html);
			test_vtip();
		},
		error:function (xhr, ajaxOptions, thrownError){
			//alert(xhr.status);
			//alert(thrownError);
		}  
	});
}
function check_all(id, name, name_check)
{
	if($("form#" + id + " INPUT[name=" + name_check + "][type='checkbox']").attr('checked'))
	{
		$("form#" + id + " INPUT[name=" + name + "][type='checkbox']").attr('checked', true);
	}else
	{
		$("form#" + id + " INPUT[name=" + name + "][type='checkbox']").attr('checked', false);
	}
}
function save_account_info(form_obj)
{
	if(validate_account(form_obj))
	{
		document.body.style.cursor = 'wait';
		var form_id = form_obj.id;
		$('#message_account').hide();
		var data = $("#"+form_id).serialize();
		//alert(data);
		data = data+"&edit_setting_button=1";
		var url = base_url+"account/edit_setting";
		$.ajax({
			type: 'POST',
			data: data,
			url: url,
			cache: false,
			dataType: 'json',
			success: function(data)
			{
                          if(data == 1)
                            $('#message_account').hide();
                          else{
                            document.body.style.cursor = '';
                            $('#message_account').show();
                            $("#message_account").removeClass('message').removeClass('success').removeClass('error').addClass(data.div_class);
                            $('#message_account').html("<p>"+data.html+"</p>");
//                            $("#edit_setting_button").attr("disabled", "true");
                          }
			}
		});
		return false;
	}
}
function save_changed_password_info(form_obj)
{
	if(validate_chng_password(form_obj))
	{
		document.body.style.cursor = 'wait';
		var form_id = form_obj.id;
		$('#message_password').html('');
		var data = $("#"+form_id).serialize();
		data = data+"&change_password=1";
		var url = base_url+"account/changepassword";
		$.ajax({
			type: 'POST',
			data: data,
			url: url,
			cache: false,
			dataType: 'json',
			success: function(data)
			{
				document.body.style.cursor = '';
				$("#message_password").show();
				$("#message_password").removeClass('message').removeClass('success').removeClass('error').addClass(data.div_class);
				$('#message_password').html("<p>"+data.html+"</p>");
				if(data.div_class == 'success')
					clear_form_elements(form_obj);
				else
					$("#old_password").select();
			}
		});
		return false;
	}
}
function clear_form_elements(ele) {

    $(ele).find(':input').each(function() {
        switch(this.type) {
            case 'password':
            case 'select-multiple':
            case 'select-one':
            case 'text':
            case 'textarea':
                $(this).val('');
                break;
            case 'checkbox':
            case 'radio':
                this.checked = false;
        }
    });

}

function show_divs(id, id_2, class_up, class_down)
{
	//alert(id);
	var count_slides = $('#count_slides').val();
	//alert(count_slides);
	if($('#'+id).is(':visible'))
	{
		$('#'+id).slideUp();	
		$('#'+id_2).removeClass(class_up).addClass(class_down);
	}else
	{
		$('#'+id).slideDown();
		$('#'+id_2).removeClass(class_down).addClass(class_up);	
	}
	/*for(var i=1;i<=count_slides;i++)
	{
		var test = 'content_'+i;
		var test_2 = 'display_'+i;
		//alert(test);
		if(test == id){
			if($('#content_'+i).is(':visible'))
			{
				$('#content_'+i).slideUp();
				$('#'+test_2).removeClass(class_up).addClass(class_down);
			}else
			{
				$('#content_'+i).slideDown();
				$('#'+test_2).removeClass(class_down).addClass(class_up);
			}
		}
		else{
			$('#content_'+i).slideUp();	
			$('#display_'+i).removeClass(class_up).addClass(class_down);
		}
	}*/
}
/*function show_divs_left(id, id_2, class_down, class_up)
{
	for(var i=10;i<=13;i++)
	{
		var test = 'content_'+i;
		var test_2 = 'display_'+i;
		//alert(test);
		if(test == id){
			if($('#content_'+i).is(':visible'))
			{
				$('#content_'+i).slideUp();
				$('#'+test_2).removeClass(class_up).addClass(class_down);
			}else
			{
				$('#content_'+i).slideDown();
				$('#'+test_2).removeClass(class_down).addClass(class_up);
			}
		}
		else{
			$('#content_'+i).slideUp();	
			$('#display_'+i).removeClass(class_up).addClass(class_down);
		}
	}
}*/
function Subscribe(controller, id, sub_id)
{
	document.body.style.cursor = 'wait';
	var url = base_url+controller+'/Subscribe/'+sub_id+'/'+id;
	$.ajax({
		type: 'POST',
		data: '',
		url: url,
		cache: false,
		dataType: 'json',
		success: function(html)
		{
			//alert(html);
			test_vtip();
			var cnt_total = $("#count_subs").val();
			for(var i=1;i<=cnt_total;i++)
			{
				var div_id = 'sub_'+i;
				if(html.div == div_id)
				{
					$("#"+html.div).html(html.result);
				}else
				{
					$("#"+div_id).html('<a href="javascript:void(0);" onclick="javascript:Subscribe(\''+controller+'\', '+i+', '+i+');">Subscribe</a>');
				}
			}
			document.body.style.cursor = '';
		}
	});
}
function unSubscribe(controller, id, sub_id)
{
	document.body.style.cursor = 'wait';
	var url = base_url+controller+'/unSubscribe/'+sub_id+'/'+id;
	//alert(url);
	$.ajax({
		type: 'POST',
		data: '',
		url: url,
		cache: false,
		dataType: 'json',
		success: function(html)
		{
			//alert(html.result);
			test_vtip();
			$("#"+html.div).html(html.result);
			document.body.style.cursor = '';
		}
	});
}
function delete_this(controller, c_function, del_id)
{
	$("#message").hide();
	var cnfm = confirm("Are you sure you want to delete this record?");
	if(cnfm)
	{
		document.body.style.cursor = 'wait';
		var url = base_url+controller+"/"+c_function+"/"+del_id;
		var data = '';
		$.ajax({
			type: 'POST',
			data: data,
			url: url,
			cache: false,
			dataType: 'json',
			success: function(html)
			{
				if(html.result == 'success')
				{
					document.body.style.cursor = '';
					$('#del_'+del_id).remove();
					$("#message").show();
					$("#message").removeClass('message').removeClass('error').removeClass('success').addClass(html.result)
					$("#message").html("<p>"+html.html+"</p>");
				}
			}
		});
		return false;
	}
}
function bulk_delete(limit, offset, sort_type, uri, sort_column, controller, form_id)
{
	$("#message").hide();
	if($("#bulk_action").val()=='Delete')
	{
		if($("form#" + form_id + " INPUT[name=select[]][type='checkbox']").attr('checked'))
		{
			var cnfm = confirm("Are you sure you want to perform this action?");
			if(cnfm)
			{
				document.body.style.cursor = 'wait';
				var url = base_url+controller+"/action";
				var data = $("#"+form_id).serialize()+"&blk_apply=1";
				$.ajax({
					type: 'POST',
					data: data,
					url: url,
					dataType: 'json',
					success: function(html)
					{
						document.body.style.cursor = '';
						$("#message").show();
						$("#message").removeClass("message").removeClass('error').addClass('success');
						$("#message").html('<p>'+html.html+'</p>');
						for(var i=0; i<html.post.length;i++)
						{
							$("#del_"+html.post[i]).remove();		
						}
						//sort_val(limit, offset, sort_type, uri, sort_column);
					}
				});	
			}
		}else
		{
			$("#message").show();
			$("#message").removeClass("message").removeClass('success').addClass('error');
			$("#message").html("<p>Please select atleast one row to delete.</p>");	
		}
	}else
	{
		$("#message").show();
		$("#message").removeClass("message").removeClass('success').addClass('error');
		$("#message").html("<p>Please select and action to perform.</p>");	
	}
	return false;
}
$(function(){
	$('input[type="checkbox"]').bind('click',function() {
		if($(this).is(':checked')) {
			check = true;
		 }else
		 {
			check = false; 
		 }
	});
});
function bulk_delete_new(limit, offset, sort_type, uri, sort_column, controller, form_id)
{
	$("#message").hide();
	if($("#bulk_action").val()!='bulk')
	{
		//var check = $("form#" + form_id + " INPUT[name=select[]][type='checkbox']").attr('checked')
		if(check)
		{
			var cnfm = confirm("Are you sure you want to perform this action?");
			if(cnfm)
			{
				document.body.style.cursor = 'wait';
				var url = base_url+controller+"/action_prod";
				var data = $("#"+form_id).serialize()+"&blk_apply=1";
				$.ajax({
					type: 'POST',
					data: data,
					url: url,
					dataType: 'json',
					success: function(html)
					{
						document.body.style.cursor = '';
						$("#message").show();
						$("#message").removeClass("message").removeClass('error').addClass('success');
						$("#message").html('<p>'+html.html+'</p>');
						if(html.type=="Delete"){
							for(var i=0; i<html.post.length;i++)
							{
								$("#del_"+html.post[i]).remove();		
							}
						}
						//sort_val(limit, offset, sort_type, uri, sort_column);
					}
				});	
			}
		}else
		{
			$("#message").show();
			$("#message").removeClass("message").removeClass('success').addClass('error');
			$("#message").html("<p>Please select atleast one row to perform action.</p>");	
		}
	}else
	{
		$("#message").show();
		$("#message").removeClass("message").removeClass('success').addClass('error');
		$("#message").html("<p>Please select and action to perform.</p>");	
	}
	return false;
}
function op_fb(h_txt, p_txt, div_class)
{
	//alert(div_class);
	jQuery.facebox('<h1 align="center" id="h_message">'+h_txt+'</h1><p class="'+div_class+'">'+p_txt+'</p>', 'my-groovy-style'); 
}
function save_item_settings(form_obj)
{
	var cont = $("#count").val();
	if(validate_auto_save(cont))
	{
		$('#main_top_message').show();
		$("#message").hide();
		document.body.style.cursor = 'wait';
		var data = $("#item_settings_form").serializeArray();
		var url = base_url+"prod_management/items_rules";
		$.ajax({
			type: 'POST',
			data: data,
			url: url,
			cache: false,
			dataType: 'json',
			success: function(data)
			{
				document.body.style.cursor = '';
				/*$("#message").show();
				$("#message").removeClass('message').addClass(data.div_class);
				$("#message").html("<p>"+data.html+"</p>");*/
				//window.location=base_url+'add_store/add_step_3#message';
				var h_txt = '';
				if(data.div_class == 'success')
				{
					h_txt = 'Congratulations';
				}else
				{
					h_txt = 'Commiserations';
				}
				$('#main_top_message').hide();
				//op_fb(h_txt, data.html, data.div_class);
			}
		});
	}
}

function edit_storeName(form_obj)
{
	if(validate_storeName(form_obj))
	{
		//alert($("#merchant_store_id").val());
		document.body.style.cursor = 'wait';
		$("#message_store").hide();
		var data = $("#formID").serialize()+"&edit_store_name=1";
		var url = base_url+"add_store/edit_store/"+$("#merchant_store_id").val();
		$.ajax({
			type: 'POST',
			data: data,
			url: url,
			cache: false,
			dataType: 'json',
			success: function(html)
			{
				document.body.style.cursor = '';
				$("#message_store").show();
				$("#message_store").removeClass('message').removeClass('success').removeClass('error').addClass(html.div_class);
				$("#message_store").html("<p>"+html.html+"</p>");
			}
		});
		return false;
	}
}
function edit_AmazonSetting(form_obj)
{
	if(validate_amazonSetting(form_obj)){
		document.body.style.cursor = 'wait';
		$("#message_amazon_setting").hide();
		var data = $("#formID").serialize()+"&edit_seller_info=1";
		var url = base_url+"add_store/edit_store/"+$("#merchant_store_id").val();
		$.ajax({
			type: 'POST',
			data: data,
			url: url,
			cache: false,
			dataType: 'json',
			success: function(html)
			{
				document.body.style.cursor = '';
				$("#message_amazon_setting").show();
				$("#message_amazon_setting").removeClass('message').removeClass('success').removeClass('error').addClass(html.div_class);
				$("#message_amazon_setting").html("<p>"+html.html+"</p>");
			}
		});
		return false;
	}
}
function edit_FTPSetting(form_obj)
{
	var check = false;
	if($('input:radio[name=select_type]:checked').val()=='1'){
			var data = $("#formID").serialize()+"&edit_ftp_info=1";
			check = validate_FTPSetting(form_obj);
	}
	else{
		var store_id = $("#merchant_store_id").val();
		var data = "ftp_venue_name=&ftp_host_name=&ftp_folder=&ftp_id=&merchant_store_id="+store_id+"&ftp_password=&edit_ftp_info=1";
		check = true;
	}
	if(check)
	{
		document.body.style.cursor = 'wait';
		$("#message_ftp_setting").hide();
		var url = base_url+"add_store/edit_store/"+$("#merchant_store_id").val();
		$.ajax({
			type: 'POST',
			data: data,
			url: url,
			cache: false,
			dataType: 'json',
			success: function(html)
			{
				document.body.style.cursor = '';
				$("#message_ftp_setting").show();
				$("#message_ftp_setting").addClass(html.div_class);
				$("#message_ftp_setting").html("<p>"+html.html+"</p>");
			}
		});
		return false;
	}
}
function enable_it(id, act, controller)
{
	$('#message').hide();
	var url = base_url+controller+"/enable_it/"+id;
	var data = '';
	$.ajax({
		type: 'POST',
		data: data,
		url: url,
		cache: false,
		dataType: 'json',
		success: function(data)
		{
			/*$('#message').show();
			$('#message').removeClass('message').removeClass('error').removeClass('success').addClass(data.div_class);
			$('#message').html("<p>"+data.html+"</p>");*/
			$("#status_"+id).html(data.html_span);
			test_vtip();
		}
	});
}
function disable_it(id, act, controller)
{
	$('#message').hide();
	var url = base_url+controller+"/disable_it/"+id;
	var data = '';
	$.ajax({
		type: 'POST',
		data: data,
		url: url,
		cache: false,
		dataType: 'json',
		success: function(data)
		{
			/*$('#message').show();
			$('#message').removeClass('message').removeClass('error').removeClass('success').addClass(data.div_class);
			$('#message').html("<p>"+data.html+"</p>");*/
			$("#status_"+id).html(data.html_span);
			test_vtip();
		}
	});
}
function showAddColumn(type)
{
	if(type=='show'){
		$("#add_new_column").html('<div class="lbel">Column Name:</div><div class="lbl_inpuCnt" style="width:300px;"><select class="account_select_med" name="column_name" id="column_name"><option value="sku">Products SKU</option><option value="category">Category</option><option value="asin">Asin</option><option value="title">Title</option><option value="min_price">Min Price</option><option value="ship_min">Min Shipp</option><option value="old_price">Price</option><option value="new_price">New Price</option><option value="shipping_price">Shipping</option><option value="listing_id">Listing ID</option><option value="quantity">Quantity</option><option value="condition">Condition</option><option value="not_priced_reason">Not Priced Reason</option><option value="type">Type</option><option value="priced_status">Priced Status</option><option value="status">Status</option><option value="rank">Rank</option><option value="monitoring">Monitoring</option><option value="last_repricing_run">Last Repricing</option><option value="floor_price">Floor Price ($)</option><option value="ceiling_price">Ceiling Price ($)</option><option value="normal_increment_up_percentage">Normal Up (%)</option><option value="normal_increment_up_amount">Normal Up ($)</option><option value="normal_increment_down_percentage">Normal Down (%)</option><option value="normal_increment_down_amount">Normal Down ($)</option><option value="amazon_increment_up_percentage">Amazon Up (%)</option><option value="amazon_increment_up_amount">Amazon Up ($)</option><option value="amazon_increment_down_percentage">Amazon Down (%)</option><option value="amazon_increment_down_amount">Amazon Down ($)</option><option value="special_increment_down_amount">Buy Box Override ($)</option></select>&nbsp;&nbsp;<input type="button" class="btn_cancel fr" style="margin-left: 5px;" value=" " onclick="showAddColumn(\'hide\')"><input type="button" class="btn_save fr" onclick="addColumn()"></div><div>&nbsp;</div>');
		$("#column_name").focus();
	}
	else
		$("#add_new_column").html('<div class="add_new"><a href="javascript:void(0);" onclick="showAddColumn(\'show\');">add a column</a>&nbsp;|&nbsp;<a href="javascript:void(0);" onclick="$(\'#added_columns\').val(\'\');$(\'#show_hid\').html(\'\');">Clear</a></div><input type="button" class="btn_save fr" onclick="save_settings();"><div class="clear"></div>');	
}
function removeColumn(col_name, col_title)
{
	var cnfm = confirm("Are you sure you want to delete this column?");
	if(cnfm)
	{
		var value = $("#added_columns").val();
		//alert(value);
		$("#"+col_name).remove();
		value = value.replace(col_name+":"+col_title+';', '');
		$("#added_columns").val(value)
	}
}
function addColumn()
{
	if($("#column_name").val()!== '')
	{
		var value = $("#added_columns").val();
		values = value.split(';');
		var cnt = values.length;
		if(value.search($("#column_name").val())===-1)
		{
			if($("#added_columns").val()=='')
				$("#added_columns").val($("#column_name").val()+":"+$("#column_name :selected").text()+";");
			else
				$("#added_columns").val($("#added_columns").val()+$("#column_name").val()+":"+$("#column_name :selected").text()+";");
			var value = $("#added_columns").val();
			value = value.split(';');
			var cnt = value.length-1;
			$("#show_hid").append('<div id="'+$("#column_name").val()+'" class="dyncolumsCnt"><div class="lbl_count">'+cnt+'</div><div class="lbl_inpuCnt_small"><select class="account_select_small"><option>'+$("#column_name").val()+'</option></select></div><div class="wrong"><a href="javascript:void(0);" onclick="removeColumn(\''+$("#column_name").val()+'\', \''+$("#column_name :selected").text()+'\')"><img src="'+image_url+'ico_close.png" alt="Close" title="Close" width="16" height="16" /></a></div></div>');
				$("#add_new_column").html('<div class="add_new"><a href="javascript:void(0);" onclick="showAddColumn(\'show\');">add a column</a>&nbsp;|&nbsp;<a href="javascript:void(0);" onclick="$(\'#added_columns\').val(\'\');$(\'#show_hid\').html(\'\');">Clear</a></div><input type="button" class="btn_save fr" onclick="save_settings();"><div class="clear"></div>');
		}else
		{
			alert("Already exist.");	
		}
	}else
	{
		alert("Please enter column name.");
		$("#column_name").focus();
	}
}
function changeView(setting_id)
{
	if(setting_id > 0)
	{
		var data = "id="+setting_id;
		var url = base_url+"prod_management/return_arrays"
		$.ajax({
			type: 'POST',
			data: data,
			url: url,
			cache: false,
			dataType: 'json',
			success: function(data)
			{
				$('#select').val('select');
				$('#content_area').html(data.html);
			}
		});
	}else if(setting_id != 'select')
	{
		var data = "id="+setting_id;
		var url = base_url+"prod_management/return_arrays"
		$.ajax({
			type: 'POST',
			data: data,
			url: url,
			cache: false,
			dataType: 'json',
			success: function(data)
			{
				$('#settings').val('select');
				$('#content_area').html(data.html);
			}
		});
	}
}
function changeView_new_2(setting_id)
{
	if(setting_id > 0)
	{
		var data = "id="+setting_id;
		var url = base_url+"exclude_items/change_view_new"
		$.ajax({
			type: 'POST',
			data: data,
			url: url,
			cache: false,
			dataType: 'json',
			success: function(data)
			{
				$("#returned_data").html(data.html);
				$("#show_hid").html(data.html_new);
				$("#added_columns").val(data.added_columns);
			}
		});
	}
}
function changeView_new(setting_id)
{
	if(setting_id > 0)
	{
		var data = "id="+setting_id;
		var url = base_url+"exclude_items/change_view_new"
		$.ajax({
			type: 'POST',
			data: data,
			url: url,
			cache: false,
			dataType: 'json',
			success: function(data)
			{
				$("#returned_data").html(data.html);	
			}
		});
	}
}
function save_settings()
{
	if($("#added_columns").val()=='')
	{
		alert("Please add columns first.");	
	}else if($("#setting_name").val()=='')
	{
		alert("Please enter setting name.");	
	}else
	{
		var column_name = $("#added_columns").val();
		var setting_name = $("#setting_name").val();
		var data = "column_name="+column_name+"&setting_name="+setting_name;
		var url = base_url+"exclude_items/add_columns";
		//alert(url);
		$.ajax({
			type: 'POST',
			data: data,
			url: url,
			cache: false,
			dataType: 'json',
			success: function(data)
			{
				if(data.div_class == 'success'){
					$("#added_columns").val('');
					$("#saved_after").html('<div class="left_info"><div class="tags_hid">Create New Column Settings</div><div id="show_hid"></div></div><div class="right_info"><div class="tags_hid">Name Column Settings</div><input type="text" name="setting_name" id="setting_name" class="account_lrg" /></div><div class="clear"></div>');
					$("#settings").append('<option value="'+data.insert_id+'">'+setting_name+'</option>');
				}
					$('#message').show();
					$('#message').removeClass('message').removeClass('error').removeClass('success').addClass(data.div_class);
					$('#message').html("<p>"+data.html+"</p>");
					$("#status_"+id).html(data.div_class);
			}
		});
	}
}
function lookup(inputString) {
	if(inputString.length == 0) {
		// Hide the suggestion box.
		$('#suggestions').hide();
	} else {
		$.post(base_url+"exclude_items/gen_columns", {queryString: ""+inputString+""}, function(data){
			if(data.length >0) {
				$('#suggestions').show();
				$('#autoSuggestionsList').html(data);
			}
		});
	}
} // lookup
	
function fill(thisValue) {
	$('#column_name').val(thisValue);
	setTimeout("$('#suggestions').hide();", 200);
}
function show_form(val)
{
	if(val == 1)
	{
		$("#show_hide").show();
	}else
	{
		$("#show_hide").hide();
	}
}


function rollover()
{
  if(!document.getElementById || !document.createTextNode){return;}
  var n=document.getElementById('nav');
  if(!n){return;}
  var lis=n.getElementsByTagName('li');
  for (var i=0;i<lis.length;i++)
  {
    lis[i].onmouseover=function()
    {
      this.className=this.className?'cur':'over';
    }
    lis[i].onmouseout=function()
    {
       this.className=this.className=='cur'?'cur':'';
    }
  }
}
//window.onload=rollover;