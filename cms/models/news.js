var moment = require('moment');

module.exports = function (sequelize, DataTypes) {
	var News = sequelize.define('News',
	{
		title_en: DataTypes.TEXT,
		title_es: DataTypes.TEXT,
		title_pt: DataTypes.TEXT,
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
				if (lang == 'pt'
					|| lang == 'es'
					|| lang == 'en') {
					return this.getDataValue('body_' + lang);
				}
				return this.getDataValue('body_es');
			},
			i18nTitle: function (lang) {
				if (lang == 'pt'
					|| lang == 'es'
					|| lang == 'en') {
					return this.getDataValue('title_' + lang);
				}
				return this.getDataValue('title_es');
			}
		},
		underscored: true
	});
	return News;
}