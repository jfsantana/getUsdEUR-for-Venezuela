<?php
/****************************
CREADO POR: JESUS SANTANA
DESCRIPCION: Panel de control manual para ejecutar actualizacion de tasas USD/EUR
****************************/

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizador de Tasas - GH Divisa</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            padding: 40px;
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #667eea;
            padding-bottom: 15px;
        }
        .info-box {
            background: #f0f4ff;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin-bottom: 25px;
            border-radius: 4px;
        }
        .info-box p {
            margin: 5px 0;
            color: #555;
            font-size: 14px;
        }
        .button-group {
            text-align: center;
            margin-bottom: 30px;
        }
        button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px 40px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        button:active {
            transform: translateY(0);
        }
        .output-box {
            background: #f5f5f5;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            margin-top: 20px;
            max-height: 500px;
            overflow-y: auto;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.6;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .output-box.success {
            background: #e8f5e9;
            border-color: #4caf50;
            color: #2e7d32;
        }
        .output-box.error {
            background: #ffebee;
            border-color: #f44336;
            color: #c62828;
        }
        .status {
            text-align: center;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .status.processing {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        .status.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .status.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .timestamp {
            text-align: center;
            color: #999;
            font-size: 12px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔄 Actualizador de Tasas - GH Divisa
             <h3> Desarrollado por Jesus Santana </h3>
        </h1>
       
        
        <div class="info-box">
            <p><strong>Descripción:</strong> Ejecuta la actualización de tasas USD/EUR oficiales del Banco Central de Venezuela.</p>
            <p><strong>Fuente:</strong> API oficial (ve.dolarapi.com)</p>
            <p><strong>Empresas afectadas:</strong> INN, SUITE, EVENTO, HYSYCA, HOTELERA, BUENAVENTURA, HOTELERAOLD</p>
            <p><strong>Última ejecución programada:</strong> Según configuración del cron.bat</p>
        </div>

        <div class="button-group">
            <form method="POST" action="">
                <button type="submit" name="ejecutar" value="1">
                    ▶ Ejecutar Actualización Ahora
                </button>
            </form>
        </div>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ejecutar'])) {
            echo '<div class="status processing">⏳ Ejecutando proceso de actualización...</div>';
            
            // Captura la salida del script
            ob_start();
            
            try {
                // Incluye el script de actualización
                include_once('TodosGH.php');
                $output = ob_get_clean();
                
                // Valida que hubo ejecución exitosa
                if (strpos($output, 'CULMINO CON EXITO') !== false) {
                    echo '<div class="status success">✓ Proceso completado exitosamente</div>';
                    echo '<div class="output-box success">' . htmlspecialchars($output) . '</div>';
                } else {
                    echo '<div class="status error">✗ Proceso completado con advertencias</div>';
                    echo '<div class="output-box">' . htmlspecialchars($output) . '</div>';
                }
            } catch (Exception $e) {
                ob_end_clean();
                echo '<div class="status error">✗ Error durante la ejecución</div>';
                echo '<div class="output-box error">' . htmlspecialchars($e->getMessage()) . '</div>';
            }
        }
        ?>

        <div class="timestamp">
            Última carga: <?php echo date('Y-m-d H:i:s'); ?>
        </div>
    </div>
      <h3> Desarrollado por Jesus Santana </h3>
</body>
</html>
