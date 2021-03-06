'use strict';

module.exports = function (sequelize, DataTypes) {
  var category = sequelize.define('SkillCategory',
  {
    name_en: { type: DataTypes.STRING, unique:true },
    name_es: { type: DataTypes.STRING, unique:true },
    name_pt: { type: DataTypes.STRING, unique:true },
    name_: { type: DataTypes.STRING, unique:true },
    color: DataTypes.STRING
  }, {
    underscored: true,
    timestamps: false
  });
  return category;
};