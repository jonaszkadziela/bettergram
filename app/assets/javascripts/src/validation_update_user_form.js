import $ from 'jquery';
import Validation from './validation';
import { Popover } from './popover';
import { FieldValidations, disableSubmit } from './field_validations';

$(() =>
{
  const form = $('#update_user_form');

  if (form.length)
  {
    const email = form.find('#email');
    const password1 = form.find('#password1');
    const password2 = form.find('#password2');
    const current_password = form.find('#current_password');
    const submit = form.find('button[type="submit"]');
    const mode = form.find('#mode');

    new FieldValidations
    (
      email,
      new Array
      (
        new Validation('Podany adres email jest nieprawidłowy!', /^(([^<>()\[\]\.,;:\s@\']+(\.[^<>()\[\]\.,;:\s@\']+)*)|(\'.+\'))@(([^<>()[\]\.,;:\s@\']+\.)+[^<>()[\]\.,;:\s@\']{2,})$/mi)
      ),
      new Popover(email)
    );
    new FieldValidations
    (
      password1,
      new Array
      (
        new Validation('Nowe hasło musi posiadać od 6 do 20 znaków!', /^.{6,20}$/m),
        new Validation('Nowe hasło musi zawierać przynajmniej 1 dużą literę, 1 małą literę i 1 cyfrę!', /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])[a-zA-Z0-9!@#$%^&*]{6,20}$/m)
      ),
      new Popover(password1)
    );
    new FieldValidations
    (
      password2,
      new Array
      (
        new Validation('Nowe hasło musi posiadać od 6 do 20 znaków!', /^.{6,20}$/m),
        new Validation('Nowe hasło musi zawierać przynajmniej 1 dużą literę, 1 małą literę i 1 cyfrę!', /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])[a-zA-Z0-9!@#$%^&*]{6,20}$/m),
        new Validation('Nowe hasła muszą być identyczne!', () =>
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
    let current_password_validations = new FieldValidations
    (
      current_password,
      new Array
      (
        new Validation('Aktualne hasło musi posiadać od 6 do 20 znaków!', /^.{6,20}$/m),
        new Validation('Aktualne hasło musi zawierać przynajmniej 1 dużą literę, 1 małą literę i 1 cyfrę!', /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])[a-zA-Z0-9!@#$%^&*]{6,20}$/m)
      ),
      new Popover(current_password)
    );
    let validations = [current_password_validations];

    if (mode.length)
    {
      validations = [];
      disableSubmit(submit, false);
    }

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
