'use strict';
var os = require('os');
// find out local ip
function localIp (interfaceName) {
  var conn = os.networkInterfaces()[interfaceName];
  return conn.filter(function (addr) {
    return addr.family == 'IPv4';
  })[0].address;
}

switch(process.env.node_env) {
  case 'production':
    exports.port = 3000;
    exports.baseUrl = 'wsaapp.suicobrasileira.com.br:' + exports.port;

    exports.db = {
      user : 'root',
      password : 'Senai115',
      name : 'wsa',
      // options: {dialect: 'mysql'}
      options: {
        dialect: 'sqlite',
        storage: __dirname + '/data.db'
      }
    };

    exports.imageHost = exports.baseUrl;
    break;
  default:
    exports.port = 3000;
    exports.interfaceName = 'Ethernet';
    exports.baseUrl =  '172.31.0.12:' + exports.port;
    exports.db = {
      user : 'root',
      password : 'Senai115',
      name : 'wsa',
      // options: {dialect: 'mysql'}
      options: {
        dialect: 'sqlite',
        storage: __dirname + '/data.db'
      }
    };

    exports.imageHost = exports.baseUrl;
    break;
}