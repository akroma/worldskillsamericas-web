module.exports = function (sequelize, DataTypes) {
	var Skill = sequelize.define('Skill',
	{
		description : DataTypes.STRING
	});
	return Skill;
}