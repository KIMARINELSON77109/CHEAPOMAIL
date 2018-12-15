/* global $ */
$(document).ready(function()
{
    //hide nav bar if not logged in
    $("#navbar").hide(); 
    //hide message div if there no error
    $("#log-msg").hide();
    
//###########################uses ajax to login#################################    
    $("#login").on('click', function(event)
    {
            
        event.preventDefault();
            
        var uname = $("#username").val();
        var pass = $("#password").val();
            
        var data = "LoginName="+uname+"&LoginPwd="+pass+"&login=true";
        $.ajax
             ({
                url: 'main.php',
                type: "POST",
                data: data,
                success: function(result)
                {
                    if(uname =="admin" && result!="No User Found")
                    {
                        $("#navbar").show();
                        $("#content").load(result);
                        getMessages();
                    }
                    else if(result === "No User Found")
                    {
                        $("#navbar").hide();
                        $("#log-msg").html("User Info Not Found! Check Login Info!");
                        $("#log-msg").show();
                    }
                    else if(uname != "admin")
                    {
                        $("#navbar").show();
                        $("#content").load(result);
                        $("#adduser").hide();
                        getMessages();
                    }
                }
               
             });
    });
    
//########################uses ajax load html pages#############################    
    $("#navbar ul li a").on('click', function(e)
    {
        var logout = function()
        {
            var data = "logout=true";
            $.ajax({
                url: "main.php",
                type: "POST",
                data: data,
                success: function()
                {
                    window.location.href = "/";
                }
            })
        }
        e.preventDefault();
        var view = $(this).attr("href");
        if(view == "index.html")
        {
            logout();
        }
        else if (view == "home.html")
        {
            $("#content").load(view);
            getMessages();
        }
        else
        {
            $("#content").load(view);
        }
    });
    
//########################sents request to get messages#########################    
    function getMessages()
    {
        var data = 'getmail=true';
        $.ajax({
            url: 'main.php',
            method: 'POST',
            data: data,
            success: function(result)
            {
                $("#mail").html(result);
                $('.recv').hide();
                $('.showbutton').on('click', function()
                {
                    $(this).prev().slideToggle(400);
                    readMessage($(this).parent(), $(this).next().text());
                });
                setTimeout(getMessages,30000);
            }
        })
    }
    
//#######################handle reading message#################################   
    function readMessage(div, mid){
        var data = "read_id="+mid;
        $.ajax({
            url: 'main.php',
            method: 'POST',
            data: data,
            success: function(result)
            {
                if(result == "Read")
                {
                    $(div).attr('class', 'mail read');
                }
            }
        });
    }
    
});            