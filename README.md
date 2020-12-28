# Software for evaluating the status of Buderus Logamatic 2107

## General
The software in this repository can read the status information from the control unit Buderus Logamatic 2107 for Buderus central heating units. It is mainly written in Python and stores the status data obtained into one or more databases. Currently, this repository does not include any software for a user interface and/or further processing of the status data.

## Credits
The software in this repository is based on the work of the user "Black" in the HomeMatic-Forum: https://homematic-forum.de/forum/viewtopic.php?f=18&t=26955 (German only). Some parts are also described in the user's personal blog: http://foto-paintings.de/index.php/hausautomatisierung/12-heizung (German only).
The description of the data telegrams sent by the Buderus Logamatic 2107 is included in another GitHub repository: https://github.com/sjs-77/logamatic2107_daten (currently German only)

## License
The software included in this repository is licensed under Creative Commons CC BY-SA 3.0. This does not include software from third parties like mentioned in the "Dependencies" section.
The author of this software does not assume any liability whatsoever for any damage caused by the software, e.g. to Logamatic units which are monitored by this software.

## Dependencies
* The main part of the software is written in Python 3, so you need a suitable Python interpreter
* For the python code, you need the class "stepchain" (available here: https://homematic-forum.de/forum/viewtopic.php?p=255061#p255061) and the class "Dust3964r" (available here: http://foto-paintings.de/index.php/hausautomatisierung/12-heizung/16-test)
* Some elements of the software are written in PHP, so you need a PHP 7 or higher
* The data retrieved from the Logamatic 2107 unit is stored into two MySQL or MariaDB databases, so you have to have a suitable database system running.

## Installation
This software was developed on a Raspberry Pi running on Raspberry Pi OS (formerly known as Raspbian) with a serial interface, but any hardware with a serial interface that is able to run Python 3, PHP 7 and a MySQL or MariaDB database (or has a network interface to connect to a MySQL/MariaDB server) should be suitable.
The main script buderus.py is intended to be run as a daemon via a systemd unit. If systemd is not available on your system of choice, you will have to figure out a way to run the script in a suitable way yourself.
### Hardware
In order to provide a serial interface, the Buderus Logamatic 2107 has to be equipped with a Buderus KM271 communication add-on module. The connection from the KM271 module to the serial port of the Raspberry Pi or similar can be made by a RS232 extension cable with a 1:1 pinout.
### Software
* Copy all components of the software in this repository on your system
* Import the SQL data structures via the provided .sql files into your database system
* Make the script buderus.py executable
* Adjust the serial device and the database access credentials in logamatic.py as needed
* Run the script via the provided systemd unit
* If desired, set up a scheduled task to run put_to_archive.php in regular intervals to transfer the current status to a long-term archive. For 1 minute intervall, this can for example be achieved by the following cron job: ``` * *     * * *   root    php /path/to/script/put_to_archive.sh ```

