$(document).ready(function()
{
  if (XRegExp_enabled)
  {
    var photo = $('#photo');
    var description = $('#description');
    var create_photo_form_button = $('#create_photo_form_button');

    var photo_validations = new FieldValidations
    (
      photo,
      new Array
      (
        new Validation('Należy wybrać zdjęcie!', function()
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

    var description_validations = new FieldValidations
    (
      description,
      new Array
      (
        new Validation('Opis zdjęcia nie może przekraczać 255 znaków!', XRegExp('^.{0,255}$', 'mu')),
        new Validation('Opis zdjęcia nie może zawierać niedozwolonych znaków!', XRegExp('^[\\p{L}\\p{N}\\p{P} ]{0,255}$', 'mu'))
      ),
      new Popover(description)
    );

    description_validations.run();

    var validations = [description_validations, photo_validations];

    validations.forEach(validation => {
      validation.field.on('keyup change', function()
      {
        var disabled = false;
        validations.forEach(validation => {
          if (!validation.passed_validations)
          {
            disabled = true;
          }
        });
        disableSubmitButton(create_photo_form_button, disabled);
      });
    });
  }
});
