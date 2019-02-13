import $ from 'jquery';

function lazyLoadImage($el)
{
  const data_src = $el.data('src');

  $el.on('load', () =>
  {
    $el.closest('.js-spinner-container').find('.js-spinner').fadeOut('slow', function()
    {
      $(this).remove();
    });

    if (!Modernizr.objectfit)
    {
      let classes = $el.prop('classList');

      if ($el.hasClass('object-fit-cover'))
      {
        classes.remove('object-fit-cover');
        $el.replaceWith(
          '<div class="' + classes + ' bg-size-cover bg-position-center bg-repeat-no-repeat"' +
          'style="background: url(\'' + data_src + '\')"></div>'
        );
      }
    }
  }).attr('src', data_src);

  $el.removeAttr('data-src');
}

export default lazyLoadImage;
