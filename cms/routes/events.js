var fs = require('fs');
var path = require('path');
var db = require('../models');
var moment = require('moment');
var config = require('../config');
var util = require('util');

function readEvents (cb) {
  db.Event.findAll().success(function (result) {
    cb({events:result});
  });
}

function formatDate (date) {
  return moment(date).format('YYYY-MM-DDTHH:mm:ss');
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
  readEvents(function (json) {
    json.events.map(function (n) {
      // return formatted created_at as date
      n.dataValues.date = n.date();

      if (lang) {
        // return correct i18n body as body
        n.dataValues.body = n.i18nBody(lang);
      }
      return n;
    });

    if (dateFilter) {
      json.events = json.events.filter(dateFilter);
    }
    res.json(json);
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