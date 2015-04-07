Bones for Genesis 2.0
==============

My fork of [cdukes](https://github.com/cdukes/bones-for-genesis-2-0) which is a fork of [eddiemachado's](https://github.com/eddiemachado/bones-genesis) Bones for Genesis. Built for Genesis 2.1+ and WordPress 4.1+.


Please see cdukes for great instructions.

Made the following changes so far:

Removed grunt-contrib-sass (I am using Windows and don't have ruby or Sass installed)

Added node-sass ( I do have node.js installed on my machine)
Added grunt-sass

Altered gruntfile.js so that Sass task uses grunt-sass

--NOTE: for some reason the SASS task takes a long time the first time my IDE is up and running (PHPStorm), but
  runs quickly, after the initial run of the task.

Added bootstrap-sass as a bower component (https://github.com/twbs/bootstrap-sass)

Making the front-page.php to load up and display multiple pages as a single web page.

Using the Bootstrap SASS mixins to format the sidebar-content, sidebar-content-sidebar, etc


