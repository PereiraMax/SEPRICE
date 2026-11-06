<?php require 'config.php';auth('inicio');top('inicio');
$c=fn($s)=>one($s)['n'];?>
<h2>Bienvenido/a, <?=h($_SESSION['u']['nombre'])?></h2><div class=cards>
<div><b><?=$c("SELECT COUNT(*) n FROM turnos t JOIN agenda a ON a.id=t.agenda_id WHERE a.fecha=CURDATE() AND t.estado<>'cancelado'")?></b>Turnos de hoy</div>
<div><b><?=$c("SELECT COUNT(*) n FROM turnos WHERE estado='espera'")?></b>Pacientes en espera</div>
<div><b><?=$c("SELECT COUNT(*) n FROM ordenes WHERE estado IN('pendiente','acreditada')")?></b>Estudios pendientes</div>
<div><b><?=$c("SELECT COUNT(*) n FROM insumos WHERE stock<=minimo")?></b>Insumos bajo mínimo</div></div><?php bot();
