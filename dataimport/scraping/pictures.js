var competitors = [
{id: 1, url: 'http://www.ntm.al.senai.br/wsa/midias/213404_29102013_juan_francisco_garces_gomez___competidor_irapuato_ii.jpg'},
{id: 2, url: 'http://www.ntm.al.senai.br/wsa/midias/214330_29102013_diego_almaguer_soto___competidor_irapuato_ii.jpg'},
{id: 3, url: 'http://www.ntm.al.senai.br/wsa/midias/215020_30102013_miguel_angel_torres_carrillo.jpg'},
{id: 4, url: 'http://www.ntm.al.senai.br/wsa/midias/215615_30102013_jessica_martinez_orta.jpg'},
{id: 5, url: 'http://www.ntm.al.senai.br/wsa/midias/221958_30102013_juan_carlos_cabrera_vega.jpg'},
{id: 6, url: 'http://www.ntm.al.senai.br/wsa/midias/233940_30102013_eduardo_israel_garcia_arellano___irapuato_i.jpg'},
{id: 7, url: 'http://www.ntm.al.senai.br/wsa/midias/163928_31102013_socorro_alejandro_guerrero_vazquez___cortazar.jpg'},
{id: 8, url: 'http://www.ntm.al.senai.br/wsa/midias/001114_31102013_jose_de_jesus_baltazar_lopez___silao.jpg'},
{id: 9, url: 'http://www.ntm.al.senai.br/wsa/midias/001533_31102013_jesus_aaron_rodriguez_mayo___silao.jpg'},
{id: 10, url: 'http://www.ntm.al.senai.br/wsa/midias/002124_31102013_usuario.jpg'},
{id: 11, url: 'http://www.ntm.al.senai.br/wsa/midias/002529_31102013_usuario.jpg'},
{id: 12, url: 'http://www.ntm.al.senai.br/wsa/midias/002901_31102013_usuario.jpg'},
{id: 13, url: 'http://www.ntm.al.senai.br/wsa/midias/003334_31102013_usuario.jpg'},
{id: 14, url: 'http://www.ntm.al.senai.br/wsa/midias/003907_31102013_usuario.jpg'},
{id: 15, url: 'http://www.ntm.al.senai.br/wsa/midias/004139_31102013_cecilia_mayorga_001.jpg'},
{id: 16, url: 'http://www.ntm.al.senai.br/wsa/midias/004526_31102013_luis_antonio.jpg'},
{id: 17, url: 'http://www.ntm.al.senai.br/wsa/midias/005053_31102013_113030024_5_sanchez_martinez_kevin_alan___san_felipe.jpg'},
{id: 18, url: 'http://www.ntm.al.senai.br/wsa/midias/005254_31102013_113030022_9_vasquez_rodriguez_erik___san_felipe.jpg'},
{id: 19, url: 'http://www.ntm.al.senai.br/wsa/midias/005733_31102013_113030078_1_rios_melendez_missael___san_felipe.jpg'},
{id: 20, url: 'http://www.ntm.al.senai.br/wsa/midias/001658_15012014_juan_daniel_acevedo_villagomez_conalep_valle_de_santiago_gto._.jpg'},
{id: 21, url: 'http://www.ntm.al.senai.br/wsa/midias/010722_31102013_usuario.jpg'},
{id: 22, url: 'http://www.ntm.al.senai.br/wsa/midias/011502_31102013_aldo_martin_alvarez_vargas___san_jose_iturbide.jpg'},
{id: 23, url: 'http://www.ntm.al.senai.br/wsa/midias/234335_1112013_michael_m_headshot.jpg'},
{id: 24, url: 'http://www.ntm.al.senai.br/wsa/midias/140005_11112013_romina_donoso.jpg'},
{id: 25, url: 'http://www.ntm.al.senai.br/wsa/midias/170952_14112013_servicio_a_restaurante.jpg'},
{id: 26, url: 'http://www.ntm.al.senai.br/wsa/midias/172741_14112013_cocina.jpg'},
{id: 27, url: 'http://www.ntm.al.senai.br/wsa/midias/174200_14112013_eliecer.jpg'},
{id: 28, url: 'http://www.ntm.al.senai.br/wsa/midias/175354_14112013_kevin_electronica.jpg'},
{id: 29, url: 'http://www.ntm.al.senai.br/wsa/midias/180038_14112013_jose_elias_refrigeracion.jpg'},
{id: 30, url: 'http://www.ntm.al.senai.br/wsa/midias/180824_14112013_hector_electricidad.jpg'},
{id: 31, url: 'http://www.ntm.al.senai.br/wsa/midias/183816_18112013_06cp82f.jpg'},
{id: 32, url: 'http://www.ntm.al.senai.br/wsa/midias/185738_18112013_brenda.jpg'},
{id: 33, url: 'http://www.ntm.al.senai.br/wsa/midias/190858_18112013_06cp82f3.jpg'},
{id: 34, url: 'http://www.ntm.al.senai.br/wsa/midias/190516_19112013_christian.jpg'},
{id: 35, url: 'http://www.ntm.al.senai.br/wsa/midias/191733_19112013_sheyla_cocina.jpg'},
{id: 36, url: 'http://www.ntm.al.senai.br/wsa/midias/192929_19112013_jose_ignacio_vargas_miranda.jpg'},
{id: 37, url: 'http://www.ntm.al.senai.br/wsa/midias/194347_19112013_jorge_esteban.jpg'},
{id: 38, url: 'http://www.ntm.al.senai.br/wsa/midias/200030_19112013_jose_manuel_estrada.jpg'},
{id: 39, url: 'http://www.ntm.al.senai.br/wsa/midias/201012_19112013_foto_de_rafael_1.jpg'},
{id: 40, url: 'http://www.ntm.al.senai.br/wsa/midias/203700_19112013_victor_manuel_fonseca.jpg'},
{id: 41, url: 'http://www.ntm.al.senai.br/wsa/midias/133253_20112013_foto_ulises.jpg'},
{id: 42, url: 'http://www.ntm.al.senai.br/wsa/midias/134207_20112013_foto_olman.jpg'},
{id: 43, url: 'http://www.ntm.al.senai.br/wsa/midias/135343_20112013_mario_mendoza.jpg'},
{id: 44, url: 'http://www.ntm.al.senai.br/wsa/midias/131856_26112013_jorge_parada__3.jpg'},
{id: 45, url: 'http://www.ntm.al.senai.br/wsa/midias/134124_26112013_luis_fuentes__3.jpg'},
{id: 46, url: 'http://www.ntm.al.senai.br/wsa/midias/135213_26112013_pedrovalenzuela_control_industrial.jpg'},
{id: 47, url: 'http://www.ntm.al.senai.br/wsa/midias/135938_26112013_diego_cisterna_ciisa__3.jpg'},
{id: 48, url: 'http://www.ntm.al.senai.br/wsa/midias/141012_26112013_clarenas_mec_automotriz.jpg'},
{id: 49, url: 'http://www.ntm.al.senai.br/wsa/midias/143619_26112013_oscar_lucero_salud.jpg'},
{id: 50, url: 'http://www.ntm.al.senai.br/wsa/midias/144324_26112013_german_fuentealba_cad.jpg'},
{id: 51, url: 'http://www.ntm.al.senai.br/wsa/midias/132431_10012014_img_20140110_121547774_hdr_1.jpg'},
{id: 52, url: 'http://www.ntm.al.senai.br/wsa/midias/145704_26112013_nicolas_icel.jpg'},
{id: 53, url: 'http://www.ntm.al.senai.br/wsa/midias/150638_26112013_cesar_valdera_inst_electricas.jpg'},
{id: 54, url: 'http://www.ntm.al.senai.br/wsa/midias/151537_26112013_xabier_villalobos_fontaneria.jpg'},
{id: 55, url: 'http://www.ntm.al.senai.br/wsa/midias/152212_26112013_juan_ojeda_mecatronica.jpg'},
{id: 56, url: 'http://www.ntm.al.senai.br/wsa/midias/153035_26112013_sergio_pasten__2.jpg'},
{id: 57, url: 'http://www.ntm.al.senai.br/wsa/midias/153321_26112013_diego_muñoz_robotica.jpg'},
{id: 58, url: 'http://www.ntm.al.senai.br/wsa/midias/154501_26112013_felipe_leal_robotica.jpg'},
{id: 59, url: 'http://www.ntm.al.senai.br/wsa/midias/155002_26112013_ricardo_vergara_torno_convencional.jpg'},
{id: 60, url: 'http://www.ntm.al.senai.br/wsa/midias/155646_26112013_juan_ponce_electronica.jpg'},
{id: 61, url: 'http://www.ntm.al.senai.br/wsa/midias/170225_5022014_santiago.jpg'},
{id: 62, url: 'http://www.ntm.al.senai.br/wsa/midias/170143_5022014_dsc_2040.jpg'},
{id: 63, url: 'http://www.ntm.al.senai.br/wsa/midias/170015_5022014_dsc_2036.jpg'},
{id: 64, url: 'http://www.ntm.al.senai.br/wsa/midias/170341_5022014_dsc_2038.jpg'},
{id: 65, url: 'http://www.ntm.al.senai.br/wsa/midias/165756_5022014_dsc_2042.jpg'},
{id: 66, url: 'http://www.ntm.al.senai.br/wsa/midias/142650_19122013_facundo_roumec.jpg'},
{id: 67, url: 'http://www.ntm.al.senai.br/wsa/midias/212717_19122013_michael_m_headshot.jpg'},
{id: 68, url: 'http://www.ntm.al.senai.br/wsa/midias/201017_14012014_jossimar_quispe.jpg'},
{id: 69, url: 'http://www.ntm.al.senai.br/wsa/midias/205325_15012014_nelson.jpg'},
{id: 70, url: 'http://www.ntm.al.senai.br/wsa/midias/182255_25012014_jorge_quiñonez_1.jpg'},
{id: 71, url: 'http://www.ntm.al.senai.br/wsa/midias/184216_25012014_ronald_rodolfo_rosales_rivera.jpg'},
{id: 72, url: 'http://www.ntm.al.senai.br/wsa/midias/191107_25012014_gustavo_abelino_isem.jpg'},
{id: 73, url: 'http://www.ntm.al.senai.br/wsa/midias/193011_25012014_kevin_alejandro_montedeoca_velasquez.jpg'},
{id: 74, url: 'http://www.ntm.al.senai.br/wsa/midias/195408_25012014_joselin.jpg'},
{id: 75, url: 'http://www.ntm.al.senai.br/wsa/midias/200530_25012014_rosa_maria.jpg'},
{id: 76, url: 'http://www.ntm.al.senai.br/wsa/midias/201056_25012014_fridel_armando_xicara.jpg'},
{id: 77, url: 'http://www.ntm.al.senai.br/wsa/midias/201450_25012014_jorge_oswaldo_ixtabalan_solis.jpg'},
{id: 78, url: 'http://www.ntm.al.senai.br/wsa/midias/123204_27012014_sergio_amilcar_tum_bo.jpg'},
{id: 79, url: 'http://www.ntm.al.senai.br/wsa/midias/150901_27012014_franky_2.jpg'},
{id: 80, url: 'http://www.ntm.al.senai.br/wsa/midias/030539_28012014_foto.jpg'},
{id: 81, url: 'http://www.ntm.al.senai.br/wsa/midias/203252_27012014_jhony_marcos_chali_lopez_v2.jpg'},
{id: 82, url: 'http://www.ntm.al.senai.br/wsa/midias/142649_28012014_antony_ivan_morales.jpg'},
{id: 83, url: 'http://www.ntm.al.senai.br/wsa/midias/145657_28012014_jonas.jpg'},
{id: 84, url: 'http://www.ntm.al.senai.br/wsa/midias/152949_28012014_jorge_alberto_giron_cajti.jpg'},
{id: 85, url: 'http://www.ntm.al.senai.br/wsa/midias/111529_29012014_jim_michael.jpg'},
{id: 86, url: 'http://www.ntm.al.senai.br/wsa/midias/112526_29012014_edy_ruben.jpg'},
{id: 87, url: 'http://www.ntm.al.senai.br/wsa/midias/113121_29012014_hans.jpg'},
{id: 88, url: 'http://www.ntm.al.senai.br/wsa/midias/113711_29012014_abner.jpg'},
{id: 89, url: 'http://www.ntm.al.senai.br/wsa/midias/114557_29012014_robin.jpg'}
]

var request = require('request').defaults({jar: true});
var login = require('./login');
var fs = require('fs');
var path = require('path');

var user = 'paolobueno';
var pw = 'Senai115'

function log (text) {
	console.log(text);
}

function download (url, path) {
	request(url).pipe(fs.createWriteStream(path));
}

function downloadPictures (competitors) {
	var destinationDirectory = 'D:/projects/wsa/Backend/cms/competitorImages/';
	login(user, pw, function (err, res, body) {
		if (err) {
			log(err);
			return;
		}
		competitors.forEach(function (competitor) {
			var imgPath = path.join(destinationDirectory, competitor.id + '.jpg');
			var url = competitor.url;
			log('downloading ' + url + ' to ' + imgPath);
			download(url, imgPath);
		});
	});
}

downloadPictures(competitors);