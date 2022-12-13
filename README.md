# WP Test

Multisite Wordpress Test Plugin

## Starting

These instructions will allow you to get a copy of the project running on your local machine.

### Pre-requisites 📋

_What do you need to start the project and how to install them_

* Docker [Link](https://docs.docker.com/desktop/)
* Docker Compose [Link](https://docs.docker.com/compose/install/)

### Installation 🔧

Step by step of what you must run to have the project running.

 1. First of all, clone this repository in your system.
   ```
   git clone https://github.com/pcruper/wp-test.git
   ```
 2. Execute the following command on the root of the project
   ```
   docker compose up -d
   ```
 3. Open your browser and navigate to [http://localhost](http://localhost)
 4. Ready, you can now view and interact with the project locally.
 
## Multisite Structure
_You can check the site details section in the following sites_
- Site 1: [Test Site 1](http://localhost/test-site-1)
- Site 2: [Test Site 2](http://localhost/test-site-2)
 
## How to include the Site Details in your site
### Using the [wpt_site_info/] shortcode as a footer widget
Check the result in [Test Site 1](http://localhost/test-site-1)

![image](https://user-images.githubusercontent.com/120252551/206867079-523aa4bd-4b25-4708-ad94-61c8b6e48aaa.png)

### Modifying your theme's footer.php
Check the result in [Test Site 2](http://localhost/test-site-2)

![Selection_427](https://user-images.githubusercontent.com/120252551/206864523-db5cd83b-e3ba-46b1-a8fe-79f0ecdc3a97.png)

### Enabling the "Show in Footer" option
 1. Navigate to "Settings" > "Plugin Settings".
 2. Check the "Show in Footer" option.
 3. Hit Save.

![image](https://user-images.githubusercontent.com/120252551/206864944-6c6b37e4-7828-4291-8d0a-e3cbec6b450e.png)

## Authors ✒️

* **Pedro Cruz** - *Full-stack developer* - GitHub: [pcruper](https://github.com/pcruper/)

## License 📄

This project is licensed under the (GNU General Public License v2.0) - see the [LICENSE.md] file (https://github.com/pcruper/wp-test/blob/master/plugins/wp-test-plugin/LICENSE) for details
