App WSA2014 - BackEnd
===

Backend para o aplicativo móvel da WSA2014

Execute
---
Na ordem de execução do servidor, sigua as etapas a seguir:

1. Instale [node.js](http://nodejs.org/).
2. Instale todas as dependencias JS - `cms\ $ npm install`
3. Execute a aplicação em **\cms** - `cms\ $ node app.js`
4. A aplicação irá iniciar em [localhost:3000](http://localhost:3000)

No modo desenvolvedor, *config.js* tente procurar o seu endereço de IP LAN, para que possa trocar o envio de fotos, sinta-se livre para alterar o ip para desenvolvimento.
*productionServer.bat* Execute o servidor em modo de produção -- sintax para sistemas windows.

API
---
`/news.json` - Novidades
`/events.json` - Eventos (unimplemented so far)

### Parametros querystring

- `since=yyyy-mm-ddThh:mm:ssZ` - Entidade since indica a data, o T é um literal, usando GMT+0
- `lang=[pt|en|es]` - Localizado em `body`
- `groupByDay=true` - agrupar resultados por dia, apenas para eventos

A versão de desenvolvimento do aplicativo rodará no servidor [http://wsaapp.suicobrasileira.com.br:3000/]()

Entidades
---

### Notícia
Possui a seguinte estrutura:
``` javascript
{
  "news": [
    {
      "id": 1,
      "title": "Test",
      "image_url": "http://wsaapp.suicobrasileira.com.br:3000/images/12345.png",
      "body_en": "en Lorem ipsum dolor sit amet, consectetur adipisicing elit. Fugit, alias illum temporibus ea perspiciatis dolorum similique nam laborum recusandae. Quis, ab, excepturi modi nemo eligendi dicta ad repudiandae aut facilis.",
      "body_es": "es Lorem ipsum dolor sit amet, consectetur adipisicing elit. Repellat, impedit, eius eaque vero nam illum dignissimos vitae ad temporibus voluptas recusandae facilis placeat quas maiores debitis ipsum qui ab quisquam.",
      "body_pt": "pt Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ipsum, minima repellendus placeat sequi. Veritatis, a, eum, suscipit culpa dolorem deserunt eveniet quos iste adipisci tempora quae ducimus officia nulla quia.",
      "author": "John Doe",
      "created_at": "2014-03-05T13:38:17.000Z",
      "updated_at": "2014-03-05T13:38:17.000Z",
      "date": "2014-03-05",
      "body": "en Lorem ipsum dolor sit amet, consectetur adipisicing elit. Fugit, alias illum temporibus ea perspiciatis dolorum similique nam laborum recusandae. Quis, ab, excepturi modi nemo eligendi dicta ad repudiandae aut facilis."
    }
  ]
}
```

- `image_url`: full url to image resource for the article
- `body_[en|es|pt]`: localized body of the article
- `body`: One of the three above selected using the `lang` querystring param; eg. `lang=en` would make **body == body_en**
- `date`: Publication date of the article, currently based on `created_at`

### Eventos
Possui a seguinte estrutura (em `lang=en`):

``` javascript
{
  "events": [
    {
      "id": 52,
      "body_en": "WSA Ordination",
      "body_es": "WSA Ordenación",
      "body_pt": "WSA Ordenação",
      "body": "WSA Ordination",
      "start": "2014-04-07T08:00:00.000Z",
      "created_at": "2014-03-07T15:48:20.000Z",
      "updated_at": "2014-03-07T15:48:20.000Z"
    },
    {
      "id": 51,
      "body_en": "Return of Delegations",
      "body_es": "Retorno de los Equipos",
      "body_pt": "Retorno das Delegações",
      "body": "Return of Delegations",
      "start": "2014-04-07T07:30:00.000Z",
      "created_at": "2014-03-07T15:48:20.000Z",
      "updated_at": "2014-03-07T15:48:20.000Z"
    }]
}
```

# Licença
Copyright (C) 2012-2016  Akroma Software

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see [http://www.gnu.org/licenses/]().

