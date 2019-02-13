import 'lightgallery';
import 'lg-zoom';
import 'lg-fullscreen';

const PHOTO_LIGHTGALLERY_OPTIONS =
{
  preload: 0,
  hideBarsDelay: 3000,
  counter: false,
  actualSize: false,
  getCaptionFromTitleOrAlt: false,
  subHtmlSelectorRelative: true
};

function photoLightGallery($el)
{
  $el.lightGallery(PHOTO_LIGHTGALLERY_OPTIONS);
}

export { photoLightGallery };
