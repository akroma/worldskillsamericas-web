'use strict';
var fs        = require('fs'),
  path      = require('path'),
  config    = require('../config'),
  Sequelize = require('sequelize'),
  sequelize = new Sequelize(config.db.name, config.db.user, config.db.password, config.db.options),
  db        = {};

function hasMany (parent, child, alias) {
  if (parent && child) {
    if (alias) {
      parent.hasMany(child, { as: alias });
    } else {
      parent.hasMany(child);
    }
  }
}

fs
  .readdirSync(__dirname)
  .filter(function(file) {
    return (file.indexOf('.') !== 0) && (file !== 'index.js');
  })
  .forEach(function(file) {
    var model = sequelize.import(path.join(__dirname, file));
    db[model.name] = model;
  });

Object.keys(db).forEach(function(modelName) {
  if ('associate' in db[modelName]) {
    db[modelName].associate(db);
  }
});

hasMany(db.SkillCategory, db.Skill);
hasMany(db.Skill, db.Picture);
hasMany(db.Skill, db.SkillDescription);

hasMany(db.Skill, db.Country);
hasMany(db.Country, db.Skill);

db.sequelize = sequelize;
db.Sequelize = Sequelize;

module.exports = db;