var should = require('should');
var login = require('../../login');
var request = require('request').defaults({jar:true});

describe('login', function () {
	it('[slow] should login successfully', function (done) {
		login('paolobueno', 'Senai115', function (err, res, body) {
			res.statusCode.should.equal(302);
			done();
		});
	});

	it('[slow] should allow access to admin area', function (done) {
		request( 'http://www.ntm.al.senai.br/wsa/homeadministration_list.asp', function (err, res, body) {
			should(err).not.be.ok;
			res.statusCode.should.equal(200);
			done()
		});
	});
});