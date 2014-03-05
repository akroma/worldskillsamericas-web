var moment = require('moment');

function format (date) {
	return moment(date).format('YYYY-MM-DD');
}

module.exports = function (sequelize, DataTypes) {
	var Event = sequelize.define('Event',
	{
		body_en: DataTypes.STRING,
		body_es: DataTypes.STRING,
		body_pt: DataTypes.STRING,
		start: DataTypes.DATE,
		end: DataTypes.DATE
	}, {
		instanceMethods: {
			date: function (which) {
				if (which == 'start' || which == 'end') {
					return format(this.getDataValue(which));
				}
				return null;
			},
			i18nBody: function (lang) {
				return this.getDataValue('body_' + lang);
			}
		},
		underscored: true
	});
	return Event;
}