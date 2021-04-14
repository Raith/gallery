# Image gallery

Proof of concept image gallery.

Live version of this project can be found here: https://infinitegallery.herokuapp.com 

Features:
* Responsive design via Bootstrap 5.
* Images are sorted alphabetically by their name (case is ignored).
* Two columns (order of images, starting with left, from left to right; ie: left contains even (0,2,4...) and right all odd (1,3,5...) images in alphabetical order).
* Infinite scroll or pseudo-pagination™. Default: infinite scroll.
  * Infinite scroll can be enabled on any page, however that page becomes the start page for infinite scroll.
  * If infinite scroll is then disabled, pagination returns to that starting page.
  * Bottom pagination navigation buttons scroll to the top of the page.
  * There are 20 images per page in pagination mode.
  * Infinite scroll loads more images (in batches of 20) once the bottom of the page has been reached.
* Two layouts (images are either stacked closer together or are equally distributed with more whitespace in between them). Default: stacking.
  * Layout can be switched with either infinite scroll or pagination.
  * If switching with infinite scroll, only the infinite scroll starting page remains, any further pages are not shown and must be loaded again.
* Both switch (presentation and layout) settings are not saved if changed and are provided for demonstration purposes only.
* Can upload new images either directly to the server or via Imgur.com API.
  *  Directly uploaded images are given a unique name.
  *  Imgur.com API returns a unique link that is saved to file.
  *  Both the name and link are used to insert new images into the live gallery at their correct alphabetical positions, moving other images further down if needed.
  *  Unlike toggle settings, uploaded images will survive a page refresh.
* Project comes with 50 locally available images and 6 images already uploaded to Imgur.com. All images are courtesy of https://search.creativecommons.org/.
  * Preloaded local images have 4 character names.
  * All Imgur.com images at the moment have 7 character names.
  * Directly uploaded images will have 16 character names.

**Note**: Server-side (PHP) is not designed to withstand misuse and does not strive to provide sanity checks for all (edge) cases.
