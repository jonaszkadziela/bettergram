import $ from 'jquery';

function getBreakpoint()
{
  const w = $(document).innerWidth();
  return w < 544 ? 'xs' : (w < 768 ? 'sm' : (w < 992 ? 'md' : (w < 1200 ? 'lg' : 'xl')));
}

export default getBreakpoint;
