<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');
define('DB_HOST',getenv('DB_HOST')?:'localhost');define('DB_PORT',getenv('DB_PORT')?:'3306');define('DB_NAME',getenv('DB_NAME')?:'seprise');
define('DB_USER',getenv('DB_USER')?:'root');define('DB_PASS',getenv('DB_PASS')?:'');define('CODIGO_ALTA',getenv('CODIGO_ALTA')?:'SEPRISE2026');
set_exception_handler(function($e){http_response_code(500);echo '<p style="font:16px sans-serif;padding:2em">Error: '.htmlspecialchars($e->getMessage()).'<br>¿MySQL está activo en XAMPP y ya ejecutó <a href="install.php">install.php</a>?</p>';});
function db(){static $p;if($p)return $p;$o=[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::MYSQL_ATTR_INIT_COMMAND=>"SET time_zone='-03:00'"];
 if(!in_array(DB_HOST,['localhost','127.0.0.1'])){$ca=getenv('DB_SSL_CA')?:'';foreach(['/etc/ssl/certs/ca-certificates.crt','/etc/pki/tls/certs/ca-bundle.crt','/etc/ssl/cert.pem'] as $c)if(!$ca&&is_file($c))$ca=$c;
  if($ca)$o[PDO::MYSQL_ATTR_SSL_CA]=$ca;else $o[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT]=false;}
 return $p=new PDO('mysql:host='.DB_HOST.';port='.DB_PORT.';dbname='.DB_NAME.';charset=utf8mb4',DB_USER,DB_PASS,$o);}
class DbSess implements SessionHandlerInterface{
 function open($a,$b):bool{return true;} function close():bool{return true;}
 function read($id):string|false{$r=one('SELECT datos FROM sesiones WHERE id=? AND ts>?',[$id,time()-28800]);return $r?$r['datos']:'';}
 function write($id,$d):bool{q('REPLACE INTO sesiones(id,datos,ts)VALUES(?,?,?)',[$id,$d,time()]);return true;}
 function destroy($id):bool{q('DELETE FROM sesiones WHERE id=?',[$id]);return true;}
 function gc($m):int|false{q('DELETE FROM sesiones WHERE ts<?',[time()-28800]);return 1;}}
session_set_save_handler(new DbSess,true);
session_set_cookie_params(['httponly'=>true,'secure'=>!empty($_SERVER['HTTPS']),'samesite'=>'Lax']);
session_start();
function q($s,$a=[]){$t=db()->prepare($s);$t->execute($a);return $t;}
function rows($s,$a=[]){return q($s,$a)->fetchAll();}
function one($s,$a=[]){return q($s,$a)->fetch();}
function h($s){return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');}
function opts($rs,$k='id',$v='nombre',$sel=null){$o='';foreach($rs as $r)$o.='<option value="'.h($r[$k]).'"'.($sel==$r[$k]?' selected':'').'>'.h($r[$v]).'</option>';return $o;}
function go($u){header("Location: $u");exit;}
function flash($m,$ok=1){$_SESSION['f']=[$m,$ok];}
function rol(){return $_SESSION['u']['rol']??'';}
function post(){return $_SERVER['REQUEST_METHOD']=='POST';}
const MENU=['inicio'=>['Inicio',['admin','recepcion','medico','tecnico','guardia']],'pacientes'=>['Pacientes',['recepcion']],'agenda'=>['Armar agenda',['admin']],'turnos'=>['Turnos',['recepcion']],'sala'=>['Acreditación y sala de espera',['recepcion','medico']],'atencion'=>['Atención médica',['medico']],'estudios'=>['Estudios',['recepcion','medico','tecnico','guardia']],'insumos'=>['Insumos',['admin']],'recursos'=>['Profesionales y consultorios',['admin']],'honorarios'=>['Honorarios',['admin']]];
function auth($p){if(empty($_SESSION['u']))go('index.php');if(!in_array(rol(),MENU[$p][1])){http_response_code(403);exit('Acceso no autorizado para su rol.');}}
function slot($aid,$hora,$ex=0){$a=one('SELECT * FROM agenda WHERE id=?',[$aid]);if(!$a)return 'Agenda inexistente.';if($hora<substr($a['hora_ini'],0,5)||$hora>=substr($a['hora_fin'],0,5))return 'El horario está fuera de la agenda.';if(one("SELECT id FROM turnos WHERE agenda_id=? AND hora=? AND estado<>'cancelado' AND id<>?",[$aid,$hora,$ex]))return 'Ese horario ya está ocupado.';return '';}
function agendas(){return rows("SELECT a.id,CONCAT(DATE_FORMAT(a.fecha,'%d/%m'),' · ',p.nombre,' · ',c.nombre,' · ',TIME_FORMAT(a.hora_ini,'%H:%i'),'-',TIME_FORMAT(a.hora_fin,'%H:%i')) n FROM agenda a JOIN profesionales p ON p.id=a.profesional_id JOIN consultorios c ON c.id=a.consultorio_id WHERE a.fecha>=CURDATE() ORDER BY a.fecha,a.hora_ini");}
function top($p){$u=$_SESSION['u'];echo '<!doctype html><html lang=es><meta charset=utf-8><meta name=viewport content="width=device-width,initial-scale=1"><title>Clínica SePrise</title><link rel=stylesheet href=style.css><body><aside><div class=brand>Clínica<b>SePrise</b><small>Sistema de gestión clínica</small></div><nav>';
foreach(MENU as $k=>$m)if(in_array($u['rol'],$m[1]))echo "<a href=$k.php".($k==$p?' class=on':'').">{$m[0]}</a>";
echo '</nav><div class=user>'.h($u['nombre']).'<small>'.h($u['rol']).'</small><a href=logout.php>Salir</a></div></aside><main>';
if(!empty($_SESSION['f'])){[$m,$o]=$_SESSION['f'];unset($_SESSION['f']);echo '<div class="msg '.($o?'ok':'er').'">'.h($m).'</div>';}}
function bot(){echo '</main>';}
