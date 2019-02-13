import $ from 'jquery';
import Validation from './validation';
import { Popover } from './popover';
import { FieldValidations, disableSubmit } from './field_validations';

$(() =>
{
  const form = $('#create_user_form');

  if (form.length)
  {
    const login = form.find('#login');
    const email = form.find('#email');
    const password1 = form.find('#password1');
    const password2 = form.find('#password2');
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
    let email_validations = new FieldValidations
    (
      email,
      new Array
      (
        new Validation('Podany adres email jest nieprawidłowy!', /^(([^<>()\[\]\.,;:\s@\']+(\.[^<>()\[\]\.,;:\s@\']+)*)|(\'.+\'))@(([^<>()[\]\.,;:\s@\']+\.)+[^<>()[\]\.,;:\s@\']{2,})$/mi)
      ),
      new Popover(email)
    );
    let password1_validations = new FieldValidations
    (
      password1,
      new Array
      (
        new Validation('Hasło musi posiadać od 6 do 20 znaków!', /^.{6,20}$/m),
        new Validation('Hasło musi zawierać przynajmniej 1 dużą literę, 1 małą literę i 1 cyfrę!', /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])[a-zA-Z0-9!@#$%^&*]{6,20}$/m)
      ),
      new Popover(password1)
    );
    let password2_validations = new FieldValidations
    (
      password2,
      new Array
      (
        new Validation('Hasło musi posiadać od 6 do 20 znaków!', /^.{6,20}$/m),
        new Validation('Hasło musi zawierać przynajmniej 1 dużą literę, 1 małą literę i 1 cyfrę!', /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])[a-zA-Z0-9!@#$%^&*]{6,20}$/m),
        new Validation('Podane hasła nie są identyczne!', () =>
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
    let validations = [login_validations, email_validations, password1_validations, password2_validations];

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
