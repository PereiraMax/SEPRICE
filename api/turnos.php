<?php require 'config.php';auth('turnos');
if(post()){$d=$_POST;$a=$d['a'];
 if($a=='alta'){$e=slot($d['agenda'],$d['hora']);if($e)flash($e,0);else{q('INSERT INTO turnos(paciente_id,agenda_id,hora)VALUES(?,?,?)',[$d['pac'],$d['agenda'],$d['hora']]);flash('Turno asignado.');}}
 elseif($a=='cancel'){q("UPDATE turnos SET estado='cancelado' WHERE id=? AND estado='asignado'",[$d['id']]);flash('Turno cancelado y disponibilidad liberada.');}
 elseif($a=='repro'){$e=slot($d['agenda'],$d['hora'],$d['id']);if($e)flash($e,0);else{q("UPDATE turnos SET agenda_id=?,hora=? WHERE id=? AND estado='asignado'",[$d['agenda'],$d['hora'],$d['id']]);flash('Turno reprogramado.');}}
 go('turnos.php');}
top('turnos');$ag=agendas();$pa=rows('SELECT id,CONCAT(nombre," (",dni,")") n FROM pacientes ORDER BY nombre');?>
<h2>Asignar turno</h2><?php if(!$pa)echo '<p>No hay pacientes: registre uno primero en <a href=pacientes.php>Pacientes</a>.</p>';?>
<form method=post class=grid><input type=hidden name=a value=alta>
<div><label>Paciente</label><select name=pac><?=opts($pa,'id','n')?></select></div><div><label>Agenda disponible</label><select name=agenda><?=opts($ag,'id','n')?></select></div>
<div><label>Hora</label><input type=time name=hora step=900 required></div><button>Asignar turno</button></form>
<h2>Turnos</h2><table><tr><th>Fecha<th>Hora<th>Paciente<th>Profesional<th>Estado<th>Gestión</tr>
<?php foreach(rows("SELECT t.*,pa.nombre pn,a.fecha,p.nombre pr FROM turnos t JOIN pacientes pa ON pa.id=t.paciente_id JOIN agenda a ON a.id=t.agenda_id JOIN profesionales p ON p.id=a.profesional_id WHERE a.fecha>=CURDATE() ORDER BY a.fecha,t.hora") as $r){
echo '<tr><td>'.$r['fecha'].'<td>'.substr($r['hora'],0,5).'<td>'.h($r['pn']).'<td>'.h($r['pr']).'<td><span class=tag>'.$r['estado'].'</span><td>';
if($r['estado']=='asignado')echo '<form method=post><input type=hidden name=id value='.$r['id'].'><button class=rojo name=a value=cancel>Cancelar</button></form><form method=post><input type=hidden name=a value=repro><input type=hidden name=id value='.$r['id'].'><select name=agenda>'.opts($ag,'id','n',$r['agenda_id']).'</select><input type=time name=hora step=900 required><button>Reprogramar</button></form>';}?></table><?php bot();
