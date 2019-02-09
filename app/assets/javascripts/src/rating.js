import $ from 'jquery';

class Star
{
  constructor(rating, star)
  {
    this.rating = rating;
    this.star = star;
    this.id = this.rating.stars.length;
    this.selected = false;
    this.init();
  }

  init()
  {
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
  }

  select()
  {
    this.star.find('.js-star-border').addClass('rating__star--selected');
    this.star.find('.js-star-fill').addClass('rating__star--selected');
  }

  deselect()
  {
    this.star.find('.js-star-border').removeClass('rating__star--selected');
    this.star.find('.js-star-fill').removeClass('rating__star--selected');
  }

  hover()
  {
    this.star.find('.js-star-border').addClass('rating__star--hovered');
  }

  unhover()
  {
    this.star.find('.js-star-border').removeClass('rating__star--hovered');
  }
}

class Rating
{
  constructor(container, callback)
  {
    this.container = container;
    this.callback = callback;
    this.stars = [];
    this.rating = 0;
    this.status = '';
    this.init();
  }

  init()
  {
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
  }

  hover(star_id)
  {
    for (var i = 0; i <= star_id; i++)
    {
      this.stars[i].hover();
    }
  }

  unhover()
  {
    for (var i = 0; i < this.stars.length; i++)
    {
      this.stars[i].unhover();
    }
  }

  click(star_id)
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

  setRating(rating)
  {
    for (var i = 0; i < rating; i++)
    {
      this.stars[i].select();
    }
  }

  clearRating()
  {
    for (var i = 0; i < this.stars.length; i++)
    {
      this.stars[i].deselect();
    }
  }
}

export default Rating;
