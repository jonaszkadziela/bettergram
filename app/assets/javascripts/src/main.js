import $ from 'jquery';
import loadAnalytics from './analytics';
import prepareRecaptcha from './recaptcha';
import expandTextarea from './expand_textarea';
import lazyLoadImage from './lazy_load_image';
import { photoLightGallery } from './lightgalleries';
import { confirmationModal } from './modals';
import { manualTooltip } from './tooltips';
import 'bootstrap';
import './modernizr';

var analytics = null;

$(() =>
{
  analytics = loadAnalytics();

  // Prevent Bootstrap dropdowns from closing, when the user clicks anywhere within the dropdown-menu
  $(document).on('click', '.dropdown-menu', (e) =>
  {
    e.stopPropagation();
  });

  if (RECAPTCHA_ENABLED)
  {
    $('form').each(function()
    {
      const $form = $(this);

      $(this).on('submit', (e) =>
      {
        if (!$form.find('[name="recaptcha"]').val())
        {
          e.preventDefault();
          prepareRecaptcha($form)
          .then(() =>
          {
            $form.submit();
          })
          .catch((error) =>
          {
            console.error(error);
          });
        }
      });
    });
  }

  $('img[data-src]').each(function()
  {
    lazyLoadImage($(this));
  });

  $('[data-toggle="tooltip"]').each(function()
  {
    const trigger = $(this).attr('data-trigger');

    $(this).tooltip();
    switch (trigger)
    {
      case 'manual':
        manualTooltip($(this));
      break;
    };
  });

  $('.js-expand-textarea').each(function()
  {
    expandTextarea(this);
    $(this).on('input', () =>
    {
      expandTextarea(this);
    });
  });

  $('.js-photo-lightgallery').each(function()
  {
    photoLightGallery($(this));
  });

  $('.js-confirmation-modal').each(function()
  {
    confirmationModal($(this));
  });

  $('.js-prevent-default').each(function()
  {
    $(this).on('click', (e) =>
    {
      e.preventDefault();
    });
  });

  $('.js-truncate-button').each(function()
  {
    $(this).on('click', () =>
    {
      const container = $(this).closest('.js-truncate');

      container.find('.js-truncate-show-less').collapse('toggle');
      container.find('.js-truncate-show-more').collapse('toggle');
      $(this).blur();
    });
  });

  $('.js-swap-text').each(function()
  {
    $(this).on('click', () =>
    {
      const initial_text = $(this).html();
      const swap_text = $(this).attr('data-swap-text');

      $(this).html(swap_text);
      $(this).attr('data-swap-text', initial_text);
    });
  });

  $('.js-select-links').each(function()
  {
    $(this).on('change', () =>
    {
      window.location.replace($(this).val());
    });
  });

  $('.js-file-input').each(function()
  {
    $(this).on('change', () =>
    {
      let placeholder = $(this).attr('data-placeholder');

      if (this.files[0])
      {
        placeholder = this.files[0].name;
      }
      $(this).next('.custom-file-label').html(placeholder);
    });
  });
});

export { analytics };
