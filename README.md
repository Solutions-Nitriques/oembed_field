# Field: oEmbed #

Version: 1.0

## Easily embed videos/images from ANY website that implements the oEmbed format ##

see http://oembed.com

### SPECS ###

- Adds a field that takes as input the link to the page that has the embeded media
- Caches the oEmbed XML info into the database
	- Easily get it into via your Data Sources
	- Refreshes the info each time the entry is saved
- Currently supported services: ***Vimeo*** only
	- v1.1 will support Youtube and Flickr too
- Anybody can add a service: Just fork, code the missing Service Driver and request a pull!

### REQUIREMENTS ###

- Symphony CMS version 2.2 and up (as of the day of the last release of this extension)

### INSTALLATION ###

- Unzip the oembed_field.zip file
- (re)Name the folder oembed_field
- Put into the extension directory
- Enable/install just like any other extension

*Voila !*

http://www.nitriques.com/open-source/

### History ###

- 1.0 - 2011-07-12
  First release