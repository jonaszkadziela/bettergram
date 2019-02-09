import $ from 'jquery';
import 'bootstrap/js/dist/popover';
import { getBreakpoint } from './main';

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

function responsivePopover(popover_options)
{
  if (getBreakpoint() == 'lg' || getBreakpoint() == 'xl')
  {
    popover_options.placement = 'left';
    return;
  }
  popover_options.placement = 'top';
}

class Popover
{
  constructor(field, options = default_popover_options)
  {
    this.field = field;
    this.options = options;
  }

  show()
  {
    if (!this.options.enabled)
    {
      this.field.popover(this.options);
      this.field.popover('show');
      this.options.enabled = true;
    }
  }

  hide()
  {
    if (this.options.enabled)
    {
      this.field.popover('dispose');
      this.options.enabled = false;
    }
  }
}

$(document).ready(function()
{
  $(window).on('load resize', function()
  {
    responsivePopover(default_popover_options);
  });
});

export default Popover;
