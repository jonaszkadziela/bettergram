var disable_submit_button = false;

default_popover = {
  enabled: false,
	trigger: "manual",
	placement: "left",
	title: "Nieprawidłowe dane!",
	content: "Opis błędu.",
  template:
  '<div class="popover" role="tooltip">' +
    '<div class="arrow"></div>' +
      '<h3 class="popover-header text-white bg-danger"></h3>' +
    '<div class="popover-body"></div>' +
  '</div>'
};

$(document).ready(function()
{
  if (!disable_submit_button)
  {
    $('button[type="submit"]').each(function()
    {
      $(this).prop("disabled", false);
    });
  }

  responsivePopover(default_popover);

  $(window).on("resize", function()
  {
    responsivePopover(default_popover);
  });
});

function responsivePopover(popover)
{
  if (getBreakpoint() == "xs" || getBreakpoint() == "sm")
  {
    popover.placement = "top";
  }
  else
  {
    popover.placement = "left";
  }
}

function validate(field, regex)
{
  if (!regex.test(field.val()))
  {
    return false;
  }
  return true;
}

function showPopover(element, popover)
{
  if (!popover.enabled)
  {
    element.popover(popover);
    element.popover("show");
    popover.enabled = true;
  }
}

function hidePopover(element, popover)
{
  if (popover.enabled)
  {
    element.popover("dispose");
    popover.enabled = false;
  }
}

function checkSubmitButton(button, tests)
{
  for (var i = 0; i < tests.length; i++)
  {
    if (!tests[i])
    {
      button.prop("disabled", true);
      return;
    }
  }
  button.prop("disabled", false);
}
