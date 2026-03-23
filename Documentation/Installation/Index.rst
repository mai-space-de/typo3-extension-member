.. _installation:

============
Installation
============

Requirements
============

* TYPO3 13.4 LTS
* PHP 8.2 or later
* No additional PHP extensions required

Composer installation
=====================

.. code-block:: bash

   composer require maispace/mai-member

TYPO3 will automatically discover the extension. No manual activation is
required.

TypoScript inclusion
====================

Include the extension's TypoScript in your site package's setup file:

.. code-block:: typoscript

   @import 'EXT:mai_member/Configuration/TypoScript/setup.typoscript'

Database update
===============

After installation run the TYPO3 Database Analyser in the Admin Tools to
create the tables the extension requires:

* ``tx_mai_member_domain_model_member`` — member records
* ``tx_mai_member_domain_model_memberapplication`` — application records

No further configuration is required for basic operation.

First steps
===========

1. Navigate to the TYPO3 backend list module and select a sysfolder.
2. Create a new **Member** record via the **New record** button.
3. To use the frontend application form, add the **Member Application**
   plugin to a page content element.
