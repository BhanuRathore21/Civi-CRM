# Archive Mailing

When mailings are archived, compile stats and tidy up the database details. Also provides a Scheduled Job to automatically archive mailings after a certain number of days (disabled by default).

Statistics for mailings are kept on a per-mailing basis, but the per-contact statistics are deleted.

Aims to improve performance by cleaning up database tables that can become very large after a certain amount of time, while still retaining some statistics about mailings sent.

The extension is licensed under [AGPL-3.0](LICENSE.txt).

## Requirements

* PHP v7.2+
* CiviCRM 5.44 or later

## Installation

Install as a regular CiviCRM extension.

## Getting Started

After enabling the extension, you may want to enable the Scheduled Job (under Administration > System Settings)
so that mailings are automatically archived after a certain number of days.

Completed mailings can also be manually archived from the CiviMail screen (Scheduled and Sent Mailings).
Click on the "more" menu associated with a mailing, then click "archive". This setting previously did not
really do anything, except set a "is archive = yes" flag in the database, which was mostly symbolic.

## References

Previous discussion on this topic:

* https://civicrm.stackexchange.com/questions/28483/best-way-to-cleanup-civimail-database-tables
* https://forum.civicrm.org/index.php%3Ftopic=6079.0.html

## Known issues

* Before CiviCRM 5.48, mailing recipients of archived mailings are recalculated when we view the Mailing Report. Patch: https://github.com/civicrm/civicrm-core/pull/22800
* The Scheduled Job runs "OPTIMIZE TABLE" on the main/larger mailing database tables, in order to reclaim the disk space from the indexes. This operation can take a few minutes on very large installs. It was tested on a server with 4 GB RAM and where the tables were initially around 500 MB (around 4 M rows), and it took 45-60 seconds in total to run.

## TODO

* When a mailing is archived in the interface, archive the stats.
* Delete CiviCRM activities related to the mailing? (or mailing activities older than X days)
