App WSA2014 - BackEnd
===

Backend for the WSA2014 mobile app

Run
---
In order to run the server, execute the following steps:

1. Install [node.js](http://nodejs.org/).
2. Install all js dependencies - `cms\ $ npm install`
3. Run the app inside **\cms** - `cms\ $ node app.js`
4. The app will start at [localhost:3000](http://localhost:3000)

API
---
`/news.json` - News
`/events.json` - Events (unimplemented so far)

### Querystring params

- `since=yyyy-mm-ddThh:mm:ss` - Entities since date given, the T is a literal, please use GMT+0
- `lang=[pt|en|es]` - Locale for `body`

A development version of the server app runs at [http://wsaapp.suicobrasileira.com.br:3000/]()

Entities
---

### News
Has the following structure:

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

- `image_url`: full url to image resource for the article
- `body_[en|es|pt]`: localized body of the article
- `body`: One of the three above selected using the `lang` querystring param; eg. `lang=en` would make **body == body_en**
- `date`: Publication date of the article, currently based on `created_at`