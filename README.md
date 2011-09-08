# Field: oEmbed #

Version: 1.2.1

## Easily embed videos/images from ANY website that implements the oEmbed format ##

see http://oembed.com

### SPECS ###

- Adds a field that takes as input the link to the page that has the embeded media
- Caches the oEmbed XML info into the database
	- Easily get it into via your Data Sources
	- Refreshes the info each time the entry is saved
- Currently supported services: ***Vimeo, Flickr, Youtube***
	- Anybody can add a service: Just fork, code the missing [Service Driver](https://github.com/Solutions-Nitriques/oembed_field/blob/master/lib/class.serviceDriver.php) and request a pull!

### REQUIREMENTS ###

- Symphony CMS version 2.2 and up (as of the day of the last release of this extension)

### INSTALLATION ###

- Unzip the oembed_field.zip file
- (re)Name the folder oembed_field
- Put into the extension directory
- Enable/install just like any other extension

### XSLT EXAMPLE ###

In this exmaple, the field `video` contains videos should be displayed width="580" height="467"

	<xsl:variable name="video-id" select="video/@id" />
			
	<xsl:choose>
		<xsl:when test="video/oembed/provider_name = 'Vimeo'">
			<!-- Player vimeo -->
			<iframe id="vimeo-player" src="http://player.vimeo.com/video/{$video-id}?color=E33A2C&amp;title=0&amp;portrait=0&amp;byline=0&amp;api=0&amp;autoplay=0" width="580" height="467" frameborder="0"></iframe>
		</xsl:when>
		<xsl:when test="video/oembed/provider_name = 'YouTube'">
			<!-- Player YouTube -->
		 	<xsl:value-of select="str:replace(str:replace(video/oembed/html,'width=&quot;480&quot;', 'width=&quot;580&quot;'), 'height=&quot;295&quot;', 'height=&quot;467&quot;')" disable-output-escaping="yes" />
		</xsl:when>
	</xsl:choose>
	

*Voila !*

http://www.nitriques.com/open-source/

### TODO ###

- Add support for image (thumbnail) in table view
- Improve error handling when loading XML data, especially if the HTTP status is 503
- Adds a auto-refresh data mechanism
- Automatically add sites in the JIT authorized sites
- Add a field setting: Authorize only certain drivers

### History ###

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