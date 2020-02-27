(function ($, ts) {
  // Move 'memberships' above 'contributions'.
  $('tr.crm-dashboard-civimember').insertBefore($('tr.crm-dashboard-civicontribute'));
  
  // Alter the membership 'renew' links so they look like buttons.
  // This leverages WP theme styles, including a class ('menu-toggle') which uses
  // the :before pseudo-class to prepend an icon. See css/CRM_Contact_Page_View_UserDashBoard.css
  // for styles that specifically omit that :before content.
  $('tr.crm-dashboard-civimember a').wrap( "<div class='secondary-navigation'></div>" );
  $('tr.crm-dashboard-civimember a').addClass('darkButton menu-toggle');
  $('tr.crm-dashboard-civimember a').css({
    'color': 'white',
    'white-space': 'nowrap',
    'font-weight': 'bold',
  });
  $('tr.crm-dashboard-civimember a').each(function(idx, el){
    var html = $(el).html().replace(/[\[\]]/g , '');
    $(el).html(html);
  })
  
})(CRM.$, CRM.ts('com.joineryhq.profcond'));
