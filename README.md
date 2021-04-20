# org.osltoday.osltweaks

This extension makes specific style and functionality changes to CiviCRM content for OSL.

* On the user dashboard, position Memberships above Contributions.
* On the user dashboard, present membership renewal links as buttons.
* On contribution pages where users would see a cms-user-help section, the sentences are re-ordered in that section section, so that user is prompted to login first, then suggested to create a username.
* On contributions pages which are specified as "US-only" (see below), non-US countries are removed from the Country field, and the "State/Province" field label is changed to "State".

## Specifying contribution pages as "US-only"
This configuration is to be made in civicrm.settings.php, as there is no UI for this at present. To specify one or more configuration pages as "US-only", add code like the following to civicrm.settings.php:

```
$civicrm_setting['com.joineryhq.osltweaks']['com.joineryhq.osltweaks']['us_only_page_ids'] = array(
  N, // Any integer representing the ID of a contribution page.
  ... // Any number of additional such integers
);
```

For example:
```
$civicrm_setting['com.joineryhq.osltweaks']['com.joineryhq.osltweaks']['us_only_page_ids'] = array(
  10,
  2,
  8,
  22,
  21
);
```

## Specifying contribution pages as membership renewal alternatives to the original contribution page.

When properly configured, this feature will cause a logged-in user to be redirected to a specific contribution page "X", if they a) have a membership record (of any status or type), and b) attempt to access a specific contribution page "Y".

For example, a user with an existing membership record will often find on their CiviCRM user dashboard a link to "Renew Membership" at the contribution page at which they originally signed up (e.g., contribution page id=1); this feature allows the site admin to configure an alternative contribution page for renewal, so that this user is redirected to that page (e.g. contribution page id=10).

This configuration is to be made in civicrm.settings.php, as there is no UI for this at present. To specify the configuration for this feature, add code like the following to civicrm.settings.php:

```
$civicrm_setting['com.joineryhq.osltweaks']['com.joineryhq.osltweaks']['member_redirect_contribution_pages'] = array(
  Y => X, // When a user with an existing membership record attempts to access contribution page id=Y, 
          // redirect them to contribution page id=X.
);
```

For example:
```
$civicrm_setting['com.joineryhq.osltweaks']['com.joineryhq.osltweaks']['member_redirect_contribution_pages'] = array(
  1 => 10,
  2 => 11,
  22 => 11, // Notice that both 2 and 22 are redirected to 11.
);
```
