<?php require 'config.php';
$reg=isset($_GET['reg']);$err='';$ok='';
if(post()){$u=trim($_POST['usuario']);$c=$_POST['clave'];
 if($reg){$n=trim($_POST['nombre']);
  if($_POST['codigo']!==CODIGO_ALTA)$err='Código de autorización inválido.';
  elseif(!in_array($_POST['rol'],['admin','recepcion','medico','tecnico','guardia']))$err='Rol inválido.';
  elseif(strlen($u)<3||strlen($c)<6||!$n)$err='Complete nombre, usuario (3+ caracteres) y clave (6+).';
  elseif(one('SELECT id FROM usuarios WHERE usuario=?',[$u]))$err='El usuario ya existe.';
  else{q('INSERT INTO usuarios(usuario,clave,nombre,rol)VALUES(?,?,?,?)',[$u,password_hash($c,PASSWORD_DEFAULT),$n,$_POST['rol']]);$ok='Usuario registrado. Ya puede iniciar sesión.';$reg=false;}
 }else{$r=one('SELECT * FROM usuarios WHERE usuario=?',[$u]);
  if($r&&$r['bloqueado'])$err='Cuenta bloqueada tras varios intentos. Contacte a Administración.';
  elseif($r&&password_verify($c,$r['clave'])){q('UPDATE usuarios SET intentos=0 WHERE id=?',[$r['id']]);session_regenerate_id(true);$_SESSION['u']=$r;go('inicio.php');}
  else{if($r)q('UPDATE usuarios SET intentos=intentos+1,bloqueado=(intentos>=5) WHERE id=?',[$r['id']]);$err='Usuario o clave incorrectos.';}}}
?><!doctype html><html lang=es><meta charset=utf-8><meta name=viewport content="width=device-width,initial-scale=1"><title>Clínica SePrise · Ingreso</title><link rel=stylesheet href=style.css>
<body><div class=login><img src=logo.jpg alt="Clínica SePrise">
<form method=post autocomplete=off>
<?php if($err)echo '<div class="msg er">'.h($err).'</div>';if($ok)echo '<div class="msg ok">'.h($ok).'</div>';
if($reg){?><label>Nombre completo</label><input name=nombre required>
<label>Rol</label><select name=rol><option value=recepcion>Recepcionista<option value=medico>Profesional médico<option value=tecnico>Profesional técnico<option value=guardia>Guardia / Internación<option value=admin>Administración</select><?php }?>
<label>Ingrese Usuario</label><input name=usuario required>
<label>Ingrese Clave</label><input name=clave type=password required>
<?php if($reg)echo '<label>Código de autorización de la clínica</label><input name=codigo type=password required>';?>
<div class=acc>
<?php if($reg){?><button class=rojo>Registrar</button><a class=btn href=index.php>Iniciar</a><?php }else{?><a class="btn rojo" href="?reg=1">Registrar</a><button>Iniciar</button><?php }?>
</div></form></div>
