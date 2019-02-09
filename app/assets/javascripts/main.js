var loaded_scripts = [];

function getBreakpoint()
{
  var w = $(document).innerWidth();
  return w < 544 ? 'xs' : (w < 768 ? 'sm' : (w < 992 ? 'md' : (w < 1200 ? 'lg' : 'xl')));
}

function swapText(el, text1, text2)
{
  el.html() == text1 ? el.html(text2) : el.html(text1);
}

function loadScript(url, check_loaded_scripts)
{
  check_loaded_scripts = check_loaded_scripts || true;
  if (check_loaded_scripts && loaded_scripts.indexOf(url) !== -1)
  {
    return;
  }
  loaded_scripts.push(url);

  $.ajax(
  {
    url: url,
    dataType: 'script',
    cache: ENVIRONMENT == 'production' ? true : false,
  })
  .fail(function()
  {
    console.log('An error occured while loading the script: ' + url + '!');
    var index = loaded_scripts.indexOf(url);
    if (index !== -1)
    {
      loaded_scripts.splice(index, 1);
    }
  });
}

$(document).ready(function()
{
  $('[data-toggle="tooltip"]').each(function()
  {
    $(this).tooltip();
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
      $(this).parent().find('.js-spinner').fadeOut('slow', function()
      {
        $(this).remove();
      });
    }).attr('src', data_src);
    $(this).removeAttr('data-src');
  });
});
