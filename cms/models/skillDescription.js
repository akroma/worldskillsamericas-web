'use strict';

module.exports = function (sequelize, DataTypes) {
  var description = sequelize.define('SkillDescription',
  {
    type: { type: DataTypes.ENUM('what_they_do', 'fields_of_work', 'qualities') },
    lang: { type: DataTypes.ENUM('en', 'es', 'pt') },
    text: DataTypes.TEXT
  }, {
    underscored: true,
    timestamps: false
  });
  return description;
};