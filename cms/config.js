var os = require('os');

switch(process.env['node_env']) {
  case 'production':
    var os = require('os');

    exports.port = 3000;
    exports.baseUrl = 'wsaapp.suicobrasileira.com.br:' + exports.port;

    exports.db = {
      user : 'root',
      password : 'Senai822%##',
      name : 'wsa2014'
    };

    exports.imageHost = exports.baseUrl;
    break;
  default:
    exports.port = 3000;
    exports.interfaceName = 'Ethernet';
    // find out local ip
    function localIp () {
      var conn = os.networkInterfaces()[exports.interfaceName];
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
    break;
}