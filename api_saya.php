<?php
include 'koneksi.php';
header('Content-Type: application/json');

function send_json_response($status, $message, $data = null) {
    global $conn;
    $response = ['status' => $status, 'message' => $message];
    if ($data !== null) {
        $response['data'] = $data;
    }
    echo json_encode($response);
    mysqli_close($conn);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $stmt = mysqli_prepare($conn, "SELECT * FROM pegawai WHERE id = ?");
            mysqli_stmt_bind_param($stmt, 'i', $id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $data = mysqli_fetch_assoc($result);

            if ($data) {
                send_json_response('ok', 'Data ditemukan', $data);
            } else {
                send_json_response('error', 'Data tidak ditemukan');
            }
        } else {
            $result = mysqli_query($conn, "SELECT * FROM pegawai");
            $items = array();
            while ($data = mysqli_fetch_assoc($result)) {
                $items[] = $data;
            }
            send_json_response('ok', 'Seluruh data berhasil diambil', $items);
        }
        break;

    case 'POST':
        $json_input = file_get_contents('php://input');
        $data = json_decode($json_input, true);

        if (isset($data['nama_pegawai']) && isset($data['nip']) && isset($data['alamat'])) {
            $nama = $data['nama_pegawai'];
            $nip = $data['nip'];
            $alamat = $data['alamat'];
            $stmt = mysqli_prepare($conn, "INSERT INTO pegawai (nama_pegawai, nip, alamat) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt, 'sss', $nama, $nip, $alamat);
            
            if (mysqli_stmt_execute($stmt)) {
                $new_id = mysqli_insert_id($conn);
                send_json_response('ok', 'Data berhasil ditambahkan', ['id' => $new_id]);
            } else {
                send_json_response('error', 'Gagal menambahkan data');
            }
        } else {
            send_json_response('error', 'Data tidak lengkap');
        }
        break;

    case 'PUT':
        if (!isset($_GET['id'])) {
            send_json_response('error', 'ID pegawai tidak disertakan di URL');
        }
        $id = intval($_GET['id']);

        $json_input = file_get_contents('php://input');
        $data = json_decode($json_input, true);

        if (isset($data['nama_pegawai']) && isset($data['nip']) && isset($data['alamat'])) {
            $nama = $data['nama_pegawai'];
            $nip = $data['nip'];
            $alamat = $data['alamat'];

            $stmt = mysqli_prepare($conn, "UPDATE pegawai SET nama_pegawai = ?, nip = ?, alamat = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, 'sssi', $nama, $nip, $alamat, $id);

            if (mysqli_stmt_execute($stmt)) {
                if (mysqli_stmt_affected_rows($stmt) > 0) {
                    send_json_response('ok', 'Data berhasil di-update');
                } else {
                    send_json_response('error', 'Data tidak berubah atau ID tidak ditemukan');
                }
            } else {
                send_json_response('error', 'Gagal meng-update data');
            }
        } else {
            send_json_response('error', 'Data tidak lengkap');
        }
        break;

    case 'DELETE':
        if (!isset($_GET['id'])) {
            send_json_response('error', 'ID pegawai tidak disertakan di URL');
        }
        $id = intval($_GET['id']);

        $stmt = mysqli_prepare($conn, "DELETE FROM pegawai WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $id);

        if (mysqli_stmt_execute($stmt)) {
            if (mysqli_stmt_affected_rows($stmt) > 0) {
                send_json_response('ok', 'Data berhasil dihapus');
            } else {
                send_json_response('error', 'ID pegawai tidak ditemukan');
            }
        } else {
            send_json_response('error', 'Gagal menghapus data');
        }
        break;

    default:
        header('HTTP/1.1 405 Method Not Allowed');
        send_json_response('error', 'Metode HTTP tidak didukung');
        break;
}
?>