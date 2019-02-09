import $ from 'jquery';
import './modernizr';
import 'bootstrap';

var lazy_loaded_stylesheets = [];

function getBreakpoint()
{
  var w = $(document).innerWidth();
  return w < 544 ? 'xs' : (w < 768 ? 'sm' : (w < 992 ? 'md' : (w < 1200 ? 'lg' : 'xl')));
}

function swapText(el, text1, text2)
{
  el.html() == text1 ? el.html(text2) : el.html(text1);
}

function lazyLoadStylesheet(url)
{
  if (lazy_loaded_stylesheets.indexOf(url) !== -1)
  {
    return;
  }

  lazy_loaded_stylesheets.push(url);
  $('head').append('<link rel="stylesheet" href="' + url + '">');
}

$(document).ready(function()
{
  $('[data-toggle="tooltip"]').each(function()
  {
    $(this).tooltip();
  });

  // Prevent Bootstrap dropdowns from closing, when the user clicks anywhere within the dropdown-menu
  $(document).on('click', '.dropdown-menu', function(e)
  {
    e.stopPropagation();
  });

  $('.js-stop-propagation').each(function()
  {
    $(this).on('click', function(e)
    {
      e.preventDefault();
      e.stopPropagation();
    });
  });

  $('.js-select-links').each(function()
  {
    $(this).on('change', function()
    {
      if ($(this).val())
      {
        window.location.replace($(this).val());
      }
    });
  });

  $('.js-file-input').each(function()
  {
    $(this).on('change', function()
    {
      var placeholder = $(this).attr('data-placeholder');
      if ($(this)[0].files[0])
      {
        placeholder = $(this)[0].files[0].name;
      }
      $(this).next('.custom-file-label').html(placeholder);
    });
  });

  $('img[data-src]').each(function()
  {
    var data_src = $(this).data('src');

    $(this).on('load', function()
    {
      $(this).parents('.card').find('.js-spinner').fadeOut('slow', function()
      {
        $(this).remove();
      });
      if (!Modernizr.objectfit)
      {
        var classes = $(this).prop('classList');

        if ($(this).hasClass('object-fit-cover'))
        {
          classes.remove('object-fit-cover');
          $(this).replaceWith(
            '<div class="' + classes + ' bg-size-cover bg-position-center bg-repeat-no-repeat"'+
            'style="background: url(\'' + data_src + '\')"></div>'
          );
        }
      }
    }).attr('src', data_src);

    $(this).removeAttr('data-src');
  });
});

export { getBreakpoint, swapText, lazyLoadStylesheet };
