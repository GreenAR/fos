/**
 * Created by root on 7/9/17.
 */
function passwordEye(name,password)
{
    var eyeBtn=$("#ey"+name);
    var pwField=$("#"+name);
    if( pwField.html()==="*****************************")
    {
        pwField.html(password);

        eyeBtn.html("<i class='fa fa-eye-slash'></i>");
        eyeBtn.removeClass("btn-success");
        eyeBtn.addClass("btn-warning");
    }


    else
    {
        pwField.html("*****************************");
        eyeBtn.html("<i class='fa fa-eye'></i>");
        eyeBtn.removeClass("btn-warning");
        eyeBtn.addClass("btn-success");
    }

}