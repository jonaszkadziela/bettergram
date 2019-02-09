login_errors = ["Login musi posiadać od 6 do 20 znaków!", "Login może składać się tylko z liter i cyfr (bez polskich znaków)!"];
login_regexs = [/^.{6,20}$/m, /^[a-zA-Z0-9]{6,20}$/m];
login_popover = default_popover;

password_errors = ["Hasło musi posiadać od 6 do 20 znaków!", "Hasło musi zawierać przynajmniej 1 dużą literę, 1 małą literę i 1 cyfrę!"];
password_regexs = [/^.{6,20}$/m, /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])[a-zA-Z0-9!@#$%^&*]{6,20}$/m];
password_popover = default_popover;

$(document).ready(function()
{
  var login = $("#login");
  var password = $("#password");
  var sign_in_form_button = $("#sign_in_form_button");

  var popovers = [login, password];
  var valid_data = [];

  for (var i = 0; i < popovers.length; i++)
  {
    valid_data.push(false);
  }

  login.bind("keyup change", function()
  {
    if (validate(login, login_regexs[0]))
    {
      if (validate(login, login_regexs[1]))
      {
        hidePopover(login, login_popover);
        valid_data[0] = true;
        return;
      }
      else
      {
        if (login_popover.content != login_errors[1])
        {
          hidePopover(login, login_popover);
          login_popover.content = login_errors[1];
        }
        showPopover(login, login_popover);
      }
    }
    else
    {
      if (login_popover.content != login_errors[0])
      {
        hidePopover(login, login_popover);
        login_popover.content = login_errors[0];
      }
      showPopover(login, login_popover);
    }
    valid_data[0] = false;
    return;
  });

  password.bind("keyup change", function()
  {
    if (validate(password, password_regexs[0]))
    {
      if (validate(password, password_regexs[1]))
      {
        hidePopover(password, password_popover);
        valid_data[1] = true;
        return;
      }
      else
      {
        if (password_popover.content != password_errors[1])
        {
          hidePopover(password, password_popover);
          password_popover.content = password_errors[1];
        }
        showPopover(password, password_popover);
      }
    }
    else
    {
      if (password_popover.content != password_errors[0])
      {
        hidePopover(password, password_popover);
        password_popover.content = password_errors[0];
      }
      showPopover(password, password_popover);
    }
    valid_data[1] = false;
    return;
  });

  if (disable_submit_button)
  {
    checkSubmitButton(sign_in_form_button, valid_data);

    popovers.forEach(element => {
      element.bind("keyup change", function()
      {
        checkSubmitButton(sign_in_form_button, valid_data);
      });
    });
  }
});
