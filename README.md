# org.osltoday.osltweaks

This extension makes specific style/layout changes to CiviCRM content for OSL.

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
