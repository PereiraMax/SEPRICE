<?php require 'config.php';auth('pacientes');
if(post()){$d=array_map('trim',$_POST);
 if(!$d['nombre']||!preg_match('/^\d{7,9}$/',$d['dni'])||!$d['cobertura'])flash('Datos incompletos o inválidos: nombre, DNI (7 a 9 dígitos) y cobertura son obligatorios.',0);
 elseif(one('SELECT id FROM pacientes WHERE dni=?',[$d['dni']]))flash('Ya existe un paciente con ese DNI.',0);
 else{q('INSERT INTO pacientes(nombre,dni,nacimiento,telefono,correo,cobertura)VALUES(?,?,?,?,?,?)',[$d['nombre'],$d['dni'],$d['nacimiento']?:null,$d['telefono'],$d['correo'],$d['cobertura']]);flash('Paciente registrado.');}
 go('pacientes.php');}
top('pacientes');$b=trim($_GET['b']??'');$l=rows('SELECT * FROM pacientes WHERE nombre LIKE ? OR dni LIKE ? ORDER BY id DESC LIMIT 100',["%$b%","%$b%"]);?>
<h2>Registrar paciente</h2><form method=post class=grid>
<div><label>Nombre y apellido</label><input name=nombre required></div><div><label>DNI</label><input name=dni required></div>
<div><label>Fecha de nacimiento</label><input type=date name=nacimiento></div><div><label>Teléfono</label><input name=telefono></div>
<div><label>Correo</label><input type=email name=correo></div><div><label>Cobertura</label><input name=cobertura placeholder="Obra social o Particular" required></div>
<button>Registrar</button></form>
<h2>Pacientes</h2><form class=grid><div><input name=b value="<?=h($b)?>" placeholder="Buscar por nombre o DNI"></div><button>Buscar</button></form><br>
<table><tr><th>Nombre<th>DNI<th>Teléfono<th>Cobertura</tr><?php foreach($l as $r)echo '<tr><td>'.h($r['nombre']).'<td>'.h($r['dni']).'<td>'.h($r['telefono']).'<td>'.h($r['cobertura']);?></table><?php bot();
