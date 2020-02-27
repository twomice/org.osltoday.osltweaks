(function ($, ts) {

  // Switch the sentences in cms_user_help-section so that user is prompted to
  // login first, then suggested to create a username.
  var html = $('div.cms_user_help-section').html().replace(ts('Please enter a Username to create an account.'), '');
  html += ' ' + ts('Otherwise, please enter a Username to create an account.');
  $('div.cms_user_help-section').html(html);

})(CRM.$, CRM.ts('com.joineryhq.profcond'));
