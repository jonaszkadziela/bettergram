import $ from 'jquery';
import { responsivePopover } from './popover';

const DISABLE_SUBMIT = ENVIRONMENT == 'production' ? true : false;
var validation_popover_options =
{
	trigger: 'manual',
	placement: 'auto',
	title: 'Nieprawidłowe dane!',
	content: 'Opis błędu.',
  template:
    '<div class="popover" role="tooltip">' +
      '<div class="arrow"></div>' +
        '<h3 class="popover-header text-white bg-danger"></h3>' +
      '<div class="popover-body"></div>' +
    '</div>'
};

class FieldValidations
{
  constructor($field, validations, popover)
  {
    this.$field = $field;
    this.validations = validations;
    this.popover = popover;
    this.passed_validations = false;
    this.init();
  }

  init()
  {
    this.popover.setOptions(validation_popover_options);

    this.$field.on('keyup change', () =>
    {
      this.run();
    });
    $(window).on('resize', () =>
    {
      if (this.popover.options.placement != validation_popover_options.placement)
      {
        this.popover.setOptions(validation_popover_options);
        this.run();
      }
    });
  }

  run()
  {
    let success = true;

    for (let i = 0; i < this.validations.length; i++)
    {
      if (!this.validations[i].test(this.$field))
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

    if (success || this.$field.val() == '')
    {
      this.popover.hide();
    }
    this.passed_validations = success ? true : false;
  }
}

function disableSubmit($button, disabled)
{
  $button.attr('disabled', disabled ? DISABLE_SUBMIT : false);
  $button.attr('tabindex', disabled ? '-1' : '0');
  $button.parent().attr('tabindex', disabled ? '0' : '-1');
  $button.parent().tooltip(disabled ? 'enable' : 'disable');
}

$(() =>
{
  $('button[type="submit"]').each(function()
  {
    const initial_text = $(this).html();
    const loading_text = initial_text + '<i class="fas fa-sync-alt fa-spin ml-0-5"></i>';

    $(this).on('click', () =>
    {
      if ($(this).html() != loading_text)
      {
        $(this).html(loading_text);
      }
    });

    disableSubmit($(this), true);
  });

  responsivePopover(validation_popover_options);
  $(window).on('resize', () =>
  {
    responsivePopover(validation_popover_options);
  });
});

export { FieldValidations, disableSubmit };
