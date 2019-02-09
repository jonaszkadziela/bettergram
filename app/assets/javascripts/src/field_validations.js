import $ from 'jquery';
import 'bootstrap/js/dist/tooltip';

var disable_submit_button = ENVIRONMENT == 'production' ? true : false;

class FieldValidations
{
  constructor(field, validations, popover)
  {
    this.field = field;
    this.validations = validations;
    this.popover = popover;
    this.passed_validations = false;
    this.init();
  }

  init()
  {
    var that = this;
    this.field.on('keyup change', function(e)
    {
      // Ignore tab key
      if (e.keyCode != 9)
      {
        that.run();
      }
    });
  }

  run()
  {
    var success = true;

    for (var i = 0; i < this.validations.length; i++)
    {
      if (!this.validations[i].test(this.field))
      {
        if (this.popover.options.content != this.validations[i].error)
        {
          this.popover.hide();
          this.popover.options.content = this.validations[i].error;
        }
        this.popover.show();
        success = false;
        break;
      }
    }

    if (success || this.field.val() == '')
    {
      this.popover.hide();
    }
    this.passed_validations = success ? true : false;
  }
}

function disableSubmitButton(button, disabled)
{
  button.attr('disabled', disabled ? disable_submit_button : false);
  button.attr('tabindex', disabled ? '-1' : '0');
  button.parent().attr('tabindex', disabled ? '0' : '-1');
  button.parent().tooltip(disabled ? 'enable' : 'disable');
}

$(document).ready(function()
{
  $('button[type="submit"]').each(function()
  {
    disableSubmitButton($(this), true);
  });
});

export { FieldValidations, disableSubmitButton };
