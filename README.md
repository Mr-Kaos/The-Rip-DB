# The Rip Database Project

This project aims to provide an easy way to catalog and find "[rips](#what-are-rips)" uploaded by all "ripping" channels on YouTube.

## Aim of the Project

The goal of this project is to provide a complete and detailed database of all rips that exist publicly on the internet. Each rip will be given appropriate tags and attributes, to help fid rips by their contents, such as the songs used, jokes and references within the rip or other meta information related to the rip.

Ultimately, if someone wanted to find rips that contain certain songs or references, they would be able to do it here.

Some examples of what the database should be able to do:

- If someone wanted to find all rips that contain a song by "Daft Punk", they should be able to search for that.  
- If someone wanted to find all rips that are from the video game "Undertale", the database should contain the data to do this.
- If someone wanted to find all rips that are "melody swaps" (a video game track in another game's soundfont or style), the database should provide this data.
- If someone wanted to find all rips by the channel "SiIvaGunner uploaded between May 2017 and May 2018, the database will provide this.

## Local Setup/Install

If you want to set this database up locally (with or without sample data), follow the steps below:

Ensure you have the following dependencies too:

- PHP 8.1 or newer (Tested with PHP 8.3.2)
- Composer for PHP
- MySQL 8.0.41 or newer (Tested with 8.0.41)

To install and run locally:

1. Clone this repository `git clone https://github.com/Mr-Kaos/The-Rip-DB.git`
2. Open `sql/deploy.php` and change the database constants to that of your database.  
   > *The password is left blank in this file, so be sure to fill it in!*
3. In the terminal, navigate to the `sql` directory and run the command `php deploy.php`. This will deploy the database for you.  
   > **Optional:** When running this command, after the database is deployed, it will prompt to ask if you want to deploy sample data. Type `y` and press enter to insert some sample data.
4. Open the file `site/private_core/config/db.php` and edit its database constants to suit your database setup.  
   > *The password is left blank in this file too!*
5. In the terminal, move into the `site` directory and run `composer install` to download the two PHP dependencies.
6. Finally, in the same directory in the terminal, use `php -S localhost:8080` to run the site locally.

A more detailed (and automated) guide will be produced as the project progresses.

## What are "Rips"?

In case you are unfamiliar with rips, the term "Rips" generally refers to a bait-and-switch video that advertises a particular track from a video game's soundtrack, but is instead an altered version of said track, often a mashup, remix or rendition.

Some of the most notable channels that upload rips are [SiIvaGunner](https://www.youtube.com/@SiIvaGunner), [TimmyTurnersGrandDad](https://www.youtube.com/@TimmyTurnersGrandDad) and [Mysikt](https://www.youtube.com/@Mysikt).

There are other channels that have popped up over the years who also make rips, but often with less quality control and just to upload "joke" videos. While these may not be in the same spirit as traditional rips, these still aim to be cataloged.
