# Bilbot

Design of an extensible chatbots cloud architecture for its application to the improvement of the consumption and the exploitation of public services and data.

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes. See deployment for notes on how to deploy the project on a live system.

### Prerequisites

* [Docker](https://www.docker.com/)
* [Docker compose](https://docs.docker.com/compose/)
* [Capistrano](http://capistranorb.com/)

### How to run

This project is fully Dockerized and orchestrated with Docker Compose.

Build the images:

```
docker-compose build
```

Run the services:
```
docker-compose up -d
```

Prepare the environment:

1. Log in to the `watson-php` container by running the following command:
    ```
    docker exec -it bilbotpfm_watson-php_1 bash
    ```

2. Install dependencies by running the following command:
    ```
    cd /var/www/docker-symfony && composer install -n
    ```

3. Change `/tmp` dir permissions:
    ```
    chown -R www-data:www-data /tmp
    ```

Open Telegram and search the bot `@bilb0_bot` to start a conversation.

Get fun with him asking information!

### If you want to access directly to microservices from your computer update your hosts

#### Mac OS X

Add the following line to your `/etc/hosts` file:
```
127.0.0.1 bilbot.dev
```

#### Windows

Add the following line to your `%SystemRoot%\System32\drivers\etc\hosts` file:
```
127.0.0.1 bilbot.dev
```

The web server is constantly querying the Telegram API for new messages sent to Bilbot, for stopping the containers:
```
docker-compose down
```

To remove the built image for starting from scratch:
```
docker-compose down
docker-compose rm
```

All the received messages are stored in a MySQL Database which can be accessed in localhost.

## Microservices

| Name | Address | Description|
| --- | --- | --- |
| bilbot |bilbot.dev:80 | Contains the Telegram bot API logic and the user commands. |
| bilbot-watson |bilbot.dev:81 | Contains the Watson API querying logic and is based on Symfony. Contains a testing index page. |
| bilbot-welive |bilbot.dev:82 | Contains the WeLive API querying logic and is based on Symfony. Contains a testing index page. |

## Deployment

The deployment process is orchestrated by Capistrano, a tool written in ruby to encapsulate each environment 
configuration specifications.

For deploying a new version in dev:

```
cap dev deploy
```

## Built With

* [PHP Telegram Bot](https://github.com/php-telegram-bot) - A Telegram bot framework in PHP
* [IBM Watson](https://www.ibm.com/watson/) - AI engine
* [WeLive](http://welive.eu/) - Datasets sources
* [Symfony](https://symfony.com/) - Web Framework

## Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/Bilbot/tags). 

## Authors

* **Aitor Brazaola** - *Main developer* - [GitHub](https://github.com/kronosnhz)

## License

This project is licensed under the GPL v3 License - see the [LICENSE.md](LICENSE) file for details

## Acknowledgments

* [docker-symfony](https://github.com/sskorc/docker-symfony) - Szymon Skórczyński
* [Example Bot Telegram](https://github.com/php-telegram-bot/example-bot) - PHP Telegram Bot Developers
* Coffe & Daft Punk
