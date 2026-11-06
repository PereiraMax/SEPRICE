<?php require 'config.php';auth('estudios');
$o=one("SELECT o.*,pa.nombre pn,pa.dni,e.nombre en,u.nombre tn FROM ordenes o JOIN pacientes pa ON pa.id=o.paciente_id JOIN estudios e ON e.id=o.estudio_id LEFT JOIN usuarios u ON u.id=o.tecnico_id WHERE o.id=? AND o.estado='realizado'",[$_GET['id']??0]);if(!$o)exit('Comprobante inexistente.');top('estudios');?>
<h2>Comprobante de estudio N.º <?=str_pad($o['id'],6,'0',STR_PAD_LEFT)?></h2>
<table><tr><th>Paciente<td><?=h($o['pn'])?> (DNI <?=h($o['dni'])?>)<tr><th>Estudio<td><?=h($o['en'])?><tr><th>Realizado<td><?=$o['fecha_real']?><tr><th>Responsable<td><?=h($o['tn'])?></table><br>
<button class=noprint onclick=print()>Imprimir</button> <a class="btn noprint" href=estudios.php>Volver</a><?php bot();
