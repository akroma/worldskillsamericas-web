'use strict';

module.exports = function (sequelize, DataTypes) {
  var Picture = sequelize.define('Picture',
  {
    path: { type: DataTypes.STRING, unique:true }
  }, {
    underscored: true,
    timestamps: false
  });
  return Picture;
};