<?php require 'config.php';auth('atencion');$uid=$_SESSION['u']['id'];
if(post()){$d=$_POST;$t=one("SELECT t.* FROM turnos t JOIN agenda a ON a.id=t.agenda_id JOIN profesionales p ON p.id=a.profesional_id WHERE t.id=? AND t.estado='en_atencion' AND p.usuario_id=?",[$d['id'],$uid]);
 if(!$t||!trim($d['motivo'])){flash('Complete el motivo de consulta.',0);go('atencion.php');}
 q('INSERT INTO atenciones(turno_id,motivo,observaciones,indicaciones)VALUES(?,?,?,?)',[$t['id'],$d['motivo'],$d['obs'],$d['ind']]);
 q("UPDATE turnos SET estado='finalizado' WHERE id=?",[$t['id']]);
 flash('Atención finalizada.'.(!empty($d['seg'])?' Indique al paciente que pase por Recepción para el turno de seguimiento.':''));
 go(!empty($d['est'])?'estudios.php?pac='.$t['paciente_id']:'atencion.php');}
top('atencion');$L=rows("SELECT t.*,pa.nombre pn FROM turnos t JOIN pacientes pa ON pa.id=t.paciente_id JOIN agenda a ON a.id=t.agenda_id JOIN profesionales p ON p.id=a.profesional_id WHERE t.estado='en_atencion' AND p.usuario_id=?",[$uid]);
if(!$L)echo '<h2>Atención médica</h2><p>No hay pacientes llamados. Llámelos desde <a href=sala.php>Sala de espera</a>.</p>';
foreach($L as $r){echo '<h2>'.h($r['pn']).'</h2>';$ant=rows('SELECT at.* FROM atenciones at JOIN turnos t ON t.id=at.turno_id WHERE t.paciente_id=? ORDER BY at.fecha DESC LIMIT 5',[$r['paciente_id']]);
 echo '<p><b>Antecedentes:</b> '.($ant?'':'primera atención, sin registros previos.').'</p>';
 if($ant){echo '<table><tr><th>Fecha<th>Motivo<th>Observaciones</tr>';foreach($ant as $x)echo '<tr><td>'.$x['fecha'].'<td>'.h($x['motivo']).'<td>'.h($x['observaciones']);echo '</table><br>';}?>
<form method=post class=grid><input type=hidden name=id value=<?=$r['id']?>><div><label>Motivo de consulta</label><input name=motivo required></div><div><label>Observaciones</label><textarea name=obs></textarea></div><div><label>Indicaciones</label><textarea name=ind></textarea></div>
<div><label><input type=checkbox name=seg style=width:auto> Requiere turno de seguimiento</label><label><input type=checkbox name=est style=width:auto> Derivar a estudio</label></div><button>Finalizar atención</button></form><?php }bot();
