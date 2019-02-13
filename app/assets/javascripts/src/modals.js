import $ from 'jquery';

function confirmationModal($el)
{
  const modal = $el.attr('data-target');

  $(modal).on('hide.bs.modal', () =>
  {
    if ($(document.activeElement).attr('data-action') != 'accept')
    {
      $el.prop('checked', false);
    }
  });
  $el.on('change', () =>
  {
    if ($el.prop('checked'))
    {
      $(modal).modal('show');
    }
  });
}

export { confirmationModal };
