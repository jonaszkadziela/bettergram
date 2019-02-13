import $ from 'jquery';
import Validation from './validation';
import { Popover } from './popover';
import { FieldValidations, disableSubmit } from './field_validations';

$(() =>
{
  const form = $('#create_comment_form');

  if (form.length)
  {
    const comment = form.find('#comment');
    const submit = form.find('button[type="submit"]');

    let comment_validations = new FieldValidations
    (
      comment,
      new Array
      (
        new Validation('Komentarz musi posiadać od 1 do 1000 znaków!', /^.{1,1000}$/m),
        new Validation('Komentarz musi posiadać przynajmniej jeden znak spoza białych znaków!', /^(?=.*[^\s]).+$/m)
      ),
      new Popover(comment)
    );
    let validations = [comment_validations];

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
