'use strict';
var db = require('../models');
var util = require('util');
var _ = require('lodash');

var lang = 'es';

function getSQL (lang) {
  return "SELECT s.number as number" +
"   , sc.name_" + lang + " as category" +
"   , sc.id as categoryId" +
"   , sc.color as color" +
"   , p.path as photos" +
"   , c.name_" + lang + " as countries" +
"   , s.image as image" +
"   , s.name_" + lang + " as name" +
"   , sd.type as descriptionType" +
"   , sd.text as description" +
"   , sc.[order] as [order]" +
" FROM skills s" +
" left join countriesSkills cs on s.number = cs.skill_id" +
" left join countries c on cs.country_id = c.id" +
" left join skillcategories sc on s.skill_category_id = sc.id" +
" left join pictures p on p.skill_id = s.number" +
" left join skillDescriptions sd on sd.skill_id = s.number" +
" where sd.lang is null or sd.lang = '" + lang + "'" +
" order by sc.[order]";
}


// {
//   'transportation' : {
//     'category': 'transportation',
//     'color': 'c12f23',
//     '33': {
//       'number': '33',
//       'image': '33.jpg'
//       'countries': [ 'guatemala', 'eua' ],
//       'photos': ['33.jpg', '33-1.jpg']
//     }
//   }
// }
function groupByCategory (accum, r) {
  // if cat key doesn't exist, create it
  if (!accum[r.categoryId]) {
    accum[r.categoryId] = {
      category: r.category,
      color: r.color,
      order: r.order,
      skills: {}
    };
  }

  var category = accum[r.categoryId];

  // skills
  if (!category.skills[r.number]) {
    category.skills[r.number] = {
      number: r.number,
      image: r.image,
      name: r.name,
      countries: [],
      photos: [],
      descriptions: {}
    };
  }
  var skill = category.skills[r.number];

  // countries / photos
  if (r.countries) {
    skill.countries.push(r.countries);
  }
  if (r.photos) {
    skill.photos.push(r.photos);
  }

  // descriptions
  skill.descriptions[r.descriptionType] = r.description;

  return accum;
}

function objToArr (obj, transformFn) {
  var arr = [];
  for (var prop in obj) {
    if (obj.hasOwnProperty(prop)) {
      if (transformFn) {
        obj[prop] = transformFn(obj[prop]);
      }
      arr.push(obj[prop]);
    }
  }

  return arr;
}

function cleanupSkill (skill) {
  skill.countries = _.uniq(skill.countries);
  skill.photos = _.uniq(skill.photos);
  return skill;
}

db.sequelize.query(getSQL(lang)).success(function (rows) {
  var group = rows.reduce(groupByCategory, {});
  for (var cat in group) {
    if (group.hasOwnProperty(cat)) {
      var category = group[cat];
      category.skills = objToArr(category.skills, cleanupSkill);
    }
  }

  var result = objToArr(group);
  result = _.sortBy(result, function (cat) {
    return cat.order;
  });
  // console.log(
  //   util.inspect(
  //     result, {depth:4, colors: true}));
  console.log(JSON.stringify(result));
});