<<<<<<< HEAD
# Field: oEmbed #

Version: 1.5.1

## Easily embed videos/images from ANY* website that implements the oEmbed format ##

@see <http://oembed.com>

### SPECS ###

- Adds a field that takes as input the link to the page that has the embeded media
- Caches the oEmbed XML info into the database
	- Easily incorporate this XML via your Data Sources
	- Refreshes the info each time the entry is saved
- *Currently supported services: 
	- **Vimeo**
	- **Youtube**
	- **Dailymotion**
	- **Twitter**
	- Flickr
	- Qik
	- Viddler
	- SlideShare
		- Anybody can add a service       
		  Just fork, code the missing [Service Driver](https://github.com/Solutions-Nitriques/oembed_field/blob/master/lib/class.serviceDriver.php) and request a pull!

### REQUIREMENTS ###

- Symphony CMS version 2.3 and up (as of the day of the last release of this extension)

### INSTALLATION ###

- Unzip the oembed_field.zip file
- (re)Name the folder **oembed_field**
- Put into the extension directory
- Enable/install just like any other extension

*Voila !*

### HOW TO USE ###

- After installation, add a oEmbed field to a section
- Configure the field
	- Select at least one supported driver
	- You can add extra parameters to the oEmbed request's query string
- All the data will be available as xml in a datasource
- Use the `oembed` tag for embeding the resource into your frontend

### TODO ###

- Adds a auto-refresh data mechanism
- Automatically add sites in the JIT authorized sites (for thumbnail and image services)
- Add MySpace driver: Waiting for **MySpace** to complete their oEmbed service
- Add a JSON parser implementation

### CREDITS ###

<http://www.nitriques.com/open-source/>

<http://www.deuxhuithuit.com/>

And thanks to everybody that added ServiceDrivers, reported bugs or submitted any improvements !


=======
sections_visualization
======================

Symphony extensions that offers a global view of the sections (Data Model)
>>>>>>> 06297c384143afa255fca880dca684f23450cf5b
