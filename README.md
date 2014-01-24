Aggregator API
===================


Updates
---------
* videos feed
* event and venue feed changed slightly to a simpler format
* added feedback webservice, see readme in github under feedback
* added photo upload webservice, see readme in github under photo_submissions
* added HTTP Compression - add extra parameter &compression=1 to enable, disabled by default at the moment. Also pass Allow-Encoding: gzip,deflate in request.
* skill feed modified
* all skills feed modified to return json
* sponsors feed modified
* added photos feed
* Skill specific json files now contain two picture URLs for profiles picture_thumb_url and picture_url, the latter contains the full resolution version to be downloaded on fly if connection is available.
* modified sector JSON to include skills inside the sector, still missing the final color code fields
* added local-push (JSON format updated)
* added news
* Aggregator API now returns four values
	* file - the gzip file of json
	* json_file - to contain non-gzipped version
	* modified - last modified date
	* feed_content - in case of event, local-push, sponsor, venue, news and sector this field contains the JSON feed so there is no need to download the gzip file with a separate request


Usage
---------

Aggregator is located at [http://aggregator.worldskills.org](http://aggregator.worldskills.org)

The API for the mobile application is located at [http://aggregator.worldskills.org/api.php](http://aggregator.worldskills.org/aggregator/api.php)

The Aggregator API takes the following **POST** parameters

* **feed** - the name of the feed requested
* **timestamp** - timestamp of the last change on offline data. Format: yyyy-mm-dd hh:ii:ss (PHP `date(Y-m-d H:i:s)`)
* **skill** - optional parameter, only when requesting skill-specific feeds (feed = skill)


The feed parameter accepts the following values:

* **full** - returns a gzipped tar file with all possible .json.gz files inside
* **generic** - returns a gzipped tar file with generic .json.gz files inside (event, sectors, sponsors, venue). Will contain news, push etc in the future
* **event** - returns event information
* **sponsors** - returns sponsor information
* **venue** - returns venue information
* **news** - returns news articles
* **local-push** - returns local push notifications
* **sector** - returns sectors with color codes and all skills inside the sector
* **all-skills** - returns a gzipped tar file of all skill-specific .json.gz files
* **skill** - returns a skill-specific .json.gz file. Remember to supply **skill** paraterer as well.
* **photos** - returns a feed with set names and flickr set URLs of highlight photos
* **videos** - returns a list of youtube playlist URL, same format as with photos feed
* **sponsors** - returns a feed with sponsors (grouped)

When called, the aggregator checks for the timestamp of the latest change in the requested feed, and if the feed has changed after the timestamp supplied, it will return the timestamp of the latest change, and the URL to download the requested feed.

**Example:** `{"file":"http:\/\/localhost\/~joni\/WorldSkills-Aggregator\/aggregator\/cache_collections\/full-data.tar.gz","modified":"2013-05-17 13:07:52"}`

If the feed has not changed after the supplied timestamp, the aggregator will return the last modified date of the requested feed.

**Example:** `{"modified":"2013-05-17 13:07:52"}`

**NOTICE** I will change the aggregator next week to return JSON as a return value instead of the GZIP package for the simple calls such as news, sectors, venue information, event information etc. This will help so that there is no need for a two requests when refreshing these contents.


Version information
----------------------

The aggregator can also return the version of the mobile application. This version information can be used to check if the application needs to be forced an update.

Version information is located at [http://aggregator.worldskills.org/aggregator/version.php](http://aggregator.worldskills.org/aggregator/version.php)

Currently it returns the following JSON

`{"version":1.0}`
