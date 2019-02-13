import $ from 'jquery';
import { load } from 'recaptcha-v3';

const RECAPTCHA_OPTIONS =
{
  useRecaptchaNet: false,
  autoHideBadge: true
};

async function prepareRecaptcha($form)
{
  const recaptcha = await load(RECAPTCHA_SITE_KEY, RECAPTCHA_OPTIONS);
  const token = await recaptcha.execute(CURRENT_PAGE);

  if (!$form.find('[name="recaptcha"]').length)
  {
    $form.prepend(
      $('<input>',
      {
        type: 'hidden',
        name: 'recaptcha',
        value: token
      })
    );
  }
  else
  {
    $form.find('[name="recaptcha"]').val(token);
  }
}

export default prepareRecaptcha;
