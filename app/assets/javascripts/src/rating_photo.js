import $ from 'jquery';
import Rating from './rating';

$(document).ready(function()
{
  var photo_rating = $('#photo_rating');
  var timeout;

  new Rating(photo_rating, function()
  {
    var form = this.container.find('form');
    var type = form.attr('method');
    var url = form.attr('action');
    var data =
    {
      rating: this.rating
    }
    var response;

    url = url + this.status + '.php';
    var that = this;
    var rating_title = this.container.find('.js-rating-title');
    var rating_response = this.container.find('.js-rating-response');
    var rating_result = this.container.siblings('.js-rating-result');

    $.ajax(
    {
      type: type,
      url: url,
      data: form.serialize() + '&' + $.param(data),
      dataType: 'json'
    })
    .done(function(data)
    {
      response = '<div class="text-success mt-0-5 mb-0">' + data.responseText + '</div>';
      rating_response.html(response);
      rating_result.html(data.ratingResult);
      that.old_rating = that.rating;

      switch (that.status)
      {
        case 'create':
        case 'update':
          rating_title.html('Twoja ocena');
        break;

        case 'destroy':
          rating_title.html('Oceń to zdjęcie');
        break;
      }
    })
    .fail(function(data)
    {
      response = '<div class="text-danger mt-0-5 mb-0">' + data.responseText + '</div>';
      rating_response.html(response);
      that.clearRating();
      that.setRating(that.old_rating);
      that.rating = that.old_rating;
    })
    .always(function()
    {
      clearTimeout(timeout);
      timeout = setTimeout(function()
      {
        rating_response.fadeOut('slow', function()
        {
          rating_response.html('');
          rating_response.show();
        });
      }, 3000);
    });
  });
});
