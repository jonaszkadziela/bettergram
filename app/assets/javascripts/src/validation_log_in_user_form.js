import $ from 'jquery';
import Popover from './popover';
import Validation from './validation';
import { FieldValidations, disableSubmitButton } from './field_validations';

$(document).ready(function()
{
  var form = $('#log_in_user_form');

  if (form.length)
  {
    var login = form.find('#login');
    var password = form.find('#password');
    var submit = form.find('#submit');

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

    var password_validations = new FieldValidations
    (
      password,
      new Array
      (
        new Validation('Hasło musi posiadać od 6 do 20 znaków!', /^.{6,20}$/m),
        new Validation('Hasło musi zawierać przynajmniej 1 dużą literę, 1 małą literę i 1 cyfrę!', /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])[a-zA-Z0-9!@#$%^&*]{6,20}$/m)
      ),
      new Popover(password)
    );

    var validations = [login_validations, password_validations];

    validations.forEach(validation =>
    {
      validation.field.on('keyup change', function()
      {
        var disabled = false;
        validations.forEach(validation =>
        {
          if (!validation.passed_validations)
          {
            disabled = true;
          }
        });
        disableSubmitButton(submit, disabled);
      });
      validation.field.trigger('change');
    });
  }
});
