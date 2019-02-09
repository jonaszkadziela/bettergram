$(document).ready(function()
{
  if (XRegExp_enabled)
  {
    var comment = $('#comment');
    var create_comment_form_button = $('#create_comment_form_button');

    var comment_validations = new FieldValidations
    (
      comment,
      new Array
      (
        new Validation('Komentarz musi posiadać od 1 do 500 znaków!', XRegExp('^.{1,500}$', 'mu')),
        new Validation('Komentarz nie może zawierać niedozwolonych znaków!', XRegExp('^[\\p{L}\\p{N}\\p{P} ]{1,500}$', 'mu'))
      ),
      new Popover(comment)
    );

    var validations = [comment_validations];

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
        disableSubmitButton(create_comment_form_button, disabled);
      });
    });
  }
});
