Bones for Genesis 2.0 with Bootstrap integration
==============

My fork of [cdukes](https://github.com/cdukes/bones-for-genesis-2-0) which is a fork of [eddiemachado's](https://github.com/eddiemachado/bones-genesis) Bones for Genesis. Built for Genesis 2.1+ and WordPress 4.1+.


Please see cdukes for great instructions.

##Changes so far:##

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


##Recent Fixes##

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


