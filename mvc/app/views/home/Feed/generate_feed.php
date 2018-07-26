<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 5/23/2018
 * Time: 10:42 PM
 */

$feed_type = $_GET['q'];

$result = $data['result'];


if($feed_type == 'RSS') {

    echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>
<rss version=\"2.0\">\n
<?xml-stylesheet type=\"text/css\" href=\"resources/stylesheets/wrapperFeed.css\"?>\n";

    echo "<channel>
  <title>AuctioX Home Page</title>
  <link>https://www.localhost.com</link>
  <description>Auction Web Site</description>\n";


    foreach ($result as $d) {

        foreach ($d as $s) {
            $id = $s['id'];
            $title = $s['title'];
            $desc = $s['description'];
            $keyw = $s['keywords'];
            $cond = $s['condition'];
            $brand = $s['brand'];
            $country = $s['country'];
            $cur_p = $s['curr_price'];
            $next_p = $s['next_price'];
            $exp_on = $s['expires_on'];

            echo "<br/>  <item>\n";
            echo "      <id>$id</id>
      <title>$title</title>
      <description>$desc</description>
      <keywords>$keyw</keywords>
      <condition>$cond</condition>
      <brand>$brand</brand>
      <country>$country</country>
      <current_price>$cur_p</current_price>
      <next_price>$next_p</next_price>
      <expires_on>$exp_on</expires_on>
      <link>link</link>\n";
            echo "  </item> \n";
        }
    }
    echo "</channel>";

    exit();
}

if($feed_type == 'Atom') {

    echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>
<atom version=\"2.0\">\n
<?xml-stylesheet type=\"text/css\" href=\"/resources/stylesheets/wrapperFeed.css\"?>\n";

    $d = new DateTime();
    $last_update = date('Y-m-d H:i:s', $d->getTimestamp());

    echo "<channel>
  <title>AuctioX Home Page</title>
  <link href = \"https://www.localhost.com\"/>
  <description>Auction Web Site</description>
  <updated>$last_update</updated>\n";

    echo "  
  <author>
    <name>Auctiox</name>
    <email>auctiox@example.com</email>
  </author>\n\n
  <products>";



    foreach ($result as $d) {

        foreach ($d as $s) {
            $id = $s['id'];
            $title = $s['title'];
            $desc = $s['description'];
            $keyw = $s['keywords'];
            $cond = $s['condition'];
            $brand = $s['brand'];
            $country = $s['country'];
            $cur_p = $s['curr_price'];
            $next_p = $s['next_price'];
            $exp_on = $s['expires_on'];

            echo "<br/>  <entry>\n";
            echo "      <id>$id</id>
      <title>$title</title>
      <description>$desc</description>
      <keywords>$keyw</keywords>
      <condition>$cond</condition>
      <brand>$brand</brand>
      <country>$country</country>
      <current_price>$cur_p</current_price>
      <next_price>$next_p</next_price>
      <expires_on>$exp_on</expires_on>
      <link href = link/>\n";
            echo "  </entry> \n";
        }
    }
    echo "</products>\n</channel>";

    exit();
}
