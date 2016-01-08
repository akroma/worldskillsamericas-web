App WSA2014 - BackEnd
===

Backend for the WSA2014 mobile app

Open Source
---
This project was open sourced aiming for reuse by other competitions in the same WorldSkills model with minimal customization (mostly on the client available at [https://github.com/akroma/worldskillsamericas-mobile-app](https://github.com/akroma/worldskillsamericas-mobile-app)).

Run
---
In order to run the server, execute the following steps:

1. Install [node.js](http://nodejs.org/).
2. Install all js dependencies - `cms\ $ npm install`
3. Run the app inside **\cms** - `cms\ $ node app.js`
4. The app will start at [localhost:3000](http://localhost:3000)

In development mode, *config.js* tries to find out your LAN ip in order to supply an address to uploaded photos, feel free to hardcode the ip in for development.
*productionServer.bat* runs the server in production mode -- syntax for windows systems.

API
---
`/news.json` - News
`/events.json` - Events (unimplemented so far)

### Querystring params

- `since=yyyy-mm-ddThh:mm:ssZ` - Entities since date given, the T is a literal, using GMT+0
- `lang=[pt|en|es]` - Locale for `body`
- `groupByDay=true` - groups results by day, only for events

A development version of the server app runs at [http://wsaapp.suicobrasileira.com.br:3000/]()

Entities
---

### News
Has the following structure:
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

### Events
Has the following structure (with `lang=en`):

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
