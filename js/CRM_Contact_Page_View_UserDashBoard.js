CRM.$(function($) {
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
  });

  // Change renew button link to match the users current membership type
  $('.form-item .secondary-navigation').each(function(){
    // Get the membership type
    var memberShipType = $(this).parent('td').parent('tr').find('td:first-child').html();
    var newHref;

    if(
      memberShipType === 'New Discerning Member - 1 year (CANADA)' || 
      memberShipType === 'New Discerning Couple Member - 1 year (CANADA)' || 
      memberShipType === 'Individual Member - 1 year (Canada Renewal)' || 
      memberShipType === 'Couple Member - 1 year (Canada Renewal)'
    ) {
      newHref = $('a', this).attr('href').replace(/\bid=10\b/g, 'id=14');
      $('a', this).attr('href', newHref);
    } else if(
      memberShipType === 'New Discerning Member - 1 year (INTERNATIONAL)' || 
      memberShipType === 'New Discerning Couple Member - 1 year (INTERNATIONAL)' || 
      memberShipType === 'Individual Member - 1 year (International Renewal)' || 
      memberShipType === 'Couple Member - 1 year (International Renewal)'
    ) {
      newHref = $('a', this).attr('href').replace(/\bid=10\b/g, 'id=15');
      $('a', this).attr('href', newHref);
    }
  });
});