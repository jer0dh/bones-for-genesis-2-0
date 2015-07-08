Bones for Genesis 2.0 with Bootstrap integration
==============

My fork of [cdukes](https://github.com/cdukes/bones-for-genesis-2-0) which is a fork of [eddiemachado's](https://github.com/eddiemachado/bones-genesis) Bones for Genesis. Built for Genesis 2.1+ and WordPress 4.1+.
Added Bootstrap SASS and integrating bootstrap components.

Please also see cdukes for great instructions on installing.

Here are some instructions if using phpStorm as your development environment, but the basic steps can be gleaned here for other types:

#Installation#
- Open phpStorm
- In Quick Start choose "Check out from Version Control" then GitHub
or if in a project: Tools | VCS | Checkout from Version Control | GitHub

```
  Git Repository URL: https://github.com/jer0dh/bones-for-genesis-2-0-bootstrap.git
  Parent Directory: ....\wp-content\themes
  Theme name: Themename
```
-Open a Terminal in the path of your project from Tools | Run Command and then below the command window, I choose the option Terminal.  Now run the following command

```
npm update --save-dev && bower update --save
```

(this assumes you already have node.js, grunt.js,  and bower.js installed).  This may take awhile as it downloads all the dependencies to develop.

- Once complete, go to Tools | Open Grunt Console and run the Sass job and the concat job to build the css and js files.

- Make sure your server has the Genesis Framework . The theme could now be used in WordPress, but with only the default settings.  You will probably want to alter some of the SASS variables to change colors and layout sizes.  Then until the admin interface is built, you'll want to go to the includes/structure/menu.php to change which type of nav you want and where you want it.

- Add a logo.png in the images folder if using the header image.

- Add bootstrap.js and bootstrap.min.js to the js folder in the root of the project.

- To deploy, run the grunt build job that will also minify the css and javascript and in WordPress dashboard go to Genesis | Theme settings to check use Production Assets to use the minified versions.  You should only need the build, fonts, images, includes, js, and svgs directories on your production server, along with any css, php files in the root of this project.

##2015-07-08##
-Fixed path to bootstrap min CDN when production assets are used (header.php)

##2015-05-22##
- Version 2.4.3
- added carousel shortcode

##2015-05-13##
- Version 2.4.2
- changed default values for column size of title_area and header widget as a percentage of $grid-columns;
- Added .nav.genesis-nav-menu {width:inherit;} to override genesis css as it interfered with navbar-right
- Made bootstrap custom menu data-toggle to collapse a unique value if multiple navbar widgets
- Added navbar-transparent to _navigation.scss to be able to select transparent background and no borders
- Add navbar style selector for Bootstrap nav menu widget so one can select navbar-default or navbar-transparent


##2015-05-12 Changes##
- Fixed that boostrap-sass not added as dependency in bower.json
- Added download and setup instructions in Readme.md

##2015-05-11 Changes##
- Version 2.4.1
- Adding Glyphicons


##2015-05-05 Changes
- Version 2.4.0
- Creating 5 different options for placement of primary navigation.  All options allowing navbar, nav-pills, or nav-tabs
- Creating a Bootstrap Custom Menu widget.  Adding options to allow navbar, nav-pills, or nav-tabs.  Title of widget also used to create custom filters for further modification.
- menu.php code cleanup


##2015-04-01 Changes so far:##

- Removed grunt-contrib-sass (I am using Windows and don't have ruby or Compass installed)
- Added node-sass ( I do have node.js installed on my machine)
- Added grunt-sass
- Altered gruntfile.js so that Sass task uses grunt-sass
  - NOTE: for some reason the SASS task takes a long time the first time my IDE is up and running (PHPStorm), but runs quickly, after the initial run of the task.
- Added bootstrap-sass as a bower component (https://github.com/twbs/bootstrap-sass)
- Making the front-page.php to load up and display multiple pages as a single web page.
- Using the Bootstrap SASS mixins to format the sidebar-content, sidebar-content-sidebar, etc to use grid.
- Creating SASS variables so user can alter the number of grids, width of content and sidebars, and width of offset.
- Bootstrap.js is enqueued with it's CDN path.  A fallback to a local bootstrap.js file is also added if CDN not available.
- Integrated Bootstrap menus to primary navigation
  - in includes\structure\menu.php the user can select nav-tabs, nav-pills, or navbar.
  - if navbar, then static-top, fixed-top, fixed-bottom, navbar-left, and/or navbar-right can be chosen
  - Added filters so one can add navbar-brand and/or navbar-search
  - navbar-brand will check height of image in php and set line-heights accordingly to vertically center nav links
- Changing WordPress Genesis Pagination markup to match Bootstrap's
  - for Numeric pagination it follows `<ul class="pagination">` markup. In the includes\structure\post.php, one can also change the range the pagination nav will show.
  - for Previous/Next pagination it follows the `<ul class="pager">` markup.
-Changing Breadcrumbs to match Bootstrap's markup


##Previous Fixes##

- Testing nav in header.. Determined may need to extend custom_menu widget.
- Testing navbar as header with brand.  Works.
- Added to _navigation.scss to move fixed-top nav down if wp admin bar is visible.
```css
body.admin-bar.fixed-top nav.navbar-fixed-top{
  top: 32px;
}
```
- Fixed errors if there was no menu set as Primary Navigation
- Fixed padding issue on .site-inner for some pages.
- Fixed padding issue on nav.navbar-fixed-top, nav.navbar-fixed-bottom
- Fixed nav-brand position in navbar
- Fixed nav-brand height issue and navbar anchor heights on mobile view


Possible Bugs
20150512 - Find using W3 Total Cache with JS minified, the fallbacks are not written to the DOM correctly:
- String for Document.write('...') produces "SyntaxError: unterminated string literal"