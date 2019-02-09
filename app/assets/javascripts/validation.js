var default_popover_options =
{
  enabled: false,
	trigger: 'manual',
	placement: 'left',
	title: 'Nieprawidłowe dane!',
	content: 'Opis błędu.',
  template:
  '<div class="popover" role="tooltip">' +
    '<div class="arrow"></div>' +
      '<h3 class="popover-header text-white bg-danger"></h3>' +
    '<div class="popover-body"></div>' +
  '</div>'
};

var disable_submit_button = ENVIRONMENT == 'production' ? true : false;
var XRegExp_enabled = false;

function Popover(field, options)
{
  options = options || default_popover_options;
  this.field = field;
  this.options = options;

  this.show = function()
  {
    if (!this.options.enabled)
    {
      this.field.popover(this.options);
      this.field.popover('show');
      this.options.enabled = true;
    }
  }

  this.hide = function()
  {
    if (this.options.enabled)
    {
      this.field.popover('dispose');
      this.options.enabled = false;
    }
  }
}

function Validation(error, validation)
{
  this.error = error;
  this.validation = validation;

  this.test = function(field)
  {
    if (typeof validation === 'function')
    {
      return validation(field);
    }
    if (!this.validation.test(field.val()))
    {
      return false;
    }
    return true;
  }
}

function FieldValidations(field, validations, popover)
{
  this.field = field;
  this.validations = validations;
  this.popover = popover;
  this.passed_validations = false;

  this.run = function()
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

    if (success)
    {
      this.popover.hide();
      this.passed_validations = true;
    }
    else
    {
      this.passed_validations = false;
    }
  }

  var that = this;
  this.field.on('keyup change', function()
  {
    that.run();
  });
}

function responsivePopover(popover_options)
{
  if (getBreakpoint() == 'lg' || getBreakpoint() == 'xl')
  {
    popover_options.placement = 'left';
    return;
  }
  popover_options.placement = 'top';
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
  if (typeof XRegExp !== 'undefined' && XRegExp !== null)
  {
    XRegExp_enabled = true;
  }

  $('button[type="submit"]').each(function()
  {
    disableSubmitButton($(this), true);
  });

  $(window).on('load resize', function()
  {
    responsivePopover(default_popover_options);
  });
});
