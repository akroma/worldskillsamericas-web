'use strict';
var db = require('../models');
var _ = require('lodash');

function readSponsors (cb) {
	db.Sponsor.findAll().success(function (result) {
		cb({sponsors: result});
	});
}

function transformByParams (json, query) {
	if (query.group) {
		// group by sponsor type
		json.sponsors = _.groupBy(json.sponsors, function (s) {
			return s.category;
		});
		return json;
	}
}

exports.json = function (req, res) {
	res.set('Content-Type', 'application/json; charset=utf-8');
	var q = req.query;
	readSponsors(function (json) {
		transformByParams(json, q)
		console.log(json);
		res.json(json);
	});
};
