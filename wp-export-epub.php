<?php
/**
 * WordPress User Page
 *
 * Handles authentication, registering, resetting passwords, forgot password,
 * and other user handling.
 *
 * @package WordPress
 */

/** Make sure that the WordPress bootstrap has run before continuing. */
require __DIR__ . '/wp-load.php';
$args = array(
'post_type'=> 'post',
'orderby'    => 'ID',
'post_status' => 'publish',
'order'    => 'ASC',
'posts_per_page' => -1 // this will retrive all the post that is published 
);
$result = new WP_Query( $args );

$write = fopen(__DIR__.'/book.html','w');
fwrite($write,'<html><body>' );
$siteUrl = get_site_url();
$newUrl = '.';
$posts=array();
if ( $result-> have_posts() )  {
   while ( $result->have_posts() ) { 
      $result->the_post();
      $posts[get_the_ID()]=array(
          'title'=>get_the_title()
      );
      fwrite(STDERR, get_the_ID()."\t".get_the_title().PHP_EOL);
      fwrite($write,'<h2 id="p'.get_the_ID().'">'.get_the_title().'</h2><br>' );
      $content = get_the_content();
      $content = str_replace($siteUrl, $newUrl, $content);
      $content = preg_replace('/width: [0-9]*px;/', '', $content);
      $content = '<a href="'.$siteUrl.'?p='.get_the_ID().'">Versione aggiornata</a><br>'.$content; 
      fwrite($write,'<p>'.$content.'</p>' );
   }
}
fwrite($write,'<h2>Indice</h2>' );
fwrite($write,'<ul>' );
foreach($posts as $id=>$post) {
   
   fwrite($write,'<li><a href="#p'.$id.'">'.$post['title'].'</a></li>' );   
}
fwrite($write,'</ul>' );
fwrite($write,'<body></html>' );
fclose($write);
fwrite(STDERR, 'Converting html to epub'.PHP_EOL);
exec('ebook-convert book.html book.epub');