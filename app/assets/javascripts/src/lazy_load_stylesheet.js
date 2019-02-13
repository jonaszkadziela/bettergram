import $ from 'jquery';

var lazy_loaded_stylesheets = [];

function lazyLoadStylesheet(url)
{
  if (lazy_loaded_stylesheets.indexOf(url) !== -1)
  {
    return;
  }

  lazy_loaded_stylesheets.push(url);
  $('head').append('<link rel="stylesheet" href="' + url + '">');
}

export { lazy_loaded_stylesheets, lazyLoadStylesheet };
