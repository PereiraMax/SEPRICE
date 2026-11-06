<?php require 'config.php';auth('recursos');
if(post()){$d=$_POST;$n=trim($d['nombre']??'');
 if($d['a']!='baja'&&!$n){flash('Complete el nombre.',0);go('recursos.php');}
 try{if($d['a']=='esp')q('INSERT INTO especialidades(nombre)VALUES(?)',[$n]);
 elseif($d['a']=='cons')q('INSERT INTO consultorios(nombre)VALUES(?)',[$n]);
 elseif($d['a']=='prof'){if(one('SELECT id FROM profesionales WHERE nombre=? AND activo=1',[$n])){flash('El profesional ya existe.',0);go('recursos.php');}q('INSERT INTO profesionales(nombre,especialidad_id,usuario_id,honorario)VALUES(?,?,?,?)',[$n,$d['esp']?:null,$d['usr']?:null,(float)$d['hon']]);}
 elseif($d['a']=='baja')q('UPDATE profesionales SET activo=1-activo WHERE id=?',[$d['id']]);flash('Cambios guardados.');}catch(PDOException $e){flash('No se pudo guardar (¿duplicado?).',0);}go('recursos.php');}
top('recursos');?>
<h2>Profesionales</h2><form method=post class=grid><input type=hidden name=a value=prof><div><label>Nombre</label><input name=nombre></div><div><label>Especialidad</label><select name=esp><option value="">—<?=opts(rows('SELECT * FROM especialidades'))?></select></div>
<div><label>Usuario del sistema</label><select name=usr><option value="">—<?=opts(rows("SELECT id,nombre FROM usuarios WHERE rol IN('medico','tecnico')"))?></select></div><div><label>Honorario por prestación</label><input type=number step=0.01 name=hon value=0></div><button>Agregar</button></form><br>
<table><tr><th>Nombre<th>Especialidad<th>Honorario<th>Estado<th></tr><?php foreach(rows('SELECT p.*,e.nombre en FROM profesionales p LEFT JOIN especialidades e ON e.id=p.especialidad_id') as $r)echo '<tr><td>'.h($r['nombre']).'<td>'.h($r['en']).'<td>$'.$r['honorario'].'<td>'.($r['activo']?'activo':'baja').'<td><form method=post><input type=hidden name=a value=baja><input type=hidden name=id value='.$r['id'].'><button class=rojo>'.($r['activo']?'Dar de baja':'Reactivar').'</button></form>';?></table>
<h2>Especialidades y consultorios</h2><form method=post class=grid><input type=hidden name=a value=esp><div><label>Nueva especialidad</label><input name=nombre></div><button>Agregar</button></form><br>
<form method=post class=grid><input type=hidden name=a value=cons><div><label>Nuevo consultorio / espacio</label><input name=nombre></div><button>Agregar</button></form>
<p>Especialidades: <?=h(implode(', ',array_column(rows('SELECT nombre FROM especialidades'),'nombre')))?><br>Consultorios: <?=h(implode(', ',array_column(rows('SELECT nombre FROM consultorios'),'nombre')))?></p><?php bot();
