/**
 * Module dependencies.
 */
 'use strict';
 var express = require('express');
 var routes = require('./routes');
 var http = require('http');
 var path = require('path');
 var news = require('./routes/news');
 var events = require('./routes/events');
 var sponsors = require('./routes/sponsors');
 var db = require('./models');
 var formidable = require('formidable');
 var mkdirp = require('mkdirp');
 var app = express();
 var config = require('./config');


// all environments
app.set('port', process.env.PORT || config.port || 3000);
app.use(express.compress());
app.set('views', path.join(__dirname, 'views'));
app.set('view engine', 'ejs');
app.set('layout extractScripts', true);
app.use(express.favicon(__dirname + '/wsa.ico'));
app.use(express.logger('dev'));
app.use(express.json());
app.use(express.urlencoded());
app.use(express.methodOverride());
app.use(require('express-ejs-layouts'));
app.use(app.router);
app.use(express.static(path.join(__dirname, 'public')));

app.use(express.errorHandler());


function uploadImage (uploadDir) {
	return function (req, res, next) {
		var form = formidable.IncomingForm();
		form.keepExtensions = true;
		form.uploadDir = __dirname + uploadDir;
		form.parse(req, function (err, fields, files) {
			req.body = fields;
			req.files = files;
			next();
		});
	};
}

var auth = express.basicAuth(config.user, config.password);

app.get('/', routes.index);

app.get('/news', news.index);
app.post('/news', auth, uploadImage('/public/images/news/'), news.add);
app.get('/news/add', auth, news.addForm);
app.get('/news.json', news.json);

app.get('/events', events.index);
app.post('/events', auth, events.add);
app.get('/events/add', auth, events.addForm);
app.get('/events.json', events.json);

app.del('/sponsors/:id', auth, sponsors.del);
app.get('/sponsors', sponsors.index);
app.get('/sponsors/add', auth, sponsors.addForm);
app.post('/sponsors', auth, uploadImage('/public/images/sponsors/'), sponsors.add);
app.get('/sponsors.json', sponsors.json);


// create path for images
mkdirp(__dirname + '/public/images/news', function () {
	mkdirp(__dirname + '/public/images/sponsors', function () {
		console.log('Images upload directories created successfully');
	});
});

//Initialize sequelize

var syncOpts = {};

// rebuild db for dev
if (app.get('env') == 'development') {
	// syncOpts.force = true;
}

db.sequelize
.sync(syncOpts)
.complete(function (err) {
	if (err) {
		throw err;
	} else {
		http.createServer(app).listen(app.get('port'), function(){
			console.log('environment: ' + app.get('env'));
			console.log('Express server listening on port ' + app.get('port'));
			console.log('server base url set to ' + config.baseUrl);
		});
	}
});