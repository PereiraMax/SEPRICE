<?php require 'config.php';auth('insumos');
if(post()){$d=$_POST;if($d['a']=='alta'&&trim($d['nombre'])){q('INSERT INTO insumos(nombre,stock,minimo)VALUES(?,?,?)',[trim($d['nombre']),(int)$d['stock'],(int)$d['minimo']]);flash('Insumo agregado.');}
 elseif($d['a']=='repo'){q('UPDATE insumos SET stock=stock+? WHERE id=?',[max(0,(int)$d['cant']),$d['id']]);flash('Reposición registrada.');}go('insumos.php');}
top('insumos');?>
<h2>Inventario de insumos</h2><table><tr><th>Insumo<th>Stock<th>Mínimo<th>Reposición</tr>
<?php foreach(rows('SELECT * FROM insumos ORDER BY stock<=minimo DESC,nombre') as $r)echo '<tr'.($r['stock']<=$r['minimo']?' class=low':'').'><td>'.h($r['nombre']).'<td>'.$r['stock'].'<td>'.$r['minimo'].'<td><form method=post><input type=hidden name=a value=repo><input type=hidden name=id value='.$r['id'].'><input type=number name=cant min=1 value=10><button>Reponer</button></form>';?></table>
<h2>Nuevo insumo</h2><form method=post class=grid><input type=hidden name=a value=alta><div><label>Nombre</label><input name=nombre required></div><div><label>Stock</label><input type=number name=stock value=0></div><div><label>Mínimo</label><input type=number name=minimo value=5></div><button>Agregar</button></form><?php bot();
