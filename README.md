# Field: oEmbed #

Version: 1.11.x

## Easily embed videos/images from ANY* website that implements the oEmbed format ##

@see <http://oembed.com>

### SPECS ###

- Adds a field that takes as input the link to the page that has the embeded media
- Caches the oEmbed XML info into the database
	- Easily incorporate this XML via your Data Sources
	- Refreshes the info each time the entry is saved
- *Currently supported services: 
	- **Vimeo** (http/https)
	- **Youtube** (https/https)
	- **Dailymotion** (http/https)
	- **Twitter** (http/https)
	- **Instagram** (http/https)
	- **Facebook** (http/https)
	- Embed.ly (http/https) * Non-native (requires an account to get the key)
	- Iframely (http/https) * Non-native
	- Flickr (http/https)
	- Qik (http)
	- Viddler (http)
	- SlideShare (http)
	- Soundcloud (http/https)
	- Mixcloud (http/https)
	- Noembed (http) * Non-native
	- Yours... Anybody can add a service!       
	  Just fork, code the missing [Service Driver](https://github.com/Solutions-Nitriques/oembed_field/blob/master/lib/class.serviceDriver.php) and request a pull!

### REQUIREMENTS ###

- Symphony CMS version 2.5.0 and up (as of the day of the last release of this extension)

### INSTALLATION ###

- `git clone` / download and unpack the tarball file
- (re)Name the folder **oembed_field**
- Put into the extension directory
- Enable/install just like any other extension

See <http://www.getsymphony.com/learn/tasks/view/install-an-extension/>

*Voila !*

### HOW TO USE ###

- After installation, add a oEmbed field to a section
- Configure the field
	- Select at least one supported driver
	- You can add extra parameters to the oEmbed request's query string: this is usefull for settings embed sizes
- All the data will be available as xml in a datasource
- Use the `oembed` tag for embeding the resource into your frontend

#### Non-native ####

"Non-native" solutions like embed.ly are tested after all other "native" solutions. This will
allow you to enable both natives solution while being able to revert to a global fallback. If
other non-native solutions are added, please do not enable more than one because this may cause 
un-wanted behavior.

### TODO ###

- Adds a auto-refresh data mechanism
- Automatically add sites in the JIT authorized sites (for thumbnail and image services)
- Add MySpace driver: Waiting for **MySpace** to complete their oEmbed service
- Add Cinemagram driver: Waiting for **Cinemagram** to complete their oEmbed service
- Add Issuu driver: Waiting for **Issuu** to complete their oEmbed service
- Add UStream driver: Waiting for **UStream** to complete their oEmbed service

### CREDITS ###

Come say hi! -> <https://deuxhuithuit.com/>

And thanks to everybody that added ServiceDrivers, reported bugs or submitted any improvements !

### LICENSE ###

MIT. See the LICENSE file or <http://deuxhuithuit.mit-license.org>
