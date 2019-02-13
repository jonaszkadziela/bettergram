import Analytics from 'ganalytics';

function loadAnalytics()
{
  if (ANALYTICS_ENABLED)
  {
    return new Analytics(ANALYTICS_TRACKING_ID);
  }
  return null;
};

export default loadAnalytics;
