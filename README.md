# Bilbot

Design of an extensible chatbots cloud architecture for its application to the improvement of the consumption and the exploitation of public services and data.

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes. See deployment for notes on how to deploy the project on a live system.

### Prerequisites

* [Docker](https://www.docker.com/)

### How to run

This project is fully Dockerized so, the unique thing you have to do is build and run the containers as follows:

Open terminal and type:

```
docker build -t bilbot .
docker run -p 80:80 bilbot
```

For mount the filesystem and be able to edit files while the image is running:

```
docker run -p 80:80 -v /path/to/your/local/src:/var/www/html/ bilbot 
```

End with an example of getting some data out of the system or using it for a little demo

## Running the tests

Explain how to run the automated tests for this system

### Break down into end to end tests

Explain what these tests test and why

```
Give an example
```

### And coding style tests

Explain what these tests test and why

```
Give an example
```

## Deployment

Add additional notes about how to deploy this on a live system

## Built With

* [PHP Telegram Bot](https://github.com/php-telegram-bot) - A Telegram bot framework in PHP
* [IBM Watson](https://www.ibm.com/watson/) - AI engine
* [WeLive](http://welive.eu/) - Datasets sources

## Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/Bilbot/tags). 

## Authors

* **Aitor Brazaola** - *Main developer* - [GitHub](https://github.com/kronosnhz)

## License

This project is licensed under the GPL v3 License - see the [LICENSE.md](LICENSE) file for details

## Acknowledgments

* Coffe
