$(document).ready(function()
{
  if (XRegExp_enabled)
  {
    var title = $('#title');
    var create_album_form_button = $('#create_album_form_button');

    var title_validations = new FieldValidations
    (
      title,
      new Array
      (
        new Validation('Tytuł albumu musi posiadać od 3 do 100 znaków!', XRegExp('^.{3,100}$', 'mu')),
        new Validation('Tytuł albumu nie może zaczynać ani kończyć się spacją!', XRegExp('^[^\\s].+[^\\s]$', 'mu')),
        new Validation('Tytuł albumu nie może zawierać niedozwolonych znaków!', XRegExp('^[\\p{L}\\p{N}\\p{P} ]{3,100}$', 'mu'))
      ),
      new Popover(title)
    );

    var validations = [title_validations];

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
        disableSubmitButton(create_album_form_button, disabled);
      });
    });
  }
});
