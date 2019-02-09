login_errors = ["Login musi posiadać od 6 do 20 znaków!", "Login może składać się tylko z liter i cyfr (bez polskich znaków)!"];
login_regexs = [/^.{6,20}$/m, /^[a-zA-Z0-9]{6,20}$/m];
login_popover = default_popover;

email_errors = ["Podany adres email jest nieprawidłowy!"];
email_regexs = [/^(([^<>()\[\]\.,;:\s@\"]+(\.[^<>()\[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/mi];
email_popover = default_popover;

password1_errors = ["Hasło musi posiadać od 6 do 20 znaków!", "Hasło musi zawierać przynajmniej 1 dużą literę, 1 małą literę i 1 cyfrę!"];
password1_regexs = [/^.{6,20}$/m, /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])[a-zA-Z0-9!@#$%^&*]{6,20}$/m];
password1_popover = default_popover;

password2_errors = ["Hasło musi posiadać od 6 do 20 znaków!", "Hasło musi zawierać przynajmniej 1 dużą literę, 1 małą literę i 1 cyfrę!", "Podane hasła nie są identyczne!"];
password2_regexs = [/^.{6,20}$/m, /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])[a-zA-Z0-9!@#$%^&*]{6,20}$/m];
password2_popover = default_popover;

$(document).ready(function()
{
  var login = $("#login");
  var email = $("#email");
  var password1 = $("#password1");
  var password2 = $("#password2");
  var sign_up_form_button = $("#sign_up_form_button");

  var popovers = [login, email, password1, password2];
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

  email.bind("keyup change", function()
  {
    if (validate(email, email_regexs[0]))
    {
      hidePopover(email, email_popover);
      valid_data[1] = true;
      return;
    }
    else
    {
      if (email_popover.content != email_errors[0])
      {
        hidePopover(email, email_popover);
        email_popover.content = email_errors[0];
      }
      showPopover(email, email_popover);
    }
    valid_data[1] = false;
    return;
  });

  password1.bind("keyup change", function()
  {
    if (validate(password1, password1_regexs[0]))
    {
      if (validate(password1, password1_regexs[1]))
      {
        hidePopover(password1, password1_popover);
        valid_data[2] = true;
        return;
      }
      else
      {
        if (password1_popover.content != password1_errors[1])
        {
          hidePopover(password1, password1_popover);
          password1_popover.content = password1_errors[1];
        }
        showPopover(password1, password1_popover);
      }
    }
    else
    {
      if (password1_popover.content != password1_errors[0])
      {
        hidePopover(password1, password1_popover);
        password1_popover.content = password1_errors[0];
      }
      showPopover(password1, password1_popover);
    }
    valid_data[2] = false;
    return;
  });

  password2.bind("keyup change", function()
  {
    if (validate(password2, password2_regexs[0]))
    {
      if (validate(password2, password2_regexs[1]))
      {
        if (password1.val() != password2.val())
        {
          if (password2_popover.content != password2_errors[2])
          {
            hidePopover(password2, password2_popover);
            password2_popover.content = password2_errors[2];
          }
          showPopover(password2, password2_popover);
        }
        else
        {
          hidePopover(password2, password2_popover);
          valid_data[3] = true;
          return;
        }
      }
      else
      {
        if (password2_popover.content != password2_errors[1])
        {
          hidePopover(password2, password2_popover);
          password2_popover.content = password2_errors[1];
        }
        showPopover(password2, password2_popover);
      }
    }
    else
    {
      if (password2_popover.content != password2_errors[0])
      {
        hidePopover(password2, password2_popover);
        password2_popover.content = password2_errors[0];
      }
      showPopover(password2, password2_popover);
    }
    valid_data[3] = false;
    return;
  });

  if (disable_submit_button)
  {
    checkSubmitButton(sign_up_form_button, valid_data);

    popovers.forEach(element => {
      element.bind("keyup change", function()
      {
        checkSubmitButton(sign_up_form_button, valid_data);
      });
    });
  }
});
