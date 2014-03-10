'use strict';

module.exports = function (sequelize, DataTypes) {
  var country = sequelize.define('Country',
  {
    name_en: { type: DataTypes.STRING, unique:true },
    name_es: { type: DataTypes.STRING, unique:true },
    name_pt: { type: DataTypes.STRING, unique:true },
    picture: DataTypes.STRING,
  }, {
    underscored: true,
    timestamps: false
  });
  return country;
};