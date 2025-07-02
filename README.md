# The Rip Database Project

This project aims to provide an easy way to catalog and find "[rips](#what-are-rips)" uploaded by all "ripping" channels on YouTube.

## Table of Contents:

- [Aim of the Project](#aim-of-the-project)
- [Local Setup/Install](#local-setupinstall)
- [Project Features and Plans](#project-features-and-plans)
- [System Design](#system-design)

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
  - FlightPHP
  - PicoDB (>= 6.0.1)
- MySQL 8.0.41 or newer (Tested with 8.0.41)

To install and run locally:

1. Clone this repository `git clone https://github.com/Mr-Kaos/The-Rip-DB.git`
2. Open `sql/deploy.php` and change the database constants to that of your database.  
   > *The password is left blank in this file, so be sure to fill it in!*
3. In the terminal, navigate to the `sql` directory and run the command `php deploy.php`. This will deploy the database for you.  
   > **Optional:** When running this command, after the database is deployed, it will prompt to ask if you want to deploy sample data. Type `y` and press enter to insert some sample data.
4. Open the file `site/private_core/config/db.php` and edit its database constants to suit your database setup.  
   > *The password is left blank in this file too!*
5. In the terminal, move into the `site` directory and run `composer update` to download the two PHP dependencies.
6. Finally, in the same directory in the terminal, use `php -S localhost:8080` to run the site locally.

This is not a perfect fix, but it fixes this issue for the time being.

A more detailed (and automated) guide will be produced as the project progresses.

## Project Features and Plans

The primary goal of this project is to provide a complete and detailed database that allows users to search for a rip based on its contents, i.e. tags. This will also be crowd sourced, meaning anyone can contribute to fill in any gaps of information that this database is missing.

### Main Features (Current Progress)

This project currently has the following features complete:

- [X] Rips
  - [X] Search page
    - [X] Search filters
  - [X] Add Rip page
  - [X] Edit Rip page
- [ ] Jokes
  - [X] Search page
    - [X] Search filters
  - [X] Add Joke page
  - [ ] Edit Joke page
- [ ] Metas
  - [ ] Search page
  - [ ] Add Meta Joke page
  - [ ] Edit Meta Joke page
  - [ ] Add Meta page
  - [ ] Edit Meta page
- [ ] Tags
  - [X] Search page
  - [X] Add Tag page
  - [ ] Edit Tag page
- [ ] Core System
  - [X] Page routing
  - [X] Custom input elements
  - [ ] Moderation/Anti-spam system
  - [ ] Page alerts (e.g. Form submission success)
  - [ ] API for data export
- [ ] RipGuessr
  - [X] Functional gameplay loop
  - [ ] Multiplayer sessions
  - [ ] Community playlists/game presets

### Potential Features

Some other features that this project aims to include in the future are:

- [ ] Statistical features:
  - [ ] Statistics that show how common particular jokes are used in rips
  - [ ] Graphs that show how different rips relate to each other
- [ ] Ability to make quick YouTube playlists based on a search result
- [ ] Moderation/Anti-Spam tools. Potentially a way to prevent spam or bots from submitting bogus content.

If you have any cool or useful ideas for features, please feel free to suggest them!

## System Design

This system is based around rips and their data. All rips are stored in a central table, with the data to help identify rips stored in their own tables (jokes and tags tables).

The ER diagram below outlines the system's design and how each component communicates with each other.

![ER diagram of the database](RipDB_Diagram.svg)

### Relations Between Rips, Jokes, Metas and Tags

Rips and their jokes are the primary focus of this database. To make it easy for different rips to share the same jokes and attributes, a hierarchy of attributes is used to organise rips and their jokes. The hierarchy is as follows, from most broad to most specific:

1. Meta
2. Meta Joke
3. Joke
4. Tag

**Metas** are used to organise **Meta Jokes**.  
**Meta Jokes** are used to categorise **Jokes**.  
**Jokes** are used to categorise rips.
**Tags** are used to provide specific, but general information about a joke. e.g. if the joke is a song, the tag of "Theme Song" can be given to specify that the joke is a theme song.

The diagram below illustrates how some potential metas, meta jokes, jokes and tags are related to each other.

![Diagram showing how jokes, tags, meta jokes and metas are linked](site/res/img/Object_Relations_Diagram.svg)

## What are "Rips"?

In case you are unfamiliar with rips, the term "Rips" generally refers to a bait-and-switch video that advertises a particular track from a video game's soundtrack, but is instead an altered version of said track, often a mashup, remix or rendition.

Some of the most notable channels that upload rips are [SiIvaGunner](https://www.youtube.com/@SiIvaGunner), [TimmyTurnersGrandDad](https://www.youtube.com/@TimmyTurnersGrandDad) and [Mysikt](https://www.youtube.com/@Mysikt).

There are other channels that have popped up over the years who also make rips, but often with less quality control and just to upload "joke" videos. While these may not be in the same spirit as traditional rips, these still aim to be cataloged.
