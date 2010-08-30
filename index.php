<?php
  $filename = 'config'.DIRECTORY_SEPARATOR.'base.yml';
  if (!file_exists($filename)) {
    $host = $_SERVER['HTTP_HOST'];
    $base_url = 'http://'.$host;
    $url = $base_url.$_SERVER['REQUEST_URI'];
    $uri = parse_url($url);
    $path = $uri['path'];

    //base.yml - base config file
    $f = fopen($filename,'w');
    fputs($f,'base_host: '.$host."\n");
    fputs($f,'base_url: '.$base_url."\n");
    fputs($f,'base_path: '.$path."\n");
    fputs($f,'root_url: '.$base_url.$path."\n");
    fclose($f);

    //.htaccess
    $f = fopen('.htaccess','w');
    fputs($f,"Options +FollowSymLinks -Indexes\n\n");
    fputs($f,"<IfModule mod_rewrite.c>\n");
    fputs($f,"  RewriteEngine On\n");
    fputs($f,"  RewriteBase $path\n\n");
    fputs($f,"  RewriteCond %{ENV:REDIRECT_STATUS} ^\$\n");
    fputs($f,'  RewriteCond %{REQUEST_URI} ^'.$path."public/index\\.php(\\?path=)?\n");
    fputs($f,"  RewriteRule ^(.*)$ public/index.php [L,QSA]\n\n");
    fputs($f,'  RewriteCond %{REQUEST_URI} !^'.$path."public/index\\.php(\\?path=)?\n");
    fputs($f,"  RewriteRule ^(.*)$ public/index.php [L,QSA]\n");
    fputs($f,"</IfModule>");
    fclose($f);
  }
  unlink('index.php');
  echo 'Setup was done!';
?>
