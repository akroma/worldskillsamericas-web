SELECT nome, o.num, p.pais_es, 'http://www.ntm.al.senai.br/wsa/midias/' + m.arquivo as picture_url
FROM usuarios u
	inner join ocupacao o on u.cod_ocupacao = o.cod
	inner join seguranca s on u.nivel_seguranca = s.cod
	inner join paises p on u.pais = p.cod
	inner join midias m on u.cod_midia = m.cod
WHERE u.ativo = 'True'
and s.nivel_seguranca_es = 'Competidores'