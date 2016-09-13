<?php
global $post;
//print_r($post);

$rows = get_field('comparison',$post->ID);

echo '<article class="memberlevel">'."\n";
	echo '<h2>'.$post->post_title.'</h2>'."\n";
	
  echo '<ul class="memberlevelitems">'."\n";

  foreach ( $rows as $row ) {
  
    echo '<li>'.$row['message'].'</li>'."\n";
  
  }

  echo '</ul>'."\n";
	
	echo '<h3>$'.$post->membership->Amount__c.' <span>annually</span></h3>'."\n";
	
	echo '<div class="memberlevelrecap">'."\n";
		echo wpautop("<strong>Best For:</strong> " . $post->post_content);
	echo '</div>'."\n";
	
	echo '<div class="memberlevelselect"><a href="/signup?mb='.$post->membership->Id.'">Select</a></div>'."\n";	
	
echo '</article>'."\n";

?>
