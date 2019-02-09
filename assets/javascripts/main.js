function getBreakpoint()
{
  var w = $(document).innerWidth();
  return (w < 768) ? "xs" : ((w < 992) ? "sm" : ((w < 1200) ? "md" : "lg"));
}
