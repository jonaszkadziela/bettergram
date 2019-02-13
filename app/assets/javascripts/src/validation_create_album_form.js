import $ from 'jquery';
import Validation from './validation';
import { Popover } from './popover';
import { FieldValidations, disableSubmit } from './field_validations';

$(() =>
{
  const form = $('#create_album_form');

  if (form.length)
  {
    const title = form.find('#title');
    const submit = form.find('button[type="submit"]');

    let title_validations = new FieldValidations
    (
      title,
      new Array
      (
        new Validation('Tytuł albumu musi posiadać od 3 do 100 znaków!', /^.{3,100}$/m),
        new Validation('Tytuł albumu nie może zaczynać ani kończyć się spacją!', /^[\S].+[\S]$/m)
      ),
      new Popover(title)
    );
    let validations = [title_validations];

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
