<?php
// INCLUI FUNÇÕES DE ADDONS -----------------------------------------------------------------------
include('addons.class.php');

// VERIFICA SE O USUÁRIO ESTÁ LOGADO --------------------------------------------------------------
session_name('mka');
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['mka_logado']) && !isset($_SESSION['MKA_Logado'])) exit('Acesso negado... <a href="/admin/login.php">Fazer Login</a>');

// Supondo que $Manifest esteja definido em algum lugar antes deste código
$manifestTitle = isset($Manifest->{'name'}) ? $Manifest->{'name'} : '';
$manifestVersion = isset($Manifest->{'version'}) ? $Manifest->{'version'} : '';
?>

<!DOCTYPE html>
<?php
if (isset($_SESSION['MM_Usuario'])) {
    echo '<html lang="pt-BR">'; // Fix versão antiga MK-AUTH
} else {
    echo '<html lang="pt-BR" class="has-navbar-fixed-top">';
}
?>
<html lang="pt-BR">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="utf-8">
    <title>MK - AUTH :: <?= htmlspecialchars($manifestTitle . " - V " . $manifestVersion); ?></title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../../estilos/mk-auth.css">
    <link rel="stylesheet" href="../../estilos/font-awesome.css">
    <script src="../../scripts/jquery.js"></script>
    <script src="../../scripts/mk-auth.js"></script>

    <style type="text/css">
        /* Estilos CSS personalizados */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
            color: #333;
        }

        form,
        .table-container,
        .client-count-container {
            width: 100%;
            margin: 0 auto;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="submit"],
        .clear-button {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
        }

        input[type="submit"] {
            background-color: #4caf50;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        .clear-button {
            background-color: #e74c3c;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        .clear-button:hover {
            background-color: #c0392b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th,
        table td {
            padding: 5px;
            text-align: left;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #0d6cea;
            color: white;
            font-weight: bold;
            text-align: center;
        }

        h1 {
            color: #4caf50;
        }

        .client-count-container {
            text-align: center;
            margin-top: 10px;
        }

        .client-count {
            color: #4caf50;
            font-weight: bold;
        }

        .client-count.blue {
            color: #2196F3;
        }

        .nome_cliente a {
            color: blue;
            text-decoration: none;
            font-weight: bold;
        }

        .nome_cliente a:hover {
            text-decoration: underline;
        }

        .nome_cliente td {
            text-align: center;
        }

        .nome_cliente:nth-child(odd) {
            background-color: #FFFF99;
        }

        /* Estilo para ressaltar letras */
        .highlighted {
            color: #f44336; /* Cor vermelha */
            font-weight: bold;
        }

        .sort-button {
            padding: 1px 10px;
            background-color: #1e7bf3;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .sort-button:hover {
            background-color: #45a049;
        }

        .red-text {
            color: #f44336; /* Cor vermelha */
        }

        .nome_cliente td {
            text-align: center;
            max-width: 260px; /* Defina a largura máxima desejada */
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis; /* Adiciona reticências (...) para indicar que o texto foi cortado */
        }

        .custom-button {
            padding: 1.5px 15px;
            border: 1px solid #ea8b0d;
            background-color: #ea8b0d;
            color: white;
            font-weight: bold;
            cursor: pointer;
            border-radius: 5px;
        }

        .custom-button i {
            margin-right: 5px; /* Espaçamento entre o ícone e o texto */
        }

        /* Estilos ao passar o mouse sobre o botão (hover) */
        .custom-button:hover {
            background-color: #ea8b0d; /* Mantém a mesma cor de fundo ao passar o mouse */
            color: white; /* Mantém a mesma cor do texto ao passar o mouse */
            border-color: #ea8b0d; /* Mantém a mesma cor da borda ao passar o mouse */
        }
    </style>

    <script>
        var sortDirection = 'asc'; // Definir a direção inicial da ordenação como ascendente

        function sortTable(columnIndex) {
            var table, rows, switching, i, x, y, shouldSwitch;
            table = document.querySelector('.table-container table');
            switching = true;
            
            // Loop até que nenhuma troca precise ser feita
            while (switching) {
                switching = false;
                rows = table.rows;
                
                // Loop através de todas as linhas, exceto o cabeçalho
                for (i = 1; i < (rows.length - 1); i++) {
                    shouldSwitch = false;
                    
                    // Obtenha os elementos a serem comparados, com base na coluna especificada
                    x = rows[i].getElementsByTagName('td')[columnIndex];
                    y = rows[i + 1].getElementsByTagName('td')[columnIndex];
                    
                    // Verifique se a direção atual da ordenação é ascendente
                    if (sortDirection === 'asc') {
                        // Compare os elementos, alterando shouldSwitch se necessário
                        if (parseInt(x.getAttribute('data-seconds')) > parseInt(y.getAttribute('data-seconds'))) {
                            shouldSwitch = true;
                            break;
                        }
                    } else {
                        // Se a direção for descendente, faça a comparação inversa
                        if (parseInt(x.getAttribute('data-seconds')) < parseInt(y.getAttribute('data-seconds'))) {
                            shouldSwitch = true;
                            break;
                        }
                    }
                }
                
                if (shouldSwitch) {
                    // Se shouldSwitch for verdadeiro, troque as posições e marque switching como verdadeiro para outro loop
                    rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                    switching = true;
                }
            }
            
            // Alterne a direção da ordenação para o próximo clique
            sortDirection = (sortDirection === 'asc') ? 'desc' : 'asc';
        }

        document.addEventListener("DOMContentLoaded", function () {
            var cells = document.querySelectorAll('.table-container tbody td.plan-name');
            cells.forEach(function (cell) {
                cell.addEventListener('click', function () {
                    var planName = this.innerText;
                    document.getElementById('search').value = planName;
                    document.title = 'Painel: ' + planName;
                    document.forms['searchForm'].submit();
                });
            });

            var calledStationIdCells = document.querySelectorAll('.table-container tbody td.calledstationid');
            calledStationIdCells.forEach(function (cell) {
                cell.addEventListener('click', function () {
                    var calledStationId = this.innerText;
                    document.getElementById('search').value = calledStationId;
                    document.forms['searchForm'].submit();
                });
            });
        });
    </script>

</head>

<body>
    <?php include('../../topo.php'); ?>

    <nav class="breadcrumb has-bullet-separator is-centered" aria-label="breadcrumbs">
        <ul>
            <li><a href="#"> ADDON</a></li>
            <li class="is-active">
                <a href="#" aria-current="page"> <?php echo htmlspecialchars($manifestTitle . " - V " . $manifestVersion); ?> </a>
            </li>
        </ul>
    </nav>

    <?php include('config.php'); ?>

    <?php
    if ($acesso_permitido) {
    // Formulário Atualizado com Funcionalidade de Busca
    ?>
    <form id="searchForm" method="GET">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 10px;">
            <div style="width: 60%; margin-right: 10px;">
                <label for="search" style="font-weight: bold; margin-bottom: 5px;">Buscar Cliente:</label>
                <input type="text" id="search" name="search" placeholder="Digite Nome ou Servidor" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ccc;">
            </div>
            <div style="width: 20%; margin-right: 10px;">
                <label for="status" style="font-weight: bold; margin-bottom: 5px;">Status:</label>
                <select id="status" name="status" style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ccc;">
                    <option value="all" <?php echo (!empty($_GET['status']) && $_GET['status'] == 'all') ? 'selected' : ''; ?>>Todos</option>
                    <option value="online" <?php echo (!empty($_GET['status']) && $_GET['status'] == 'online') ? 'selected' : ''; ?>>Online</option>
                    <option value="offline" <?php echo (!empty($_GET['status']) && $_GET['status'] == 'offline') ? 'selected' : ''; ?>>Offline</option>
                </select>
            </div>
            <div style="display: flex; align-items: flex-end;">
                <input type="submit" value="Buscar" style="padding: 10px; border: 1px solid #4caf50; background-color: #4caf50; color: white; font-weight: bold; cursor: pointer; border-radius: 5px; margin-right: 10px;">
                <button type="button" onclick="clearSearch()" class="clear-button" style="padding: 10px; border: 1px solid #e74c3c; background-color: #e74c3c; color: white; font-weight: bold; cursor: pointer; border-radius: 5px; margin-right: 10px;">Limpar</button>
                <button type="button" onclick="sortTable2(4)" class="clear-button sort-button-2" style="padding: 10px; border: 1px solid #4336f4; background-color: #4336f4; color: white; font-weight: bold; cursor: pointer; border-radius: 5px; margin-right: 10px;">Ordenar</button>
                <?php
                // Verifica se o status é "offline"
                if ($_GET['status'] === 'offline') {
                    // Se for "offline", imprime o trecho de código HTML com o botão personalizado
                    echo '<button type="button" onclick="sortTable(5)" class="clear-button sort-button-3 custom-button"><i class="fas fa-sort"></i> Ordenar</button>';
                }
                ?>
            </div>
        </div>
    </form>

    <script>
        function clearSearch() {
            // Limpa o campo de pesquisa
            document.getElementById('search').value = '';

            // Atualiza o valor do campo de seleção de status para "todos"
            document.getElementById('status').value = 'todos';

            // Submeta o formulário
            document.getElementById('searchForm').submit();
        }
    </script>

    <script>
        var sortDirection2 = 'desc'; // Definir a direção inicial da ordenação como descendente
        var sortColumnIndex2 = 4; // Índice da coluna de data de bloqueio

        // Função para ordenar a tabela
        function sortTable2() {
            var table, rows, switching, i, x, y, shouldSwitch;
            table = document.querySelector('.table-container table');
            switching = true;
            
            while (switching) {
                switching = false;
                rows = table.rows;
                
                for (i = 1; i < (rows.length - 1); i++) {
                    shouldSwitch = false;
                    x = getDateFromString(rows[i].getElementsByTagName('td')[sortColumnIndex2].textContent);
                    y = getDateFromString(rows[i + 1].getElementsByTagName('td')[sortColumnIndex2].textContent);
                    
                    if (sortDirection2 === 'desc') {
                        if (x < y) {
                            shouldSwitch = true;
                            break;
                        }
                    } else {
                        if (x > y) {
                            shouldSwitch = true;
                            break;
                        }
                    }
                }
                
                if (shouldSwitch) {
                    rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                    switching = true;
                }
            }
        }

        // Função auxiliar para converter string de data em objeto Date
        function getDateFromString(dateString) {
            var parts = dateString.split('-');
            var day = parseInt(parts[0], 10);
            var month = parseInt(parts[1], 10) - 1; // Mês é baseado em zero (janeiro é 0)
            var year = parseInt(parts[2], 10);
            return new Date(year, month, day);
        }

        // Adicionando evento de clique ao botão de ordenação
        document.addEventListener("DOMContentLoaded", function() {
            var sortButton2 = document.querySelector('.sort-button-2');
            sortButton2.addEventListener('click', function(event) {
                event.preventDefault();
                sortTable2();
                sortDirection2 = (sortDirection2 === 'desc') ? 'asc' : 'desc';
            });
        });
    </script>

    <div class="table-container">
        <table>
            <thead>
                <tr>                   
                    <th style='text-align: center;'>Adicional</th>
                    <th style='text-align: center;'>Principal</th>
                    <th style='text-align: center;'>Servidor</th>
                    <!--<th style='text-align: center;'>Última Conexão</th>-->
					<th style='text-align: center;'>Tempo Offline</th>    
                    <th style='text-align: center;'>UP</th>
                    <th style='text-align: center;'>Down</th>
                                   
                </tr>
            </thead>
            <tbody>
<?php
$searchCondition = '';
if (!empty($_GET['search'])) {
    $search = mysqli_real_escape_string($link, $_GET['search']);
    $searchCondition = " AND (a.username LIKE '%$search%' OR a.login LIKE '%$search%' OR a.ip LIKE '%$search%' OR r.calledstationid LIKE '%$search%')";
}

$query = "SELECT a.tipo, a.username, a.login, a.ip, a.mac, a.bloqueado, a.uuid_adicional, 
                MAX(r.calledstationid) AS calledstationid, MAX(r.acctstarttime) AS ultima_conexao,
                MAX(r.acctstoptime) AS ultima_desconexao, r.acctinputoctets AS total_input_octets, 
                r.acctoutputoctets AS total_output_octets,
                IFNULL((
                    SELECT IF(r.acctstoptime IS NULL AND r.radacctid IS NOT NULL, 'online', 'offline') 
                    FROM radacct r 
                    WHERE r.username = a.username 
                    ORDER BY r.acctstarttime DESC 
                    LIMIT 1
                ), 'offline') AS status
            FROM sis_adicional a
            LEFT JOIN radacct r ON a.username = r.username AND r.acctstarttime = (SELECT MAX(acctstarttime) FROM radacct WHERE username = a.username)
            WHERE a.bloqueado = 'sim' AND r.acctstarttime IS NOT NULL"; // Filtra registros sem última conexão

$query .= $searchCondition;

if (!empty($_GET['status'])) {
    if ($_GET['status'] == 'online') {
        $query .= " AND (SELECT COUNT(*) FROM radacct r WHERE r.username = a.username AND r.acctstoptime IS NULL) > 0";
    } elseif ($_GET['status'] == 'offline') {
        $query .= " AND (SELECT COUNT(*) FROM radacct r WHERE r.username = a.username AND r.acctstoptime IS NULL) = 0";
    }
}

$query .= " GROUP BY a.tipo, a.username, a.login, a.ip, a.mac, a.bloqueado, a.uuid_adicional
            ORDER BY a.username ASC";

$result = mysqli_query($link, $query);

if ($result) {
    $total_adicionais = 0;

    while ($row = mysqli_fetch_assoc($result)) {
        $total_adicionais++;

        $nomeClienteClass = ($total_adicionais % 2 == 0) ? 'nome_cliente' : 'nome_cliente highlight';

        $total_input = $row['total_input_octets'];
        if ($total_input >= 1024 * 1024) {
            $total_input = round($total_input / (1024 * 1024), 2);
            if ($total_input >= 1000) {
                $total_input = round($total_input / 1000, 2) . ' GB';
            } else {
                $total_input .= ' MB';
            }
        } else {
            $total_input = round($total_input / 1024, 2) . ' KB';
        }

        $total_output = $row['total_output_octets'];
        if ($total_output >= 1024 * 1024) {
            $total_output = round($total_output / (1024 * 1024), 2);
            if ($total_output >= 1000) {
                $total_output = round($total_output / 1000, 2) . ' GB';
            } else {
                $total_output .= ' MB';
            }
        } else {
            $total_output = round($total_output / 1024, 2) . ' KB';
        }

        $ultimaDesconexao = strtotime($row['ultima_conexao']);
        $tempoOffline = time() - $ultimaDesconexao;

        $days = floor($tempoOffline / (60 * 60 * 24));
        $remainingSeconds = $tempoOffline % (60 * 60 * 24);
        $hours = floor($remainingSeconds / (60 * 60));
        $remainingSeconds %= (60 * 60);
        $minutes = floor($remainingSeconds / 60);

        $offlineTimeFormatted = "";
        if ($days > 0) {
            $offlineTimeFormatted .= $days . "D, ";
        }
        $offlineTimeFormatted .= sprintf("%02d:%02d", $hours, $minutes);

        echo "<tr class='$nomeClienteClass'>";
		// Nome Adicional
        echo "<td style='border: 1px solid #ddd; padding: 1px; text-align: center; font-weight: bold; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; width: 220px;'>";
        echo "<a href='../../adicional_alt.hhvm?uuid=" . $row['uuid_adicional'] . "' target='_blank' style='color: #06683e; display: flex; align-items: center;' title='" . $row['nome'] . "'>";
        echo "<img src='img/icon_cliente.png' alt='Cliente' style='margin-right: 5px; width: 25px; height: 25px;'>";
        echo "<span class='red-text'>" . $row['username'] . "</span>";
        echo "</a>";
        echo "</td>";
        
		// Nome Login Principal
        echo "<td style='border: 1px solid #ddd; padding: 1px; text-align: center;' title='Login: " . $row['login'] . "'>";
        echo "<a href='../../relatorios_u.hhvm?login=" . $row['username'] . "' target='_blank' style='display: flex; align-items: center;'>";
        echo "<img src='img/icon_globo.png' alt='Ícone Globo' style='width: 25px; height: 25px;'>";
        echo "<span class='red-text' style='margin: auto;'>";
        echo $row['login'];
        echo "</span>";
        echo "</a>";
        echo "</td>";

        // Servidor
        echo "<td style='border: 1px solid #ddd; padding: 1px; text-align: center; font-weight: bold; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;' class='calledstationid'>";
        echo "<a target='_blank' style='color: #06683e; display: flex; align-items: center;' title='" . $row['calledstationid'] . "'>";
        echo "<img src='img/icon_servidor.png' alt='Servidor' style='margin-right: 5px; width: 25px; height: 25px;'>";
        echo $row['calledstationid'];
        echo "</a>";
        echo "</td>";

        // Mostra Ultima Desconexão
        //echo "<td style='border: 1px solid #ddd; padding: 1px; text-align: center; font-weight: bold; color: green;'>" . htmlspecialchars($row['ultima_conexao']) . "</td>";

		//Tempo Offline	
		$status = $row['status'];
        if ($status == 'online') {
            //echo "<td style='border: 1px solid #ddd; padding: 1px; text-align: center; color: #078910; font-weight: bold;'>" . date('d-m-Y / H:i', strtotime($row['data_bloq'])) . "</td>";
            echo "<td style='border: 1px solid #ddd; padding: 4px; text-align: center; font-weight: bold;'>";

            if ($status == 'online') {
                echo "<img src='img/icon_ativo.png' alt='Cliente Ativo' style='float: left; margin-right: 5px; width: 20px;'>";
                echo "<span style='color: #078910;'>Ativo</span>";
            } else {
                echo "Inativo";
            }

            echo "</td>";
        } else {
            //echo "<td style='border: 1px solid #ddd; padding: 1px; text-align: center; color: #f44336; font-weight: bold;'>" . date('d-m-Y / H:i', strtotime($row['data_bloq'])) . "</td>";
            echo "<td style='border: 1px solid #ddd; padding: 4px; text-align: center; color: #000000; font-weight: bold;' class='highlighted' data-seconds='$tempoOffline'>";
            if ($status == 'offline') {
                echo "<img src='img/icon_bloqueado.png' alt='Cliente Bloqueado' style='float: left; margin-right: 5px; width: 20px;'>";
            }
            echo "$offlineTimeFormatted";
            echo "</td>";
        }

        // Uploud
        echo "<td style='border: 1px solid #ddd; padding: 1px; text-align: center; font-weight: bold; color: #402603;'>";
        echo "<div style='display: flex; align-items: center;'>";
        echo "<img src='img/upload.png' alt='Imagem' width='20' height='20' style='margin-right: 5px;'>";
        echo "<span style='display: inline-block; vertical-align: middle;'>{$total_input}</span>";
        echo "</div>";
        echo "</td>";

        // Download
        echo "<td style='border: 1px solid #ddd; padding: 1px; text-align: center; font-weight: bold; color: #402603;'>";
        echo "<div style='display: flex; align-items: center;'>";
        echo "<img src='img/download.png' alt='Imagem' width='20' height='20' style='margin-right: 5px;'>";
        echo "<span style='display: inline-block; vertical-align: middle;'>{$total_output}</span>";
        echo "</div>";
        echo "</td>";



        echo "</tr>";
    }

    echo "<div style='text-align: center;'>";
    echo "<strong><span style='color:#3688f4;'>Total de adicionais: <span style='font-weight:bold;'>$total_adicionais</span></span></strong>";
    echo "</div>";

} else {
    echo "<tr><td colspan='13'>Erro na consulta: " . mysqli_error($link) . "</td></tr>";
}
?>
            </tbody>
        </table>

    </div>
    <?php
    } else {
        echo "Acesso não permitido!";
    }
    ?>

    <?php include('../../baixo.php'); ?>

    <script src="../../menu.js.php"></script>
    <?php include('../../rodape.php'); ?>
</body>

</html>
