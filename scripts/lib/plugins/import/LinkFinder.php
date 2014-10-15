<?php
$plugin = function($goods){
      
  if (preg_match('~Ссылка:\sT\[(.*)\]~Ui', $goods->data['Features'], $mathes)) {
    $goods->{'Origin goods url'} = $mathes[1];
  }
  
};