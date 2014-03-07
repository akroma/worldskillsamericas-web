var fs = require('fs');
var path = require('path');
var db = require('../models');
var moment = require('moment');
var config = require('../config');
var util = require('util');
var dataUtils = require('../lib/data')

function readEvents (cb) {
  db.Event.findAll({ order: 'start DESC'}).success(function (result) {
    cb({events:result});
  });
}

function formatDate (date) {
  return moment(date).format('YYYY-MM-DDTHH:mm:ss');
}

function transformByParams (entities, queryString) {
  var dateFilter = null;
  if (queryString.date) {
    dateFilter = function (n) {
      return moment(n.created_at).isAfter(queryString.date);
    }
  }
  entities.events.map(function (n) {
    if (queryString.lang) {
      // return correct i18n body as body
      n.dataValues.body = n.i18nBody(queryString.lang);
    }
    return n;
  });

  if (dateFilter) {
    entities.events = entities.events.filter(dateFilter);
  }

  if (queryString.group) {
    entities.events = dataUtils.groupByDay(entities.events, 'start');
  }

  return entities;
}

exports.json = function (req, res) {
  res.set('Content-Type', 'application/json; charset=utf-8');
  var dateFilter = null;
  var q = req.query;
  readEvents(function (json) {
    res.json(transformByParams(json, q));
  });
}


exports.index = function (req, res) {
  readEvents(function (json) {
    res.render('events/index', json);
  });
};

exports.addForm = function (req, res) {
  res.render('events/add');
}
exports.add = function (req, res) {
  var b = req.body;
  var event = db.Event.build(b);
  event.start = buildDate(b, 'start');
  console.log(buildDate(b, 'start'));
  event.end = buildDate(b, 'end');
  event.save().success(function () {
    res.redirect(303, '/events');
  });
}

function buildDate (body, prefix) {
  var t = moment(body[prefix]);
  t.add('hours', body[prefix + '_hour']);
  t.add('minutes', body[prefix + '_min']);
  return t.toDate();
}