import $ from 'jquery';

function manualTooltip($el)
{
  let $target = null;
  let focused = false;

  $el.on('click mouseenter', () =>
  {
    if (focused)
    {
      return;
    }

    $el.tooltip('show');
    $target = $('#' + $el.attr('aria-describedby'));

    $target.on('mouseleave', () =>
    {
      $el.tooltip('hide');
    });
    $target.on('click', (e) =>
    {
      if (focused)
      {
        $el.focus();
      }
      e.stopPropagation();
    });
  });
  $el.on('mouseleave', () =>
  {
    if (focused)
    {
      return;
    }

    if ($target && !$target.is(':hover'))
    {
      $el.tooltip('hide');
    }
  });
  $el.on('click', (e) =>
  {
    focused = true;
    $target.off('mouseleave');
    e.stopPropagation();
  });

  $(document).on('click', () =>
  {
    if ($el.attr('aria-describedby'))
    {
      focused = false;
      $el.tooltip('hide');
    }
  });
}

export { manualTooltip };
