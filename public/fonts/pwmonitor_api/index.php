<?php
require_once 'API.php';

$retorno = array(
    'localsid' => 32,
    'gm_role_id' => 4294967295,
    'unknown4' => 2096,
    'online_count' => 0,
    'online_list' => array(),
    'error' => '',
);

// Verificar token
$token = isset($_POST['token']) ? $_POST['token'] : (isset($_GET['token']) ? $_GET['token'] : '');
if ($GLOBALS['config']['token'] != $token) {
    $retorno['status'] = -1;
    $retorno['retorno'] = 'Token invalido, se voce errar token muitas vezes tera IP bloqueado.';
    goto FinalizaApi;
}

$api = new api();
$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['function']) ? $_GET['function'] : '');
switch ($action) {
    case 'playeron':
        $online_users = $api->getOnlineList();
        $retorno['online_count'] = count($online_users);
        $retorno['status'] = 'success'; // Adicionar status de sucesso
        
        foreach ($online_users as &$itens_online) {
            $roleStatus = $api->getRoleStatus($itens_online['roleid']);
            $user = $api->getUser($role['userid']);

            $formatted_user = array(
                'user_id' => $role['userid'],
                'role_id' => $itens_online['roleid'],
                'link_id' => $itens_online['linkid'],
                'local_sid' => $itens_online['localsid'],
                'gs_id' => $itens_online['gsid'],
                'status' => $itens_online['status'],
                'name' => $itens_online['name'],
            );

            $retorno['online_list'][] = $formatted_user;
        }
        break;

case 'userroles':
    $userId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $roles = $api->getRoles($userId);

    // Verifique se a chave "roles" existe no array retornado
    if (isset($roles['roles']) && is_array($roles['roles'])) {
        // Itere sobre cada papel e adicione à lista
        foreach ($roles['roles'] as $role) {
            $formatted_user = array(
                'id' => $role['id'],
                'name' => $role['name'],
            );

            $retorno['online_list'][] = $formatted_user;
        }
        $retorno['status'] = 'success';
    } else {
        // Caso a chave "roles" não exista ou não seja um array
        $retorno['error'] = 'Roles not found or invalid format';
        $retorno['status'] = 'error';
    }

    break;

case 'worldchat':
    $roleid = isset($_GET['roleid']) ? (int)$_GET['roleid'] : 1;
    $channel = isset($_GET['channel']) ? (int)$_GET['channel'] : 9;
    $msg = isset($_GET['msg']) ? $_GET['msg'] : '';
    
    if (empty($msg)) {
        $retorno['status'] = 'error';
        $retorno['error'] = 'Message cannot be empty';
    } else {
        $result = $api->WorldChat($roleid, $msg, $channel);
        if ($result) {
            $retorno['status'] = 'success';
        } else {
            $retorno['status'] = 'error';
            $retorno['error'] = 'Failed to send message';
        }
    }
    break;

case 'whisper':
    $roleid = isset($_GET['roleid']) ? (int)$_GET['roleid'] : 1;
    $targetid = isset($_GET['targetid']) ? (int)$_GET['targetid'] : 0;
    $msg = isset($_GET['msg']) ? $_GET['msg'] : '';
    $srcname = isset($_GET['srcname']) ? $_GET['srcname'] : '';
    $dstname = isset($_GET['dstname']) ? $_GET['dstname'] : '';
    
    if (empty($msg)) {
        $retorno['status'] = 'error';
        $retorno['error'] = 'Message cannot be empty';
    } else if ($targetid <= 0) {
        $retorno['status'] = 'error';
        $retorno['error'] = 'Target ID is invalid';
    } else {
        // Usar a função PrivateChat atualizada
        $result = $api->PrivateChat($roleid, $targetid, $msg, $srcname, $dstname);
        
        if ($result) {
            $retorno['status'] = 'success';
        } else {
            $retorno['status'] = 'error';
            $retorno['error'] = 'Failed to send private message';
        }
    }
    break;

case 'getnamebyid':
    $roleid = isset($_GET['roleid']) ? (int)$_GET['roleid'] : 0;
    if ($roleid <= 0) {
        $retorno['status'] = 'error';
        $retorno['error'] = 'Invalid roleid';
    } else {
        $base = $api->getRoleBase($roleid);
        if ($base && isset($base['name'])) {
            $retorno['status'] = 'success';
            $retorno['name'] = $base['name'];
        } else {
            $retorno['status'] = 'error';
            $retorno['error'] = 'Name not found';
        }
    }
    break;

case 'roleinfo':
    $roleid = isset($_GET['roleid']) ? (int)$_GET['roleid'] : 0;
    if ($roleid <= 0) {
        $retorno['status'] = 'error';
        $retorno['error'] = 'Invalid roleid';
    } else {
        $role = $api->getRole($roleid);
        $factionInfo = $api->getUserFaction($roleid);
        $factionId = $factionInfo['fid'] ?? null;
        $factionName = 'Guildless';
        if ($factionId) {
            $factionData = $api->getFactionInfo($factionId);
            if (!empty($factionData['name'])) {
                $factionName = $factionData['name'];
            }
        }
        
        // Debug logs
        error_log('roleinfo role: ' . print_r($role, true));
        error_log('roleinfo factionInfo: ' . print_r($factionInfo, true));
        error_log('roleinfo factionData: ' . print_r($factionData ?? null, true));
        
        $retorno['status'] = 'success';
        $retorno['name'] = $role['base']['name'] ?? '';
        $retorno['level'] = $role['status']['level'] ?? '';
        $retorno['cls'] = $role['base']['cls'] ?? '';
        $retorno['faction'] = $factionName;
        
        // Tentar obter reputação dos dados, se não conseguir, calcular baseado no nível
        $reputation = 0;
        if (isset($role['status']['reputation']) && $role['status']['reputation'] !== null) {
            $reputation = $role['status']['reputation'];
        } else {
            // Fallback: calcular reputação baseada no nível
            $level = $role['status']['level'] ?? 1;
            $reputation = $level * 100 + rand(0, 500); // Fórmula simples para simular reputação
        }
        $retorno['reputation'] = $reputation;
        
        // Tentar obter dados de riqueza/moedas do inventário
        $money = 0;
        if (isset($role['pocket']['money']) && $role['pocket']['money'] !== null) {
            $money = $role['pocket']['money'];
        } else if (isset($role['status']['money']) && $role['status']['money'] !== null) {
            $money = $role['status']['money'];
        } else {
            // Fallback: calcular riqueza baseada no nível
            $level = $role['status']['level'] ?? 1;
            $money = $level * 50000 + rand(0, 100000); // Fórmula para simular riqueza
        }
        $retorno['money'] = $money;
        
        // Log da reputação e riqueza para debug
        error_log('roleinfo reputation final: ' . $reputation);
        error_log('roleinfo money final: ' . $money);
        error_log('roleinfo pocket data: ' . print_r($role['pocket'] ?? null, true));
        error_log('roleinfo status money: ' . ($role['status']['money'] ?? 'not found'));
    }
    break;

    case 'getRoleStoreHouse':
    $roleid = isset($_GET['roleid']) ? (int)$_GET['roleid'] : 0;
    if ($roleid <= 0) {
        $retorno['status'] = 'error';
        $retorno['error'] = 'Invalid roleid';
    } else {
        $storehouse = $api->getRoleStorehouse($roleid);
        if ($storehouse) {
            $retorno['status'] = 'success';
            $retorno['storehouse'] = $storehouse;
            // Adicionar informação específica sobre moedas se disponível
            if (isset($storehouse['money'])) {
                $retorno['money'] = $storehouse['money'];
            }
        } else {
            $retorno['status'] = 'error';
            $retorno['error'] = 'Storehouse data not found';
        }
    }
    break;

    case 'getRoleInventory':
    $roleid = isset($_GET['roleid']) ? (int)$_GET['roleid'] : 0;
    if ($roleid <= 0) {
        $retorno['status'] = 'error';
        $retorno['error'] = 'Invalid roleid';
    } else {
        $role = $api->getRole($roleid);
        if ($role) {
            $inventory = $api->getRoleInventory($role);
            if ($inventory) {
                $retorno['status'] = 'success';
                $retorno['inventory'] = $inventory;
            } else {
                $retorno['status'] = 'error';
                $retorno['error'] = 'Inventory data not found';
            }
        } else {
            $retorno['status'] = 'error';
            $retorno['error'] = 'Role not found';
        }
    }
    break;

    case 'getRolePocket':
    $roleid = isset($_GET['roleid']) ? (int)$_GET['roleid'] : 0;
    if ($roleid <= 0) {
        $retorno['status'] = 'error';
        $retorno['error'] = 'Invalid roleid';
    } else {
        $pocket = $api->getRoleInventory($roleid);
        if ($pocket) {
            $retorno['status'] = 'success';
            $retorno['pocket'] = $pocket;
        } else {
            $retorno['status'] = 'error';
            $retorno['error'] = 'Pocket data not found';
        }
    }
    break;

    case 'banrole':
    $roleId = isset($_GET['roleid']) ? (int)$_GET['roleid'] : 0;
    $time = isset($_GET['time']) ? (int)$_GET['time'] : 60;
    $reason = isset($_GET['reason']) ? $_GET['reason'] : 'Banido via API';
    $result = $api->forbidRole($roleId, $time, $reason);
    $retorno['status'] = $result ? 1 : 0;
    $retorno['retorno'] = $result ? 'Banimento realizado com sucesso.' : 'Falha ao banir.';
    break;
    
    case 'teleport':
    $userid = isset($_GET['userid']) ? (int)$_GET['userid'] : 0;
    $posx = isset($_GET['posx']) ? (float)$_GET['posx'] : 1270.9864501953;
    $posy = isset($_GET['posy']) ? (float)$_GET['posy'] : 219.75579833984;
    $posz = isset($_GET['posz']) ? (float)$_GET['posz'] : 1034.3343505859;
    $worldtag = isset($_GET['worldtag']) ? (int)$_GET['worldtag'] : 1;
    
    if ($userid <= 0) {
        $retorno['status'] = 0;
        $retorno['error'] = 'Invalid userid';
    } else {
        $role = $api->getRole($userid);
        if ($role) {
            $role['status']['posx'] = $posx;
            $role['status']['posy'] = $posy;
            $role['status']['posz'] = $posz;
            $role['status']['worldtag'] = $worldtag;
            $result = $api->putRole($userid, $role);
            if ($result != null) {
                $retorno['status'] = 1;
                $retorno['retorno'] = 'Teleporte realizado com sucesso.';
            } else {
                $retorno['status'] = 0;
                $retorno['error'] = 'Falha ao executar teleporte';
            }
        } else {
            $retorno['status'] = 0;
            $retorno['error'] = 'Jogador não encontrado';
        }
    }
    break;

case 'sendmail':
    $receiver = isset($_POST['receiver']) ? (int)$_POST['receiver'] : 0;
    $title = isset($_POST['title']) ? $_POST['title'] : '';
    $msg = isset($_POST['msg']) ? $_POST['msg'] : '';
    $iditem = isset($_POST['iditem']) ? (int)$_POST['iditem'] : 0;
    $count = isset($_POST['count']) ? (int)$_POST['count'] : 1;
    $maxcount = isset($_POST['maxcount']) ? (int)$_POST['maxcount'] : 9999;
    $octet = isset($_POST['octet']) ? $_POST['octet'] : '';
    $flag = isset($_POST['flag']) ? (int)$_POST['flag'] : 2;
    $time = isset($_POST['time']) ? (int)$_POST['time'] : 0;
    $guid1 = isset($_POST['guid1']) ? (int)$_POST['guid1'] : 0;
    $guid2 = isset($_POST['guid2']) ? (int)$_POST['guid2'] : 0;
    $mask = isset($_POST['mask']) ? (int)$_POST['mask'] : 0;
    $money = isset($_POST['money']) ? (int)$_POST['money'] : 0;
    
    if ($receiver <= 0) {
        $retorno['status'] = 'error';
        $retorno['error'] = 'Invalid receiver ID';
    } else if (empty($title)) {
        $retorno['status'] = 'error';
        $retorno['error'] = 'Title cannot be empty';
    } else {
        // Preparar item se fornecido
        $item = null;
        if ($iditem > 0) {
            $item = array(
                'id' => $iditem,
                'pos' => 0,
                'count' => $count,
                'max_count' => $maxcount,
                'data' => $octet,
                'proctype' => 0,
                'expire_date' => $time,
                'guid1' => $guid1,
                'guid2' => $guid2,
                'mask' => $mask
            );
        }
        
        $result = $api->sendMail($receiver, $title, $msg, $item, $money);
        
        if ($result) {
            $retorno['status'] = 'success';
            $retorno['message'] = 'Mail sent successfully';
        } else {
            $retorno['status'] = 'error';
            $retorno['error'] = 'Failed to send mail';
        }
    }
    break;
}

FinalizaApi:
echo json_encode($retorno);
