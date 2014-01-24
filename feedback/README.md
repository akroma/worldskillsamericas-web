Feedback submission web service
===============================

The webservice can be accessed at [http://aggregator.worldskills.org/feedback/submit.php](http://aggregator.worldskills.org/feedback/submit.php)

The webservice uses $_POST Authentication

* Username: app_feedback
* Password: GC4YVWOJ3fLBI1gS
* Example call url: http://aggregator.worldskills.org/feedback/submit.php

Required auth variables
	$_POST['auth_user']
	$_POST['auth_pass']


Required input
-----------------

The webservice takes in 4 **POST** attributes (all required but some can be empty)

* from
* from_email
* subject
* message
* auth_user
* auth_pass

Possible return values
------------------------

All return values are JSON formatted.

Return JSON values:

* status - status code
* message - message

**Example return JSON:** `{"status":"101","message":"Error: missing POST data variables"}`
