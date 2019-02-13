import $ from 'jquery';

function expandTextarea(el)
{
  if (!$(el).attr('data-expand-textarea'))
  {
    const initial_value = el.value;

    el.value = '';
    el.baseScrollHeight = el.scrollHeight;
    el.value = initial_value;
    $(el).attr('data-expand-textarea', true);
  }

  const min_rows = $(el).attr('data-min-rows') | 0;
  const line_height = parseInt($(el).css('line-height'));

  el.rows = min_rows;
  let rows = Math.ceil((el.scrollHeight - el.baseScrollHeight) / line_height) + min_rows;
  el.rows = rows;
}

export default expandTextarea;
