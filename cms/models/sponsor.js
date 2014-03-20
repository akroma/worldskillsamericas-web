'use strict';

module.exports = function (sequelize, DataTypes) {
  var sponsor = sequelize.define('Sponsor',
  {
    name: DataTypes.STRING,
    picture: DataTypes.STRING,
    category: DataTypes.STRING
  }, {
    underscored: true
  });
  return sponsor;
};