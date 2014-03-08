'use strict';

module.exports = function (sequelize, DataTypes) {
  var category = sequelize.define('SkillCategory',
  {
    name: { type: DataTypes.STRING, unique:true },
    color: DataTypes.STRING
  }, {
    underscored: true
  });
  return category;
};