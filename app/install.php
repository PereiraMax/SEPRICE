<?php require 'config.php';
$t=getenv('INSTALL_TOKEN');if(!$t||($_GET['token']??'')!==$t)exit('Instalación deshabilitada: defina INSTALL_TOKEN y use install.php?token=SU_TOKEN');
if(!getenv('INSTALL_DEMO')&&!getenv('ADMIN_PASS'))exit('Defina ADMIN_PASS (clave del admin) o INSTALL_DEMO=1.');
$p=db();$I='id INT AUTO_INCREMENT PRIMARY KEY';
foreach(["sesiones(id VARCHAR(128) PRIMARY KEY,datos MEDIUMTEXT,ts INT)","usuarios($I,usuario VARCHAR(40) UNIQUE,clave VARCHAR(255),nombre VARCHAR(80),rol ENUM('admin','recepcion','medico','tecnico','guardia'),intentos INT DEFAULT 0,bloqueado TINYINT DEFAULT 0)",
"pacientes($I,nombre VARCHAR(100),dni VARCHAR(12) UNIQUE,nacimiento DATE NULL,telefono VARCHAR(30),correo VARCHAR(80),cobertura VARCHAR(80))",
"especialidades($I,nombre VARCHAR(60) UNIQUE)","consultorios($I,nombre VARCHAR(60) UNIQUE)",
"profesionales($I,nombre VARCHAR(100),especialidad_id INT NULL,usuario_id INT NULL,honorario DECIMAL(10,2) DEFAULT 0,activo TINYINT DEFAULT 1)",
"agenda($I,profesional_id INT,consultorio_id INT,fecha DATE,hora_ini TIME,hora_fin TIME)",
"turnos($I,paciente_id INT,agenda_id INT,hora TIME,estado ENUM('asignado','cancelado','espera','en_atencion','finalizado','ausente') DEFAULT 'asignado',cobertura_modo VARCHAR(30),hora_llamado DATETIME NULL)",
"atenciones($I,turno_id INT,motivo VARCHAR(255),observaciones TEXT,indicaciones TEXT,fecha DATETIME DEFAULT CURRENT_TIMESTAMP)",
"estudios($I,nombre VARCHAR(80),honorario DECIMAL(10,2) DEFAULT 0)",
"ordenes($I,paciente_id INT,estudio_id INT,origen ENUM('consultorio','guardia','internacion'),prioridad TINYINT DEFAULT 0,estado ENUM('pendiente','acreditada','realizado','no_realizado') DEFAULT 'pendiente',motivo VARCHAR(255),creada DATETIME DEFAULT CURRENT_TIMESTAMP,fecha_real DATETIME NULL,tecnico_id INT NULL,prof_id INT NULL)",
"insumos($I,nombre VARCHAR(80),stock INT,minimo INT)","liquidaciones($I,profesional_id INT,desde DATE,hasta DATE,atenciones INT,estudios INT,total DECIMAL(12,2),creada DATETIME DEFAULT CURRENT_TIMESTAMP)"] as $t)$p->exec("CREATE TABLE IF NOT EXISTS $t");
if(!$p->query('SELECT COUNT(*) FROM usuarios')->fetchColumn()){
 foreach([['admin','admin123','Administración','admin'],['recepcion','recep123','Recepción','recepcion'],['medico','medico123','Dra. Ana Torres','medico'],['tecnico','tecnico123','Técnico de laboratorio','tecnico'],['guardia','guardia123','Guardia médica','guardia']] as $u)$p->prepare('INSERT INTO usuarios(usuario,clave,nombre,rol)VALUES(?,?,?,?)')->execute([$u[0],password_hash($u[1],PASSWORD_DEFAULT),$u[2],$u[3]]);
 if(!getenv('INSTALL_DEMO')){$p->exec("DELETE FROM usuarios WHERE usuario<>'admin'");$p->prepare("UPDATE usuarios SET clave=? WHERE usuario='admin'")->execute([password_hash(getenv('ADMIN_PASS'),PASSWORD_DEFAULT)]);exit('Base lista. Ingrese como admin con la clave ADMIN_PASS y luego elimine INSTALL_TOKEN.');}
 $f=date('Y-m-d');
 foreach(["INSERT INTO especialidades(nombre)VALUES('Clínica médica'),('Cardiología'),('Pediatría')","INSERT INTO consultorios(nombre)VALUES('Consultorio 1'),('Consultorio 2')",
 "INSERT INTO profesionales(nombre,especialidad_id,usuario_id,honorario)VALUES('Dra. Ana Torres',1,3,15000),('Técnico de laboratorio',NULL,4,0)",
 "INSERT INTO estudios(nombre,honorario)VALUES('Análisis de sangre',3000),('Electrocardiograma',5000),('Radiografía',6000)",
 "INSERT INTO insumos(nombre,stock,minimo)VALUES('Jeringas',100,20),('Tubos de ensayo',80,20),('Guantes',200,50),('Placas radiográficas',30,10)",
 "INSERT INTO pacientes(nombre,dni,cobertura)VALUES('Juan Pérez','30111222','OSDE'),('María Gómez','28999888','Particular')",
 "INSERT INTO agenda(profesional_id,consultorio_id,fecha,hora_ini,hora_fin)VALUES(1,1,'$f','08:00','13:00')","INSERT INTO turnos(paciente_id,agenda_id,hora)VALUES(1,1,'09:00')"] as $s)$p->exec($s);}
echo '<p style="font:16px sans-serif;padding:2em">Base <b>seprise</b> lista. <a href="index.php">Ir al login</a><br>Usuarios: admin/admin123 · recepcion/recep123 · medico/medico123 · tecnico/tecnico123 · guardia/guardia123</p>';
