Photo submission web service
===============================

The webservice can be accessed at [http://aggregator.worldskills.org/photo_submissions/submit.php](http://aggregator.worldskills.org/photo_submissions/submit.php)

The webservice uses $_POST authentication

* Username: app_upload
* Password: CbRdThE0Mv5y6taK
* Example call url: http://aggregator.worldskills.org/photo_submissions/submit.php

Required variables for auth
	
	$_POST['auth_user']
	$_POST['auth_pass']


Required input
-----------------

The webservice takes in 5 **POST** attributes (all required but some can be empty)

* caption
* skill
* author
* description
* photo
* auth_user
* auth_pass

The photo is sent as a "file", not a base64 representation.

Possible return values
------------------------

All return values are JSON formatted.

Return JSON values:

* status - status code
* message - message
* photo_url - only returned when photo upload successful

**Example return JSON:** `{"status":"101","message":"Error: missing POST data variables"}`
