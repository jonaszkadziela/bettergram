function Star(rating, star)
{
  this.rating = rating;
  this.star = star;
  this.id = this.rating.stars.length;
  this.selected = false;

  var that = this;
  this.star
  .on('mouseenter', function()
  {
    that.rating.hover(that.id);
  })
  .on('mouseleave', function()
  {
    that.rating.unhover();
  })
  .on('click', function()
  {
    that.rating.click(that.id);
  });

  this.select = function()
  {
    this.star.find('.js-star-border').addClass('rating__star--selected');
    this.star.find('.js-star-fill').addClass('rating__star--selected');
  }

  this.deselect = function()
  {
    this.star.find('.js-star-border').removeClass('rating__star--selected');
    this.star.find('.js-star-fill').removeClass('rating__star--selected');
  }

  this.hover = function()
  {
    this.star.find('.js-star-border').addClass('rating__star--hovered');
  }

  this.unhover = function()
  {
    this.star.find('.js-star-border').removeClass('rating__star--hovered');
  }
}

function Rating(container, callback)
{
  this.container = container;
  this.callback = callback;
  this.stars = [];
  this.rating = 0;
  this.status = '';

  var that = this;
  this.container.find('.js-star').each(function()
  {
    that.stars.push
    (
      new Star(that, $(this))
    );
    if ($(this).attr('data-star-selected'))
    {
      that.rating++;
    }
  });

  this.old_rating = this.rating;

  this.hover = function(star_id)
  {
    for (var i = 0; i <= star_id; i++)
    {
      this.stars[i].hover();
    }
  }

  this.unhover = function()
  {
    for (var i = 0; i < this.stars.length; i++)
    {
      this.stars[i].unhover();
    }
  }

  this.click = function(star_id)
  {
    this.clearRating();

    var new_rating = star_id + 1;

    if (new_rating != this.rating)
    {
      this.setRating(new_rating);
      this.status = this.rating == 0 ? 'create' : 'update';
    }
    else
    {
      new_rating = 0;
      this.status = 'destroy';
    }

    this.rating = new_rating;

    if (typeof this.callback === 'function')
    {
      this.callback();
    }
  }

  this.setRating = function(rating)
  {
    for (var i = 0; i < rating; i++)
    {
      this.stars[i].select();
    }
  }

  this.clearRating = function()
  {
    for (var i = 0; i < this.stars.length; i++)
    {
      this.stars[i].deselect();
    }
  }
}
