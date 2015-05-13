TYPO3 CMS Extension "news_importxmlics"
=======================================

What is it about
----------------
This extensions provides an import interface for ``xml`` and ``ics`` files which can either be on the same server or a URL.
The import is done by the scheduler.

Requirements
^^^^^^^^^^^^
- TYPO3 CMS 6.2 LTS
- EXT:news 3.2.0+

Add the following dependeny to your ``composer.json``: ::

    "fguillot/picofeed": "0.1.3",

Screenshots
^^^^^^^^^^^
TBD


Configuration
-------------
After installing the extension, switch to the module **scheduler** and create a new task **Import news**. These additional fields are now available:

- **Format**: Select *XML* or *ICS*
- **Path**: Define a local path like ``fileadmin/data.xml`` or any URL like ``http://typo3.org/xml-feeds/rss.xml``
- **Page ID**: Define a page id where the new records will be saved
- **Mapping**: TBD
- **Email notification**: Add an email address which will get notified after each run

About XML import
^^^^^^^^^^^^^^^^

About ICS import
^^^^^^^^^^^^^^^^