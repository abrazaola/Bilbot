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

Run the servers:
```
docker-compose up
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

* Coffe
