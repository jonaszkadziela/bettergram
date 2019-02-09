import $ from 'jquery';
import Popover from './popover';
import Validation from './validation';
import { FieldValidations, disableSubmitButton } from './field_validations';

$(document).ready(function()
{
  var form = $('#update_photo_form');

  if (form.length)
  {
    var description = form.find('#description');
    var submit = form.find('#submit');

    var description_validations = new FieldValidations
    (
      description,
      new Array
      (
        new Validation('Opis zdjęcia nie może przekraczać 255 znaków!', /^.{0,255}$/m),
        new Validation('Opis zdjęcia musi posiadać przynajmniej jeden znak spoza białych znaków!', function()
        {
          if (description.val())
          {
            return /^(?=.*[^\s]).+$/m.test(description.val());
          }
          return true;
        })
      ),
      new Popover(description)
    );

    var validations = [description_validations];

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
