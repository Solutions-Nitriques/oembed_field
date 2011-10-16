# Field: oEmbed #

Version: 1.3.1

## Easily embed videos/images from ANY* website that implements the oEmbed format ##

see http://oembed.com

### SPECS ###

- Adds a field that takes as input the link to the page that has the embeded media
- Caches the oEmbed XML info into the database
	- Easily incorporate this XML via your Data Sources
	- Refreshes the info each time the entry is saved
- *Currently supported services: 
	- **Vimeo**
	- **Youtube**
	- **Dailymotion**
	- Flickr
	- Qik
	- Viddler
		- Anybody can add a service       
		  Just fork, code the missing [Service Driver](https://github.com/Solutions-Nitriques/oembed_field/blob/master/lib/class.serviceDriver.php) and request a pull!

### REQUIREMENTS ###

- Symphony CMS version 2.2 and up (as of the day of the last release of this extension)

### INSTALLATION ###

- Unzip the oembed_field.zip file
- (re)Name the folder oembed_field
- Put into the extension directory
- Enable/install just like any other extension

*Voila !*

http://www.nitriques.com/open-source/

### TODO ###

- Refactor how the drivers are managed in the ServiceDispatcher
- Allow appending parameters to oEmbed request from section editor
- Adds a auto-refresh data mechanism
- Automatically add sites in the JIT authorized sites (for thumbnail and image services)
- Add a field setting: Authorize only certain drivers (needs discussion on that)
- Add MySpace driver: Waiting for **MySpace** to complete their oEmbed service

### History ###

- 1.3.1 - 2011-10-xx    
  Added the `parameters sets` field's setting ([see how it work])
  Added the 'unique' option - url can now be unique across a section  
  Added Dailymotion, Qik and Viddler drivers (thanks Andrew!)
  Improved comments     
  Fix a typo (issue #6)    
  Added support for image (thumbnail) in table view    

- 1.3 - 2011-10-06      
  Improved error management - Added a ref flag for that in the public method     
  Added a method that permit change of the root tag name in the oEmbed response         

- 1.2.2 - 2011-09-28       
  Update the YouTube driver to 1.2 - Fix when the entered url contains a #

- 1.2.1 - 2011-09-08       
  Update the YouTube driver to 1.1 - Fix the width when the field is in the sidebar

- 1.2 - 2011-08-19     
  Update for Symphony 2.2.2 compatibility
  	(do not need to check for the $simulate value as it seems to be always true) 

- 1.1 - 2011-07-25     
  Couple of bug fixes    
  Adapted the code so oEmbed resources in the sidebar displays correctly

- 1.0.1 - 2011-07-17     
  Added YouTube

- 1.0 - 2011-07-15     
  First release