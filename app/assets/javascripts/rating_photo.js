$(document).ready(function()
{
  var photo_rating = $('#photo_rating');
  var timeout;

  new Rating(photo_rating, function()
  {
    var form = this.container.find('form');
    var photo_id = form.find('input[name="photo_id"]').val();
    var user_id = form.find('input[name="user_id"]').val();
    var type = form.attr('method');
    var url = form.attr('action');
    var data, response;

    switch (this.status)
    {
      case 'create':
      case 'update':
        data =
        {
          photo_id: photo_id,
          user_id: user_id,
          rating: this.rating
        };
      break;

      case 'destroy':
        data =
        {
          photo_id: photo_id,
          user_id: user_id
        };
      break;

      default:
        return;
    }

    url = url + this.status + '.php';
    var that = this;
    var rating_title = this.container.find('.js-rating-title');
    var rating_response = this.container.find('.js-rating-response');
    var rating_result = this.container.siblings('.js-rating-result');

    $.ajax(
    {
      type: type,
      url: url,
      data: data,
      dataType: 'json'
    })
    .done(function(data)
    {
      response = '<p class="text-success mt-0-5 mb-0">' + data.responseText + '</p>';
      rating_response.html(response);
      rating_result.html(data.ratingResult);
      that.old_rating = that.rating;

      switch (this.status)
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
      response = '<p class="text-danger mt-0-5 mb-0">' + data.responseText + '</p>';
      rating_response.html(response);
      rating_result.html(data.ratingResult);
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
