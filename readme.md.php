<?php if (!defined('FW')) die('Forbidden'); ?>

##STEP 1

###Copy the breadcrumbs code

This code is what displays the breadcrumbs on your website. Copy the following to your clipboard:

<code>&lt;?php if( function_exists('fw_ext_breadcrumbs') ) { fw_ext_breadcrumbs(); } ?&gt;</code>

---

##STEP 2

###Paste the breadcrumbs code in your theme

Open Appearance/Editor and select <strong>single.php</strong> file to edit.

In the theme, paste the code where you want your breadcrumbs to appear (usually beneath the the_title() tag) and then save your theme.

---

##STEP 3

###Add the breadcrumbs to your archive listings

Copy the following code to your clipboard:

<code>&lt;?php if( function_exists('fw_ext_breadcrumbs') ) { fw_ext_breadcrumbs(); } ?&gt;</code>

Open Appearance/Editor and select <strong>archive.php</strong> file to edit.

In the theme, find the place where each item is rendered and paste the code inside that code block.

Then, save your theme.
