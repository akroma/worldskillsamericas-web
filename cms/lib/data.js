var _ = require('lodash');
var moment = require('moment');

exports.groupByDay = function (entities, field) {
  return _.groupBy(entities, function (e) {
    return moment(e[field]).format('YYYY-MM-DD');
  });
}

exports.formatDate = function (date) {
  return moment(date).format('YYYY-MM-DDThh:mm:ss');
}