import $ from 'jquery';
import Rating from './rating';
import prepareRecaptcha from './recaptcha';

var timeout = null;

function ajaxRating(rating)
{
  const $form = rating.$container.find('form');
  const $rating_title = rating.$container.find('.js-rating-title');
  const $rating_response = rating.$container.find('.js-rating-response');
  const $rating_result = rating.$container.siblings('.js-rating-result');
  const type = $form.attr('method');
  const url = $form.attr('action');
  const data =
  {
    rating: rating.rating
  };
  var response;

  $.ajax(
  {
    type: type,
    url: url + rating.status + '.php',
    data: $form.serialize() + '&' + $.param(data),
    dataType: 'json'
  })
  .done((data) =>
  {
    response = '<div class="text-success mt-0-5">' + data.responseText + '</div>';
    $rating_response.html(response);
    $rating_result.html(data.ratingResult);
    rating.old_rating = rating.rating;

    switch (rating.status)
    {
      case 'create':
      case 'update':
        $rating_title.html('Twoja ocena');
      break;

      case 'destroy':
        $rating_title.html('Oceń to zdjęcie');
      break;
    }
  })
  .fail((data) =>
  {
    response = '<div class="text-danger mt-0-5">' + data.responseText + '</div>';
    $rating_response.html(response);
    rating.clearRating();
    rating.setRating(rating.old_rating);
    rating.rating = rating.old_rating;
  })
  .always(() =>
  {
    if (timeout == null)
    {
      timeout = setTimeout(() =>
      {
        $rating_response.fadeOut('slow', () =>
        {
          $rating_response.html('');
          $rating_response.show();
        });
      }, 3000);
    }
  });
}

$(() =>
{
  const photo_rating = $('#photo_rating');

  new Rating(photo_rating, function()
  {
    const rating = this;
    const $rating_response = rating.$container.find('.js-rating-response');

    $rating_response.html('<div class="mt-0-5">Przetwarzanie <i class="fas fa-sync-alt fa-spin ml-0-5"></i></div>');
    clearTimeout(timeout);
    timeout = null;

    if (RECAPTCHA_ENABLED)
    {
      const $form = rating.$container.find('form');

      prepareRecaptcha($form)
      .then(() =>
      {
        ajaxRating(rating);
      })
      .catch((error) =>
      {
        console.error(error);
      });
      return;
    }
    ajaxRating(rating);
  });
});
