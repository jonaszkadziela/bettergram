import $ from 'jquery';
import Validation from './validation';
import { Popover } from './popover';
import { FieldValidations, disableSubmit } from './field_validations';

$(() =>
{
  const form = $('#log_in_user_form');

  if (form.length)
  {
    const login = form.find('#login');
    const password = form.find('#password');
    const submit = form.find('button[type="submit"]');

    let login_validations = new FieldValidations
    (
      login,
      new Array
      (
        new Validation('Login musi posiadać od 6 do 20 znaków!', /^.{6,20}$/m),
        new Validation('Login może składać się tylko z liter i cyfr (bez polskich znaków)!', /^[a-zA-Z0-9]{6,20}$/m)
      ),
      new Popover(login)
    );
    let password_validations = new FieldValidations
    (
      password,
      new Array
      (
        new Validation('Hasło musi posiadać od 6 do 20 znaków!', /^.{6,20}$/m),
        new Validation('Hasło musi zawierać przynajmniej 1 dużą literę, 1 małą literę i 1 cyfrę!', /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])[a-zA-Z0-9!@#$%^&*]{6,20}$/m)
      ),
      new Popover(password)
    );
    let validations = [login_validations, password_validations];

    validations.forEach(validation =>
    {
      validation.$field.on('keyup change', () =>
      {
        let disabled = false;

        validations.forEach(validation =>
        {
          if (!validation.passed_validations)
          {
            disabled = true;
          }
        });
        disableSubmit(submit, disabled);
      });
      validation.$field.trigger('change');
    });
  }
});
