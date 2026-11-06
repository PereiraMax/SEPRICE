<?php require 'config.php';auth('estudios');$uid=$_SESSION['u']['id'];
if(post()){$d=$_POST;
 if($d['a']=='orden'&&rol()!='tecnico'){$p=in_array($d['origen'],['guardia','internacion'])?1:0;q('INSERT INTO ordenes(paciente_id,estudio_id,origen,prioridad,motivo)VALUES(?,?,?,?,?)',[$d['pac'],$d['est'],$d['origen'],$p,$d['motivo']]);flash('Orden registrada'.($p?' con prioridad.':'. Si requiere turno, gestiónelo en Turnos.'));}
 elseif($d['a']=='real'&&rol()=='tecnico'){$o=one("SELECT * FROM ordenes WHERE id=? AND estado='acreditada'",[$d['id']]);
  if(!$o)flash('La orden no está acreditada.',0);
  elseif($d['res']=='realizado'){$ins=$d['ins']?one('SELECT * FROM insumos WHERE id=?',[$d['ins']]):null;$c=max(1,(int)$d['cant']);
   if($ins&&$ins['stock']<$c)flash('Falta de insumos críticos: stock insuficiente de '.$ins['nombre'].'.',0);
   else{if($ins)q('UPDATE insumos SET stock=stock-? WHERE id=?',[$c,$ins['id']]);q("UPDATE ordenes SET estado='realizado',fecha_real=NOW(),tecnico_id=?,prof_id=? WHERE id=?",[$uid,$d['prof']?:null,$o['id']]);go('comprobante.php?id='.$o['id']);}}
  else{q("UPDATE ordenes SET estado='no_realizado',fecha_real=NOW(),tecnico_id=?,motivo=? WHERE id=?",[$uid,$d['motivo'],$o['id']]);flash('Estudio registrado como no realizado.');}}
 go('estudios.php');}
top('estudios');?>
<?php if(rol()!='tecnico'){?><h2>Registrar orden de estudio</h2><form method=post class=grid><input type=hidden name=a value=orden>
<div><label>Paciente</label><select name=pac><?=opts(rows('SELECT id,CONCAT(nombre," (",dni,")") n FROM pacientes ORDER BY nombre'),'id','n',$_GET['pac']??null)?></select></div>
<div><label>Estudio</label><select name=est><?=opts(rows('SELECT * FROM estudios'))?></select></div>
<div><label>Origen</label><select name=origen><option value=consultorio>Consultorios externos<option value=guardia <?=rol()=='guardia'?'selected':''?>>Guardia médica<option value=internacion>Internación</select></div>
<div><label>Indicación</label><input name=motivo></div><button>Registrar orden</button></form><?php }?>
<h2>Órdenes</h2><table><tr><th>Paciente<th>Estudio<th>Origen<th>Estado<th><?=rol()=='tecnico'?'Realización':''?></tr>
<?php $P=opts(rows('SELECT id,nombre FROM profesionales WHERE activo=1'));$I='<option value="">Sin consumo'.opts(rows('SELECT id,nombre FROM insumos'));
foreach(rows("SELECT o.*,pa.nombre pn,e.nombre en FROM ordenes o JOIN pacientes pa ON pa.id=o.paciente_id JOIN estudios e ON e.id=o.estudio_id ORDER BY o.estado IN('realizado','no_realizado'),o.prioridad DESC,o.id DESC LIMIT 60") as $r){
echo '<tr><td>'.h($r['pn']).'<td>'.h($r['en']).'<td>'.$r['origen'].($r['prioridad']?' <span class="tag pri">prioritaria</span>':'').'<td><span class=tag>'.$r['estado'].'</span><td>';
if(rol()=='tecnico'&&$r['estado']=='acreditada')echo '<form method=post><input type=hidden name=a value=real><input type=hidden name=id value='.$r['id'].'><select name=res><option value=realizado>Realizado<option value=no_realizado>No realizado</select><select name=prof>'.$P.'</select><select name=ins>'.$I.'</select><input name=cant type=number value=1 min=1 style=width:60px><input name=motivo placeholder="Motivo si no se realizó"><button>Registrar</button></form>';
if($r['estado']=='realizado')echo '<a href="comprobante.php?id='.$r['id'].'">Comprobante</a>';}?></table><?php bot();
