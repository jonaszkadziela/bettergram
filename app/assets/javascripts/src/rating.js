import $ from 'jquery';

class Star
{
  constructor(rating, $star)
  {
    this.rating = rating;
    this.$star = $star;
    this.id = this.rating.stars.length;
    this.selected = false;
    this.init();
  }

  init()
  {
    this.$star
    .on('mouseenter', () =>
    {
      this.rating.hover(this.id);
    })
    .on('mouseleave', () =>
    {
      this.rating.unhover();
    })
    .on('click', () =>
    {
      this.rating.click(this.id);
    });
  }

  select()
  {
    this.$star.find('.js-star-border').addClass('rating__star--selected');
    this.$star.find('.js-star-fill').addClass('rating__star--selected');
  }

  deselect()
  {
    this.$star.find('.js-star-border').removeClass('rating__star--selected');
    this.$star.find('.js-star-fill').removeClass('rating__star--selected');
  }

  hover()
  {
    this.$star.find('.js-star-border').addClass('rating__star--hovered');
  }

  unhover()
  {
    this.$star.find('.js-star-border').removeClass('rating__star--hovered');
  }
}

class Rating
{
  constructor($container, callback)
  {
    this.$container = $container;
    this.callback = callback;
    this.stars = [];
    this.rating = 0;
    this.status = '';
    this.init();
  }

  init()
  {
    const that = this;

    this.$container.find('.js-star').each(function()
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
    for (let i = 0; i <= star_id; i++)
    {
      this.stars[i].hover();
    }
  }

  unhover()
  {
    for (let i = 0; i < this.stars.length; i++)
    {
      this.stars[i].unhover();
    }
  }

  click(star_id)
  {
    let new_rating = star_id + 1;

    this.clearRating();
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
    for (let i = 0; i < rating; i++)
    {
      this.stars[i].select();
    }
  }

  clearRating()
  {
    for (let i = 0; i < this.stars.length; i++)
    {
      this.stars[i].deselect();
    }
  }
}

export default Rating;
