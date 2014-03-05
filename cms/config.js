var os = require('os');

exports.port = 3000;
// find out local ip
function localIp () {
	var conn = os.networkInterfaces()['Ethernet'];
	return conn.filter(function (addr) {
		return addr.family == 'IPv4';
	})[0].address;
}
exports.baseUrl = localIp() + ':' + exports.port;

exports.db = {
	user : 'root',
	password : 'Senai115',
	name : 'wsa'
};

exports.imageHost = exports.baseUrl;