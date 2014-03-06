SELECT distinct o.num, o.ocupacao_br, o.ocupacao_en, o.ocupacao_es, p.pais_br, p.pais_en, p.pais_es
FROM usuarios u
  inner join ocupacao o on u.cod_ocupacao = o.cod
  inner join seguranca s on u.nivel_seguranca = s.cod
  inner join paises p on u.pais = p.cod
WHERE u.ativo = 'True'
and s.nivel_seguranca_es = 'Competidores'