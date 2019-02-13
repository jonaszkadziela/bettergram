import getBreakpoint from './breakpoint';

class Popover
{
  constructor(field, options)
  {
    this.field = field;
    this.options = options;
  }

  show()
  {
    this.field.popover(this.options);
    this.field.popover('show');
  }

  hide()
  {
    this.field.popover('dispose');
  }

  setOptions(options)
  {
    this.options = Object.assign({}, options);
  }
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

export { Popover, responsivePopover };
