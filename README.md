# SePrise en Vercel + MySQL online
1. Cree una base MySQL online (TiDB Cloud Serverless, Aiven u otra compatible) y una base vacía llamada `seprise`.
2. Suba esta carpeta a un repositorio de GitHub.
3. En vercel.com: Add New > Project > importar el repo. Framework preset: Other.
4. Settings > Environment Variables: cargue las variables de `.env.example` con sus valores reales.
5. Deploy. Abra `https://SU-APP.vercel.app/install.php?token=INSTALL_TOKEN` una sola vez (crea tablas y el usuario admin).
6. Elimine INSTALL_TOKEN y ADMIN_PASS de Vercel y vuelva a desplegar. Ingrese con usuario `admin`.
Para una demo con datos de ejemplo, use INSTALL_DEMO=1 (crea usuarios con claves públicas: no usar en producción).
