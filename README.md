## Setup Deployment

Install the Heroku CLI:

```sh
sudo snap install --classic heroku
```
Sign Up and create app:

```sh
heroku apps:create cp-twilio
```
Login and Push:

```sh
heroku login
heroku git:clone -a cp-twilio
```
Setup dependency files for Heroku PHP App:

```sh
echo '<?php include_once("index.html"); ?>' > index.php
echo '{}' > composer.json
```
