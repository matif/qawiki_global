/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function()
{
$('#addBtn').click(function()
  {
    var emailFilter=/^.+@.+\..{2,3}$/;
    if($('#user_email').val().match(emailFilter)==null)
    {
     $('#error_email').show();
    }
    else
    {
      $.ajax({
          url: base_url+"teams/sendInvitaion/",
          type:"post",
          dataType: "json",
          data: {
            'teamId':$('#team_id').val(),
            'user_email':$( "#user_email" ).val()
          },
          success: function(msg) {
            $('#error_email').hide();
            if(msg == -1 )
            {
              $('#user_email').val('');
              $('#error').slideDown('fast');
              $('#confirmation').slideUp('fast');

            }
            else
            {
              $('#user_email').val('');
              $('#error').slideUp('fast');
              $('#confirmation').slideDown('fast');
            }
          }
        });
    }

  });
    
  $( "#user_email" ).autocomplete({
    source: function( request, response ) {
      $.ajax({
        url: base_url+"teams/getSuggession/"+request.term,
        dataType: "json",
        data: {
        },
        success: function(items) {
          if(items == -1)
            $("#no_record").show();
          else
            $("#no_record").hide();
          if(!items || items.length == 0) {
            if(typeof auto_complete_no_result != 'undefined')
              auto_complete_no_result();
              return false;
          }
          response($.map( items, function( item ) {
            return {
              label: item.Username,
              value: item.Username,
              Id: item.Id
            }
          }));
        }
      });
    },
    minLength: 1,
    select: function( event, ui ) {
      var callback_func = '';
      if(callback_func != '') {
        var ret = eval(callback_func)(ui.item);
      }
    }
  });
  
});
