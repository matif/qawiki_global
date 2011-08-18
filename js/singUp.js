/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


  $('#name').blur(function()
  {
    alert('here');
    $.ajax({
          type: "GET",
          url: base_url+"/register/checkDuplication",
          data: "name" + $('#name').val(),
          dataType: "html",
          success: function(msg){
             if(msg == -1 )
             {
               $('#name_error').val('This name is already registerd');
             }
          },
          error:function(msg)
          {
              alert('error'+msg);
          }

        });
  
  });
  $('#email').blur(function()
  {
    $.ajax({
          type: "GET",
          url: base_url+"/register/checkDuplication",
          data: "email" + $('#email').val(),
          dataType: "html",
          success: function(msg){
             if(msg == -1 )
             {
               $('#email_error').val('This email is already registerd');
             }
          },
          error:function(msg)
          {
              alert('error'+msg);
          }

        });

  });
