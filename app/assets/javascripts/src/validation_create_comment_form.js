import $ from 'jquery';
import Popover from './popover';
import Validation from './validation';
import { FieldValidations, disableSubmitButton } from './field_validations';

$(document).ready(function()
{
  var form = $('#create_comment_form');

  if (form.length)
  {
    var comment = form.find('#comment');
    var submit = form.find('#submit');

    var comment_validations = new FieldValidations
    (
      comment,
      new Array
      (
        new Validation('Komentarz musi posiadać od 1 do 500 znaków!', /^.{1,500}$/m),
        new Validation('Komentarz musi posiadać przynajmniej jeden znak spoza białych znaków!', /^(?=.*[^\s]).+$/m)
      ),
      new Popover(comment)
    );

    var validations = [comment_validations];

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
