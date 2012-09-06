Gitonomy
========

Configuration & Installation
----------------------------

This application relies on:

* PHP 5.3 (Symfony2 powered)
* MySQL database
* Git

This project is still a work in progress.

System configuration
--------------------

The command ``php app/console gitonomy:authorized-keys -i`` will regenerate the
file with user SSH keys.

Features
--------

* Packaged application
  * Web installation
* Git repository
  * Full web administration
  * Accessible by SSH
  * Private repositories
  * Role based permissions
  * Manage Git accesses to a repository (write/force/delete)
* Browser repository
  * Browse files & folders
* Browse history
  * Graph view of log
  * Log view
* Security
  * Manage roles, users and projects
  * Disable registration
* Account
  * Forgot password
  * Change username
  * Manage SSH keys
  * Manage e-mails
