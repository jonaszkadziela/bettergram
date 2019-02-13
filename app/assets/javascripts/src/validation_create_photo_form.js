import $ from 'jquery';
import Validation from './validation';
import { Popover } from './popover';
import { FieldValidations, disableSubmit } from './field_validations';

$(() =>
{
  const form = $('#create_photo_form');

  if (form.length)
  {
    const photo = form.find('#photo');
    const description = form.find('#description');
    const submit = form.find('button[type="submit"]');

    let photo_validations = new FieldValidations
    (
      photo,
      new Array
      (
        new Validation('Należy wybrać zdjęcie!', () =>
        {
          if (photo.val())
          {
            return true;
          }
          return false;
        })
      ),
      new Popover(photo)
    );
    let description_validations = new FieldValidations
    (
      description,
      new Array
      (
        new Validation('Opis zdjęcia nie może przekraczać 255 znaków!', /^.{0,255}$/m),
        new Validation('Opis zdjęcia musi posiadać przynajmniej jeden znak spoza białych znaków!', () =>
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
    let validations = [photo_validations, description_validations];

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
