var moment = require('moment');

module.exports = function (sequelize, DataTypes) {
	var News = sequelize.define('News',
	{
		title: DataTypes.STRING,
		image_url: DataTypes.STRING,
		body_en: DataTypes.TEXT,
		body_es: DataTypes.TEXT,
		body_pt: DataTypes.TEXT,
		author: DataTypes.STRING
	}, {
		instanceMethods: {
			date: function () {
				var date = this.getDataValue('created_at');
				return moment(date).format('YYYY-MM-DD');
			},
			i18nBody: function (lang) {
				return this.getDataValue('body_' + lang);
			}
		},
		underscored: true
	});
	return News;
}