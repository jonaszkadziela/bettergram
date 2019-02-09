import $ from 'jquery';
import Popover from './popover';
import Validation from './validation';
import { FieldValidations, disableSubmitButton } from './field_validations';

$(document).ready(function()
{
  var form = $('#create_album_form');

  if (form.length)
  {
    var title = form.find('#title');
    var submit = form.find('#submit');

    var title_validations = new FieldValidations
    (
      title,
      new Array
      (
        new Validation('Tytuł albumu musi posiadać od 3 do 100 znaków!', /^.{3,100}$/m),
        new Validation('Tytuł albumu nie może zaczynać ani kończyć się spacją!', /^[\S].+[\S]$/m)
      ),
      new Popover(title)
    );

    var validations = [title_validations];

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
