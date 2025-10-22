<?php
/**
 * Strato Environment → Docker Mirror Generator
 * Upload as env-check.php, open in browser.
 * Download: ?raw=Dockerfile | ?raw=php.ini | ?raw=apache.conf | ?raw=htaccess
 */

function html($s){return htmlspecialchars($s,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');}
function detect_php_tag(){return PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;}
function exts(){ $e=get_loaded_extensions(); sort($e,SORT_NATURAL|SORT_FLAG_CASE); return $e; }

function infer_apt_packages(array $exts):array{
  $map=[
    'gd'=>['libjpeg-dev','libpng-dev','libfreetype6-dev'],
    'intl'=>['libicu-dev'],
    'zip'=>['libzip-dev'],
    'xml'=>['libxml2-dev'],
    'mbstring'=>['libonig-dev'],
    'pgsql'=>['libpq-dev'],
    'ldap'=>['libldap2-dev'],
    'curl'=>['libcurl4-openssl-dev'],
  ];
  $deps=['git','ca-certificates','unzip'];
  foreach($map as $ext=>$pkgs) if(in_array($ext,$exts,true)) $deps=array_merge($deps,$pkgs);
  return array_unique($deps);
}

function dockerfile(array $exts):string{
  $tag=detect_php_tag();
  $apt=infer_apt_packages($exts);
  $lines=[
    "FROM php:{$tag}-apache",
    "ENV DEBIAN_FRONTEND=noninteractive",
    "RUN apt-get update && apt-get install -y --no-install-recommends \\",
    "    ".implode(" \\
    ",$apt)." \\",
    " && rm -rf /var/lib/apt/lists/*",
    "RUN a2enmod rewrite headers expires",
    "WORKDIR /var/www/html",
    "COPY public/ /var/www/html/",
    "COPY .docker/php.ini /usr/local/etc/php/conf.d/zz-overrides.ini",
    "COPY apache.conf /etc/apache2/sites-available/000-default.conf",
    "EXPOSE 80",
    "CMD [\"apache2-foreground\"]",
  ];
  return implode("\n",$lines)."\n";
}

function phpini():string{
  $keys=['memory_limit','upload_max_filesize','post_max_size','max_execution_time',
         'max_input_time','display_errors','error_reporting','default_charset',
         'file_uploads','allow_url_fopen','date.timezone'];
  $out=[];
  foreach($keys as $k){$v=ini_get($k);if($v!==false)$out[]="$k=$v";}
  return implode("\n",$out)."\n";
}

function apacheconf():string{
return <<<APACHE
<VirtualHost *:80>
    ServerName localhost
    DocumentRoot /var/www/html

    <Directory /var/www/html>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/error.log
    CustomLog \${APACHE_LOG_DIR}/access.log combined

    Header always set X-Content-Type-Options "nosniff"
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-XSS-Protection "1; mode=block"
</VirtualHost>
APACHE;
}

function htaccess():string{
return <<<HT
Options -Indexes
RewriteEngine On

# SPA routing (optional)
#RewriteCond %{REQUEST_FILENAME} !-f
#RewriteCond %{REQUEST_FILENAME} !-d
#RewriteRule . /index.html [L]

<IfModule mod_headers.c>
    Header set X-Content-Type-Options "nosniff"
    Header set X-Frame-Options "SAMEORIGIN"
    Header set Referrer-Policy "strict-origin-when-cross-origin"
</IfModule>
HT;
}

// Raw downloads
$raw=$_GET['raw']??null;
if($raw){
  header('Content-Type: text/plain; charset=utf-8');
  header("Content-Disposition: attachment; filename=$raw");
  switch($raw){
    case 'Dockerfile': echo dockerfile(exts()); break;
    case 'php.ini': echo phpini(); break;
    case 'apache.conf': echo apacheconf(); break;
    case 'htaccess': echo htaccess(); break;
    default: http_response_code(404); echo "Unknown file.";
  }
  exit;
}

// HTML report
$exts=exts();
?><!doctype html>
<html lang="en"><meta charset="utf-8">
<title>Strato → Docker Environment Mirror</title>
<style>
body{font-family:system-ui,Segoe UI,Roboto,Arial;margin:2rem;background:#0f172a;color:#e5e7eb}
.card{background:#111827;border:1px solid #374151;border-radius:14px;padding:1rem;margin:1rem 0}
pre{background:#0b1220;padding:1rem;border-radius:12px;overflow:auto}
a.btn{background:#2563eb;color:#fff;text-decoration:none;padding:.5rem 1rem;border-radius:8px;margin-right:.5rem}
</style>
<h1>Strato Environment → Docker Mirror</h1>

<div class="card">
<p><b>PHP Version:</b> <?=html(PHP_VERSION)?></p>
<p><b>Server:</b> <?=html($_SERVER['SERVER_SOFTWARE']??'N/A')?> (<?=html(PHP_OS_FAMILY)?>)</p>
<p><b>Generated:</b> <?=html(date('Y-m-d H:i:s'))?></p>
</div>

<h2>Generated Files</h2>
<div class="card">
  <a class="btn" href="?raw=Dockerfile">Dockerfile</a>
  <a class="btn" href="?raw=php.ini">php.ini</a>
  <a class="btn" href="?raw=apache.conf">apache.conf</a>
  <a class="btn" href="?raw=htaccess">.htaccess</a>
</div>

<h3>Preview: Dockerfile</h3>
<div class="card"><pre><?=html(dockerfile($exts))?></pre></div>

<h3>Preview: php.ini</h3>
<div class="card"><pre><?=html(phpini())?></pre></div>

<h3>Preview: apache.conf</h3>
<div class="card"><pre><?=html(apacheconf())?></pre></div>

<h3>Preview: .htaccess</h3>
<div class="card"><pre><?=html(htaccess())?></pre></div>
</html>
