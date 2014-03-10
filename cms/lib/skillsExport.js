'use strict';
var db = require('../models');
var util = require('util');
var _ = require('lodash');

var sql = 'SELECT s.number as number' +
'   , sc.name_es as category' +
'   , sc.id as categoryId' +
'   , sc.color as color' +
'   , p.path as photos' +
'   , c.name_es as countries' +
'   , s.image as image' +
'   , s.name_es as name' +
' FROM skills s' +
' left join countriesSkills cs on s.id = cs.skill_id' +
' left join countries c on cs.country_id = c.id' +
' left join skillcategories sc on s.skill_category_id = sc.id' +
' left join pictures p on p.skill_id = s.id';


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
      skills: {}
    };
  }

  var category = accum[r.categoryId];

  // skills
  if (!category.skills[r.number]) {
    category.skills[r.number] = {
      number: r.number,
      image: r.image,
      countries: [],
      photos: []
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

  return accum;
}

function flattenObject (obj) {
  var result = []
  for (var category in obj) {
    if (obj.hasOwnProperty(category)) {
      result.push(obj[category]);
    }
  }

  return result;
}

db.sequelize.query(sql).success(function (rows) {
  var group = rows.reduce(groupByCategory, {});
  for (var cat in group) {
    if (group.hasOwnProperty(cat)) {
      group[cat].skills = flattenObject(group[cat].skills);
    }
  }

  var result = flattenObject(group);
  console.log(
    util.inspect(
      result, {depth:4, colors: true}));
})