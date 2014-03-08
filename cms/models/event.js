'use strict';
var moment = require('moment');

function format (date) {
	return moment(date).format('YYYY-MM-DD hh:mm:ss');
}

module.exports = function (sequelize, DataTypes) {
	var Event = sequelize.define('Event',
	{
		body_en: DataTypes.STRING,
		body_es: DataTypes.STRING,
		body_pt: DataTypes.STRING,
		start: DataTypes.DATE
	}, {
		instanceMethods: {
			date: function () {
				return format(this.getDataValue('start'));
			},
			i18nBody: function (lang) {
				if (lang == 'pt' ||
					lang == 'es' ||
					lang == 'en') {
					return this.getDataValue('body_' + lang);
				}
				return this.getDataValue('body_es');
			}
		},
		underscored: true
	});
	return Event;
};