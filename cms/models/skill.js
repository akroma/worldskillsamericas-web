'use strict';

module.exports = function (sequelize, DataTypes) {
  var Skill = sequelize.define('Skill',
  {
    number: DataTypes.STRING, // for skills that have D01 as id
    name_en: { type: DataTypes.STRING, unique:true },
    name_es: { type: DataTypes.STRING, unique:true },
    name_pt: { type: DataTypes.STRING, unique:true },
    image: DataTypes.STRING
  }, {
    underscored: true,
    timestamps: false
  });
  return Skill;
};