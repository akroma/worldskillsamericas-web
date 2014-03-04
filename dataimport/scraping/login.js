var request = require('request').defaults({jar:true});
var wsaUrl = 'http://www.ntm.al.senai.br/wsa';

module.exports = function (user, pw, cb) {
	request.post({
		url: wsaUrl + '/access.asp', 
		form: {login: user, senha: pw}}
		, cb)
}