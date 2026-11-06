<?php
// Único punto de entrada (Vercel Hobby permite máx. 12 funciones): enruta a /app/<pagina>.php
$p=$_GET['p']??'index';
if(!preg_match('/^[a-z]+$/',$p)||$p==='config'||!is_file(__DIR__.'/../app/'.$p.'.php')){http_response_code(404);exit('Página no encontrada.');}
chdir(__DIR__.'/../app');
require __DIR__.'/../app/'.$p.'.php';
