'use strict';
var db = require('../models');
var config = require('../config');
var _ = require('lodash');
var path = require('path');


var CATEGORIES = ['Official', 'Gold', 'Silver', 'Bronze'];

function readSponsors (cb) {
	db.Sponsor.findAll().success(function (result) {
		cb({sponsors: result});
	});
}

function groupSponsors (sponsors) {
	return _.groupBy(sponsors, function (s) {
		return s.category;
	});
}

function transformByParams (json, query) {
	if (query.group) {
		// group by sponsor type
		json.sponsors = groupSponsors(json.sponsors);
		return json;
	}
}

exports.index = function (req, res) {
	readSponsors(function (result) {
		result.sponsors = groupSponsors(result.sponsors);
		res.render('sponsors/index', result);
	});
};

exports.addForm = function (req, res) {
	res.render('sponsors/add', {categories: CATEGORIES});
};

exports.add = function (req, res) {
	function finish () {
		res.redirect(303, '/news');
	}

	var baseUrl = 'http://' + config.imageHost + '/images/sponsors/';

	var b = req.body;
	var sponsor = db.Sponsor.build(b);

	if (req.files.image.size) {
		var file = req.files.image;
		var filename = path.basename(file.path);
		sponsor.picture = baseUrl + filename;
		
		sponsor.save().success(function () {
			res.redirect(303, '/sponsors')
		});
	} else {
		var locals = {};
		locals.model = sponsor;
		locals.categories = CATEGORIES;

		// no image, invalid sponsor
		res.render('/sponsors/add', locals);
	}
};

exports.json = function (req, res) {
	res.set('Content-Type', 'application/json; charset=utf-8');
	var q = req.query;
	readSponsors(function (json) {
		transformByParams(json, q);
		res.json(json);
	});
};
