var fs = require('fs');
var path = require('path');
var db = require('../models');
var moment = require('moment');
var config = require('../config');
var util = require('util');

function readNews (cb) {
	db.News.findAll().success(function (result) {
		cb({news:result});
	});
}

function formatDate (date) {
	return moment(date).format('YYYY-MM-DDThh:mm:ss');
}

exports.json = function (req, res) {
	res.set('Content-Type', 'application/json');
	var dateFilter = null;
	var lang = req.query.lang;
	var date = req.query.since;

	if (date) {
		dateFilter = function (n) {
      return moment(n.created_at).isAfter(date);
		}
	}
	readNews(function (json) {
		json.news.map(function (n) {
			// return formatted created_at as date
			n.dataValues.date = n.date();

			if (lang) {
				// return correct i18n body as body
				n.dataValues.body = n.i18nBody(lang);
			}
			return n;
		});

		if (dateFilter) {
			json.news = json.news.filter(dateFilter);
		}
		res.json(json);
	});
}

exports.index = function (req, res) {
	readNews(function (json) {
		res.render('news/index', json);
	});
};

exports.addForm = function (req, res) {
	res.render('news/add');
}
exports.add = function (req, res) {
	var baseUrl = "http://" + config.imageHost + "/images/news/";

	function finish () {
		res.redirect(303, '/news');
	}
	function proccessImage (n) {
		var file = req.files.image
		var filename = path.basename(file.path);
		n.image_url = baseUrl + filename;

		n.save().success(finish);
	}
	if (req.files.image.size) {
		db.News.create(req.body).success(proccessImage);
	} else {
		// no image
		var article = db.News.build(req.body);
		article.image_url = baseUrl + "placeholder.png";
		db.News.create(req.body).success(finish);
	}
}