import $ from 'jquery';
import 'bootstrap/js/dist/modal';

$(document).ready(function()
{
  $('.js-confirmation-modal').each(function()
  {
    var parent = $(this);
    var modal = parent.attr('data-target');
    $(modal).on('hide.bs.modal', function()
    {
      if ($(document.activeElement).attr('data-action') != 'accept')
      {
        parent.prop('checked', false);
      }
    });
    parent.on('change', function()
    {
      if (parent.prop('checked'))
      {
        $(modal).modal('show');
      }
    });
  });
});
