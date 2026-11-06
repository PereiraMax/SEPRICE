<?php require 'config.php';auth('sala');$uid=$_SESSION['u']['id'];
if(post()){$d=$_POST;$a=$d['a'];
 if(rol()=='recepcion'&&$a=='acred'){q("UPDATE turnos SET estado='espera',cobertura_modo=? WHERE id=? AND estado='asignado'",[$d['cob'],$d['id']]);flash('Paciente acreditado y en sala de espera.');}
 elseif(rol()=='recepcion'&&$a=='acred_o'){q("UPDATE ordenes SET estado='acreditada' WHERE id=? AND estado='pendiente'",[$d['id']]);flash('Paciente acreditado para estudio.');}
 elseif(rol()=='medico'&&in_array($a,['llamar','ausente'])){$ok=one('SELECT t.id FROM turnos t JOIN agenda a ON a.id=t.agenda_id JOIN profesionales p ON p.id=a.profesional_id WHERE t.id=? AND p.usuario_id=?',[$d['id'],$uid]);
  if($ok){q($a=='llamar'?"UPDATE turnos SET estado='en_atencion',hora_llamado=NOW() WHERE id=? AND estado='espera'":"UPDATE turnos SET estado='ausente' WHERE id=? AND estado='espera'",[$d['id']]);flash($a=='llamar'?'Paciente llamado al consultorio.':'Paciente marcado como ausente.');}}
 go('sala.php');}
top('sala');$f=rol()=='medico'?' AND p.usuario_id='.(int)$uid:'';
$L=rows("SELECT t.*,pa.nombre pn,pa.cobertura,a.fecha,p.nombre pr,c.nombre cn FROM turnos t JOIN pacientes pa ON pa.id=t.paciente_id JOIN agenda a ON a.id=t.agenda_id JOIN profesionales p ON p.id=a.profesional_id JOIN consultorios c ON c.id=a.consultorio_id WHERE a.fecha=CURDATE() AND t.estado IN('asignado','espera')$f ORDER BY t.estado='asignado',t.hora");?>
<h2>Turnos de hoy</h2><table><tr><th>Hora<th>Paciente<th>Profesional<th>Consultorio<th>Estado<th></tr>
<?php foreach($L as $r){echo '<tr><td>'.substr($r['hora'],0,5).'<td>'.h($r['pn']).'<td>'.h($r['pr']).'<td>'.h($r['cn']).'<td><span class=tag>'.($r['estado']=='espera'?'en espera':'sin acreditar').'</span><td>';
 if($r['estado']=='asignado'&&rol()=='recepcion')echo '<form method=post><input type=hidden name=a value=acred><input type=hidden name=id value='.$r['id'].'><select name=cob><option>Obra social<option>Particular</select><button>Acreditar</button></form>';
 if($r['estado']=='espera'&&rol()=='medico')echo '<form method=post><input type=hidden name=id value='.$r['id'].'><button name=a value=llamar>Llamar</button><button class=rojo name=a value=ausente>Ausente</button></form>';}?></table>
<?php if(rol()=='recepcion'){?><h2>Estudios por orden de llegada</h2><table><tr><th>Paciente<th>Estudio<th>Origen<th></tr>
<?php foreach(rows("SELECT o.*,pa.nombre pn,e.nombre en FROM ordenes o JOIN pacientes pa ON pa.id=o.paciente_id JOIN estudios e ON e.id=o.estudio_id WHERE o.estado='pendiente' ORDER BY o.prioridad DESC,o.id") as $r)echo '<tr><td>'.h($r['pn']).'<td>'.h($r['en']).'<td>'.$r['origen'].($r['prioridad']?' <span class="tag pri">prioritaria</span>':'').'<td><form method=post><input type=hidden name=a value=acred_o><input type=hidden name=id value='.$r['id'].'><button>Acreditar</button></form>';?></table><?php }bot();
