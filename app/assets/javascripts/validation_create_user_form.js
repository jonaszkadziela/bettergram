$(document).ready(function()
{
  var login = $('#login');
  var email = $('#email');
  var password1 = $('#password1');
  var password2 = $('#password2');
  var sign_up_form_button = $('#sign_up_form_button');

  var login_validations = new FieldValidations
  (
    login,
    new Array
    (
      new Validation('Login musi posiadać od 6 do 20 znaków!', /^.{6,20}$/m),
      new Validation('Login może składać się tylko z liter i cyfr (bez polskich znaków)!', /^[a-zA-Z0-9]{6,20}$/m)
    ),
    new Popover(login)
  );

  var email_validations = new FieldValidations
  (
    email,
    new Array
    (
      new Validation('Podany adres email jest nieprawidłowy!', /^(([^<>()\[\]\.,;:\s@\']+(\.[^<>()\[\]\.,;:\s@\']+)*)|(\'.+\'))@(([^<>()[\]\.,;:\s@\']+\.)+[^<>()[\]\.,;:\s@\']{2,})$/mi)
    ),
    new Popover(email)
  );

  var password1_validations = new FieldValidations
  (
    password1,
    new Array
    (
      new Validation('Hasło musi posiadać od 6 do 20 znaków!', /^.{6,20}$/m),
      new Validation('Hasło musi zawierać przynajmniej 1 dużą literę, 1 małą literę i 1 cyfrę!', /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])[a-zA-Z0-9!@#$%^&*]{6,20}$/m)
    ),
    new Popover(password1)
  );

  var password2_validations = new FieldValidations
  (
    password2,
    new Array
    (
      new Validation('Hasło musi posiadać od 6 do 20 znaków!', /^.{6,20}$/m),
      new Validation('Hasło musi zawierać przynajmniej 1 dużą literę, 1 małą literę i 1 cyfrę!', /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])[a-zA-Z0-9!@#$%^&*]{6,20}$/m),
      new Validation('Podane hasła nie są identyczne!', function()
      {
        if (password1.val() != password2.val())
        {
          return false;
        }
        return true;
      })
    ),
    new Popover(password2)
  );

  var validations = [login_validations, email_validations, password1_validations, password2_validations];

  validations.forEach(validation => {
    validation.field.on('keyup change', function()
    {
      var disabled = false;
      validations.forEach(validation => {
        if (!validation.passed_validations)
        {
          disabled = true;
        }
      });
      disableSubmitButton(sign_up_form_button, disabled);
    });
  });
});
