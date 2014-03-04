
/**
 * Module dependencies.
 */

 var express = require('express');
 var routes = require('./routes');
 var http = require('http');
 var path = require('path');
 var news = require('./routes/news');
 var db = require('./models');
 var formidable = require('formidable');
 var util = require('util');
 var config = require('./config');
 var mkdirp = require('mkdirp');

 var app = express();

// all environments
app.set('port', process.env.PORT || config.port || 3000);
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

// development only
if ('development' == app.get('env')) {
	app.use(express.errorHandler());
}

function uploadNewsImage (req, res, next) {
	var form = formidable.IncomingForm();
	form.keepExtensions = true;
	form.uploadDir = __dirname + '/public/images/news/';
	form.parse(req, function (err, fields, files) {
		req.body = fields;
		req.files = files;
		util.inspect(files);
		next();
	});
}

app.get('/', routes.index);

app.get('/news', news.index);
app.post('/news', uploadNewsImage, news.add);
app.get('/news/add', news.addForm);

app.get('/news.json', news.json);
app.get('/news.json/:lang', news.json);
app.get('/news.json/:lang/:date', news.json);

// create path for images
mkdirp('./public/images/news', function (err) {
	if (err){
		throw err
	} else {
		console.log('Image upload directory created successfully');
	}
});


//Initialize sequelize
db.sequelize
.sync() // rebuild db for dev
.complete(function (err) {
	if (err) {
		throw err
	} else {
		http.createServer(app).listen(app.get('port'), function(){
			console.log('Express server listening on port ' + app.get('port'));
			console.log('server base url set to ' + config.baseUrl);
		});			
	}
});