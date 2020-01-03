CONTENTS OF THIS FILE
---------------------

 * Introduction
 * Requirements
 * Recommended modules
 * Installation
 * Configuration
 * Usage
 * Maintainers


INTRODUCTION
------------

Relations Audit allows developers and maintainers of Drupal websites to gauge
relationships between entities and entity structure. Currently, only nodes and
paragraphs are supported. This kind of tool can be useful for component-driven
development.

This module provides a real-time readout of which paragraphs are available as
children to which content types, as well as of where examples of paragraph types
exist on the site.


REQUIREMENTS
------------

No special requirements.


RECOMMENDED MODULES
-------------------

No recommended modules.


INSTALLATION
------------

 * Install as you would normally install a contributed Drupal module.
   See: https://www.drupal.org/node/895232 for further information.


CONFIGURATION
-------------

No configuration for this module.


USAGE
-----

Either make use of the APIs directly, download or export a CSV, execute a drush
command, or automatically express relations structure in Google Sheets. The
following methods will provide data as to which paragraph types are available to
which content types.

 * 1. Make use of the APIs directly.
   Calling /api/structure-relations will return a JSON object. This requires
   basic authentication as a user with the "Access relations audit API"
   permission. For example (see below for parameter information):
   `http://example.com/api/structure-relations?base_entity_type=node&target_entity_type=paragraph`
 * 2. Download or export a CSV.
   Go to /admin/config/relations-audit and click "Download Relations Structure
   CSV". Or you can execute the drush command `drush
   export-relations-structure`.
 * 3. Automatically generate Google Sheets with relations structure.
   Create a new Google Sheet. Click "Tools" -> "Code Editor". Copy & paste the
   contents of `/gs/GoogleScript.js` into a new ".gs" Google Script file. Then
   copy & paste the contents of `/gs/sidebarForm.html` into a new Google HTML
   file, making sure to name it "sidebarForm". Then, in the Google Sheet, click
   "Component Audit" -> "Execute Audit". A dialog form will prompt for the
   website the module is installed on, and username & password of a user with
   the "Access relations audit API" permission.

 *** Note: For both #1 & #2, the following parameters are available (query
 parameters in #1, drush parameters in #2):
   * base_entity_type
   * target_entity_type
   * base_bundle
   * target_bundle

MAINTAINERS
-----------

Current maintainers:
 * Charlene Uban - https://www.o3world.com/about/team/charlene-uban
 * Matt Schaff (gwolfman) - https://www.drupal.org/u/gwolfman

This project has been sponsored by:
 * O3 World
   O3 World is a digital agency in Philadelphia that builds great products,
   transforms digital experiences, and takes your most innovative ideas to
   market.
