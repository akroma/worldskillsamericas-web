CREATE TABLE skill_sponsors(
	id MEDIUMINT(9) NOT NULL PRIMARY KEY AUTO_INCREMENT,
	competition_id MEDIUMINT(9) NOT NULL,
	competition_trade_id VARCHAR(9) NOT NULL,
	name VARCHAR(255),
	logo_thumb_url VARCHAR(255),
	profile_image_url VARCHAR(255),
	profile_title_en VARCHAR(255),
	profile_title_de VARCHAR(255),
	profile_description_en MEDIUMTEXT,
	profile_description_de MEDIUMTEXT,
	profile_video_url VARCHAR(255),
	read_more_url VARCHAR(255),
	created DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	modified DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'
);

INSERT INTO skill_sponsors VALUES('', 42, '17', 'Autodesk', 'https://asset1.basecamp.com/1904112/people/1802400-joni-aaltonen/photo/avatar.96.gif', 'http://www.worldskills.org/images/stories/header_banners/website-header-restaurant-service.jpg', 'Autodesk', 'Autodesk', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Recusandae, quod blanditiis repellendus pariatur quia consequuntur id eos voluptatum consequatur architecto minima ducimus ipsum sequi! Dolore, maiores beatae magni error reprehenderit.', 'Lorem in german', 'https://www.youtube.com/watch?v=njxSCtE_QG0', 'http://www.autodesk.com', NOW(), NOW());
INSERT INTO skill_sponsors VALUES('', 42, '17', 'Fluke', 'https://asset1.basecamp.com/1904112/people/1802400-joni-aaltonen/photo/avatar.96.gif', 'http://www.worldskills.org/images/stories/header_banners/website-header-restaurant-service.jpg', 'Autodesk', 'Autodesk', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Recusandae, quod blanditiis repellendus pariatur quia consequuntur id eos voluptatum consequatur architecto minima ducimus ipsum sequi! Dolore, maiores beatae magni error reprehenderit.', 'Lorem in german', 'https://www.youtube.com/watch?v=njxSCtE_QG0', 'http://www.autodesk.com', NOW(), NOW());
INSERT INTO skill_sponsors VALUES('', 42, '01', 'Autodesk', 'https://asset1.basecamp.com/1904112/people/1802400-joni-aaltonen/photo/avatar.96.gif', 'http://www.worldskills.org/images/stories/header_banners/website-header-restaurant-service.jpg', 'Autodesk', 'Autodesk', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Recusandae, quod blanditiis repellendus pariatur quia consequuntur id eos voluptatum consequatur architecto minima ducimus ipsum sequi! Dolore, maiores beatae magni error reprehenderit.', 'Lorem in german', 'https://www.youtube.com/watch?v=njxSCtE_QG0', 'http://www.autodesk.com', NOW(), NOW());
INSERT INTO skill_sponsors VALUES('', 42, 'D1', 'Autodesk', 'https://asset1.basecamp.com/1904112/people/1802400-joni-aaltonen/photo/avatar.96.gif', 'http://www.worldskills.org/images/stories/header_banners/website-header-restaurant-service.jpg', 'Autodesk', 'Autodesk', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Recusandae, quod blanditiis repellendus pariatur quia consequuntur id eos voluptatum consequatur architecto minima ducimus ipsum sequi! Dolore, maiores beatae magni error reprehenderit.', 'Lorem in german', 'https://www.youtube.com/watch?v=njxSCtE_QG0', 'http://www.autodesk.com', NOW(), NOW());


CREATE TABLE test_projects(
	id MEDIUMINT(9) NOT NULL PRIMARY KEY AUTO_INCREMENT,
	competition_id MEDIUMINT(9) NOT NULL,
	competition_trade_id VARCHAR(9) NOT NULL,
	title_en VARCHAR(255),
	title_de VARCHAR(255),
	images MEDIUMTEXT, #JSON ARRAY
	subtitle_en VARCHAR(255),
	subtitle_de VARCHAR(255),
	description_en MEDIUMTEXT,
	description_de MEDIUMTEXT,
	created DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	modified DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'
);




INSERT INTO test_projects VALUES('', 42, '17', 'Web Design Project', 'Project DE', '["http:\/\/www.worldskillsportal.com\/images\/stories\/NLS\/17_web_design_01.jpg","http:\/\/www.worldskillsportal.com\/images\/stories\/NLS\/17_web_design_01.jpg"]', 'Subtitle', 'Subtitle DE', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Numquam, commodi cupiditate possimus aliquid suscipit doloribus rerum nulla eius illum nam. Odio, alias itaque ullam nobis minus quod maiores perspiciatis qui?', 'Lorem Ipsum in DE', NOW(), NOW());
INSERT INTO test_projects VALUES('', 42, '01', '01 Project', 'Project DE', '["http:\/\/www.worldskillsportal.com\/images\/stories\/NLS\/17_web_design_01.jpg","http:\/\/www.worldskillsportal.com\/images\/stories\/NLS\/17_web_design_01.jpg"]', 'Subtitle', 'Subtitle DE', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Numquam, commodi cupiditate possimus aliquid suscipit doloribus rerum nulla eius illum nam. Odio, alias itaque ullam nobis minus quod maiores perspiciatis qui?', 'Lorem Ipsum in DE', NOW(), NOW());
INSERT INTO test_projects VALUES('', 42, 'D1', 'D1 Project', 'Project DE', '["http:\/\/www.worldskillsportal.com\/images\/stories\/NLS\/17_web_design_01.jpg","http:\/\/www.worldskillsportal.com\/images\/stories\/NLS\/17_web_design_01.jpg"]', 'Subtitle', 'Subtitle DE', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Numquam, commodi cupiditate possimus aliquid suscipit doloribus rerum nulla eius illum nam. Odio, alias itaque ullam nobis minus quod maiores perspiciatis qui?', 'Lorem Ipsum in DE', NOW(), NOW());


ALTER TABLE competition_trade_sectors ADD COLUMN sector_color VARCHAR(7) NOT NULL DEFAULT '#c0c0c0' after sector_order;
ALTER TABLE competition_trade_sectors ADD COLUMN sector_description_de tinytext AFTER `sector_description`;
ALTER TABLE competition_trade_sectors ADD COLUMN sector_description_fr tinytext AFTER `sector_description_de`;

UPDATE competition_trade_sectors SET sector_color = '#72c25c' WHERE id = 1;
UPDATE competition_trade_sectors SET sector_color = '#bf373b' WHERE id = 2;
UPDATE competition_trade_sectors SET sector_color = '#ffa33c' WHERE id = 3;
UPDATE competition_trade_sectors SET sector_color = '#017dc3' WHERE id = 4;
UPDATE competition_trade_sectors SET sector_color = '#cb2ae8' WHERE id = 5;
UPDATE competition_trade_sectors SET sector_color = '#ef0d89' WHERE id = 6;


CREATE TABLE app_updates(
	file VARCHAR(255) NOT NULL PRIMARY KEY,
	last_updated DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	created DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	modified DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'
);


CREATE TABLE app_api_stats(
		id MEDIUMINT(9) NOT NULL PRIMARY KEY AUTO_INCREMENT,
		feed VARCHAR(255),
		timestamp DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
		skill VARCHAR(3),
		created DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
		modified DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00');
#SO FAR ON LIVE WIW2

ALTER TABLE competition_trade_sectors drop column sector_color;
ALTER TABLE competition_trade_sectors ADD COLUMN sector_baseColor VARCHAR(7);
ALTER TABLE competition_trade_sectors ADD COLUMN sector_secondaryColor VARCHAR(7);
ALTER TABLE competition_trade_sectors ADD COLUMN sector_labelColor VARCHAR(7);

UPDATE competition_trade_sectors SET sector_baseColor = '#8dc640', sector_secondaryColor = '#8dc640', sector_labelColor = '#ffffff' WHERE id = 1;
UPDATE competition_trade_sectors SET sector_baseColor = '#c4161c', sector_secondaryColor = '#a4181c', sector_labelColor = '#ffffff' WHERE id = 2;
UPDATE competition_trade_sectors SET sector_baseColor = '#faa61a', sector_secondaryColor = '#f58222', sector_labelColor = '#ffffff' WHERE id = 3;
UPDATE competition_trade_sectors SET sector_baseColor = '#00aeef', sector_secondaryColor = '#009ce0', sector_labelColor = '#ffffff' WHERE id = 4;
UPDATE competition_trade_sectors SET sector_baseColor = '#922278', sector_secondaryColor = '#782890', sector_labelColor = '#ffffff' WHERE id = 5;
UPDATE competition_trade_sectors SET sector_baseColor = '#ec008b', sector_secondaryColor = '#ec4096', sector_labelColor = '#ffffff' WHERE id = 6;


#so far on live

CREATE TABLE app_photo_uploads(
id MEDIUMINT(9) NOT NULL PRIMARY KEY AUTO_INCREMENT,
caption varchar(255),
skill VARCHAR(255),
author VARCHAR(255),
description mediumtext,
created DATETIME,
modified DATETIME);


#so far on live

CREATE TABLE app_feedback(
	id MEDIUMINT(9) NOT NULL PRIMARY KEY AUTO_INCREMENT,
`from` varchar(255),
from_email VARCHAR(255),
subject VARCHAR(255),
message mediumtext,
created DATETIME,
modified DATETIME
);

# so far on live

CREATE TABLE app_photo_highlights(
id MEDIUMINT(9) NOT NULL PRIMARY KEY AUTO_INCREMENT,
title VARCHAR(255),
flickr_set_url VARCHAR(255),
created DATETIME,
modified DATETIME);

CREATE TABLE app_events(
id MEDIUMINT(9) NOT NULL PRIMARY KEY AUTO_INCREMENT,
title_en VARCHAR(255),
title_de VARCHAR(255),
content_en MEDIUMTEXT,
content_de MEDIUMTEXT,
content_image VARCHAR(255),
created DATETIME,
modified DATETIME);
);

CREATE TABLE app_venues(
id MEDIUMINT(9) NOT NULL PRIMARY KEY AUTO_INCREMENT,
title_en VARCHAR(255),
title_de VARCHAR(255),
content_en MEDIUMTEXT,
content_de MEDIUMTEXT,
content_image VARCHAR(255),
created DATETIME,
modified DATETIME
);


#LIVE MARKER

CREATE TABLE app_sponsor_groups(
id MEDIUMINT(9) NOT NULL PRIMARY KEY AUTO_INCREMENT,
title_en VARCHAR(255),
title_de VARCHAR(255),
ordernum TINYINT(2),
created DATETIME,
modified DATETIME
);					

INSERT INTO app_sponsor_groups VALUES('1', 'Global Industry Partners', '', 1, NOW(), NOW());	
INSERT INTO app_sponsor_groups VALUES('2', 'Event Sponsors', '', 2, NOW(), NOW());	

CREATE TABLE app_sponsors(
id MEDIUMINT(9) NOT NULL PRIMARY KEY AUTO_INCREMENT,
group_id MEDIUMINT(9) NOT NULL,
name VARCHAR(255),
logo_thumb_url VARCHAR(255),
profile_image_url VARCHAR(255),
profile_title_en VARCHAR(255),
profile_title_de VARCHAR(255),
profile_description_en MEDIUMTEXT,
profile_description_de MEDIUMTEXT,
profile_video_url VARCHAR(255),
read_more_url VARCHAR(255),
created DATETIME,
modified DATETIME);


#LIVER MARKER
CREATE TABLE app_skill_definitions(
id MEDIUMINT(9) NOT NULL PRIMARY KEY AUTO_INCREMENT,
internal_trade_id MEDIUMINT(9) NOT NULL,
required_skills_en MEDIUMTEXT,
required_skills_de MEDIUMTEXT,
industry_action_en MEDIUMTEXT,
industry_action_de MEDIUMTEXT,
competition_action_en MEDIUMTEXT,
competition_action_de MEDIUMTEXT,
created DATETIME,
modified DATETIME
);	


#LIVE MARKER
CREATE TABLE app_local_push_global(
id MEDIUMINT(9) NOT NULL PRIMARY KEY AUTO_INCREMENT,
timestamp DATETIME NOT NULL,
text_en VARCHAR(255),
text_de VARCHAR(255),
created DATETIME,
modified DATETIME
);	

alter table app_photo_uploads ADD COLUMN filename VARCHAR(255) AFTER description;
alter table app_photo_uploads ADD COLUMN fileurl VARCHAR(255) AFTER description;

#LIVE MARKER

CREATE TABLE app_event_schedule(
id MEDIUMINT(9) NOT NULL PRIMARY KEY AUTO_INCREMENT,
competition_day VARCHAR(4),
datestamp DATE NOT NULL,
title VARCHAR(255),
description MEDIUMTEXT,
skill MEDIUMINT(9),
start_time TIME,
end_time TIME,
created DATETIME,
modified DATETIME
);

#LIVE MARKER
