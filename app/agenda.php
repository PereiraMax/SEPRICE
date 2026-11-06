<?php require 'config.php';auth('agenda');
if(post()){$d=$_POST;
 if($d['fin']<=$d['ini'])flash('La hora de fin debe ser posterior al inicio.',0);
 elseif(one('SELECT id FROM agenda WHERE fecha=? AND hora_ini<? AND hora_fin>? AND (consultorio_id=? OR profesional_id=?)',[$d['fecha'],$d['fin'],$d['ini'],$d['cons'],$d['prof']]))flash('Superposición: el consultorio o el profesional ya tienen agenda en ese horario.',0);
 else{q('INSERT INTO agenda(profesional_id,consultorio_id,fecha,hora_ini,hora_fin)VALUES(?,?,?,?,?)',[$d['prof'],$d['cons'],$d['fecha'],$d['ini'],$d['fin']]);
  $m=array_column(rows('SELECT nombre FROM insumos WHERE stock<=minimo'),'nombre');
  flash('Agenda creada y disponible para turnos.'.($m?' Atención, insumos bajo mínimo: '.implode(', ',$m).'.':''),$m?0:1);}
 go('agenda.php');}
top('agenda');?>
<h2>Armar agenda de atención</h2><form method=post class=grid>
<div><label>Profesional</label><select name=prof><?=opts(rows('SELECT id,nombre FROM profesionales WHERE activo=1'))?></select></div>
<div><label>Consultorio / espacio</label><select name=cons><?=opts(rows('SELECT * FROM consultorios'))?></select></div>
<div><label>Fecha</label><input type=date name=fecha required min="<?=date('Y-m-d')?>"></div>
<div><label>Desde</label><input type=time name=ini required></div><div><label>Hasta</label><input type=time name=fin required></div><button>Confirmar agenda</button></form>
<h2>Agendas vigentes</h2><table><tr><th>Fecha<th>Profesional<th>Consultorio<th>Horario</tr><?php foreach(rows('SELECT a.*,p.nombre pn,c.nombre cn FROM agenda a JOIN profesionales p ON p.id=a.profesional_id JOIN consultorios c ON c.id=a.consultorio_id WHERE fecha>=CURDATE() ORDER BY fecha,hora_ini') as $r)echo '<tr><td>'.$r['fecha'].'<td>'.h($r['pn']).'<td>'.h($r['cn']).'<td>'.substr($r['hora_ini'],0,5).' - '.substr($r['hora_fin'],0,5);?></table><?php bot();
