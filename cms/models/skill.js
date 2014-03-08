'use strict';

module.exports = function (sequelize, DataTypes) {
  var Skill = sequelize.define('Skill',
  {
    number: { type: DataTypes.INTEGER, primary: true },
    name: { type: DataTypes.STRING, unique:true }
  }, {
    underscored: true
  });
  return Skill;
};