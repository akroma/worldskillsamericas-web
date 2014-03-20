var fs = require('fs');
var path = require('path');
var db = require('../models');
var moment = require('moment');
var config = require('../config');
var util = require('util');
var dataUtils = require('../lib/data')

function readNews (cb) {
	db.News.findAll({ order: 'created_at DESC'}).success(function (result) {
		cb({news:result});
	});
}

function transformByParams (entities, queryString) {
	var dateFilter = null;
	if (queryString.since) {
		dateFilter = function (n) {
			return moment(n.created_at).isAfter(queryString.since);
		}
	}
	entities.news.map(function (n) {
		// return formatted created_at as date
		n.dataValues.date = n.date();

		if (queryString.lang) {
			// return correct i18n values
			n.dataValues.body = n.i18nBody(queryString.lang);
			n.dataValues.title = n.i18nTitle(queryString.lang);
		}
		return n;
	});

	if (dateFilter) {
		entities.news = entities.news.filter(dateFilter);
	}
	return entities;
}

exports.json = function (req, res) {
	res.set('Content-Type', 'application/json; charset=utf-8');
	var dateFilter = null;
	var q = req.query;

	readNews(function (json) {
		res.json(transformByParams(json, q))
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
	var article = db.News.build(req.body);

	if (req.files.image.size) {
		var file = req.files.image
		var filename = path.basename(file.path);
		article.image_url = baseUrl + filename;
	} else {
		// no image
		article.image_url = baseUrl + "placeholder.png";
	}
	article.save().success(finish);
}