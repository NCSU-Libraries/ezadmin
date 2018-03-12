# EZAdmin

## Installation
To install the EZadmin application, you will need:

1. A place to host web-accessible PHP code
2. A MySQL database

### PHP code

For the application to run, it must be deployed to a folder on a web-accessible server that can run PHP. EZadmin does not currently have authentication built into the application, so it is recommended that this folder be restricted to appropriate staff users using Apache configuration.

Please note that even when this is done, the database connection information in `/application/config/config.xml` could be accessible via http to hackers, and you may want to consider doing one of the following:
 1. moving the whole application folder somewhere outside of web server's document root (recommended)
 2. allowing no web access to the application folder (via apache configuration)

See the instructions in `/sample_localize.php` in order to specify where you have installed the application files if you have followed our advice above and moved the `/application/` folder outside the web server's document root.

To customize the links in the footer of the application, change the values in `/application/config/config.xml`.


### MySQL database

A MySQL database should be created with an account that allows localhost access. The database name (schema name) does not matter. To create the tables necessary for EZadmin, you can run the `/application/sql/schema.sql` SQL script. Please note that you will need to put your database name at the top of that file (replace `<dbname>`) for it to work properly. A database with that name (schema name) must already exist.

You will need to configure your database connection information in `/application/config/config.xml`.

If you would like to populate the database with a little sample data, you can load the `/application/sql/test_data.sql` script. As with the `schema.sql` script you'll need to replace `<dbname>` with your database name.

### EZProxy Configuration Files:

EZadmin's purpose is to store EZProxy configuration information in a database and then write it out to configuration files that can be read by EZProxy. The location where these files should be written should be configured in `/application/config/config.xml`. In order for these configuration files to affect EZProxy, they must be stored somewhere accessible to EZProxy, and then EZProxy must be restarted to read in any updates to the configuration.

One way to achieve this is to set the following in `config.xml`:
* `<outputPath>` should be set to a writable directory in the filesystem server where the configuration files will be written.
* set your `<uploadTarget>` to be a valid target rsync target EZProxy server

## Upgrading from a previous version

Users who are currently running versions 1.2.0 or older will need to run the upgrade scripts in the sql folder in order.
Example: If you're currently running version 1.1.0, then you will need to run the `1.2.0_upgrade.sql` then the `1.3.0_upgrade.sql`.
