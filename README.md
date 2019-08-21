# <img height="50" valign="middle" src="app/assets/images/brand/bettergram-logo.svg"> BetterGram

BetterGram is a simple yet powerful web application to share your photos with others.

## A few words of introduction:
This application was created solely by me, without the use of any back-end frameworks, as a school project for a subject called web applications.

There were specific requirements for the project, thus, some aspects of the application, mainly UI-related, can be inconsistent.

The development process started in November 2018 and the first publicly available production version was released in February 2019.

## Some of the technologies used in BetterGram:
* HTML
* PHP
* JavaScript, jQuery, AJAX, ECMAScript, Node.js
* NPM, Webpack, Babel, Modernizr
* CSS, SCSS
* Bootstrap
* MySQL

## Integrated APIs:
* Google reCAPTCHA
* Google Analytics
* Gravatar

## Prerequisites:
During the development I used [XAMPP](https://www.apachefriends.org/) package which integrates:
* PHP
* Apache
* MySQL

If you prefer not to use XAMPP, then you will have to configure all of these applications by yourself.

Moreover, in order to bundle JavaScript or compile SCSS files, it is necessary to install NPM which comes together with [Node.js](https://nodejs.org/).

## How to set up locally?
1. Clone the repository
	```
	$ git clone "https://github.com/jonaszkadziela/bettergram.git"
	```

1. Enter `bettergram` project directory
	```
	$ cd bettergram
	```

1. Install all of the dependencies
	```
	$ npm install
	```

1. Run this command so that all `package.json` scripts work properly
	```
	$ npm install -g npm-check-updates
	```

1. Build SCSS & JavaScript assets
	```
	$ npm run build
	```

1. Setup the environment variables by copying an example `env` file, which is located in the root directory of the project. Be sure to adjust the contents of this file to your needs
	```
	$ cp env.example.php env.php
	```

1. If you are using XAMPP, move the whole `bettergram` folder to the `xampp/htdocs` directory. Otherwise, move `bettergram` folder to the root folder of your local web server

1. Import a MySQL database, which is located in a folder `database_schema`. If you are using XAMPP, then you can easily achieve this thanks to the included `phpMyAdmin` panel

1. Run Apache and MySQL services. If you are using XAMPP, then you can do this using the included `XAMPP Control Panel`

## Links:
* [Live BetterGram website](http://bettergram.jonaszkadziela.pl/)
