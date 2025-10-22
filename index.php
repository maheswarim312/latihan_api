<?php
$url_api = "http://localhost:8080/latihan_api/api_saya.php";
$data_json = file_get_contents($url_api);
$data_array = json_decode($data_json, true);

if ($data_array && $data_array['status'] == 'ok') {
    $list_pegawai = $data_array['data'];
} else {
    $list_pegawai = array();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pegawai (Full CRUD)</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; margin: 20px; background-color: #f9f9f9; }
        h1, h2 { color: #333; }
        form { background: #fff; border: 1px solid #ddd; border-radius: 8px; padding: 20px; margin-bottom: 20px; max-width: 500px; }
        form div { margin-bottom: 10px; }
        form label { display: block; margin-bottom: 5px; font-weight: 600; }
        form input[type="text"] { width: 95%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        form button { padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; }
        form button[type="submit"] { background-color: #007bff; color: white; }
        form button[type="button"] { background-color: #6c757d; color: white; }
        table { border-collapse: collapse; width: 100%; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .btn-edit, .btn-delete {
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            color: white;
            cursor: pointer;
            margin-right: 5px;
        }
        .btn-edit { background-color: #ffc107; }
        .btn-delete { background-color: #dc3545; }
    </style>
</head>
<body>
    <h1>Manajemen Pegawai</h1>

    <h2>Form Pegawai</h2>
    <form id="form-pegawai">
        <input type="hidden" id="pegawai_id" name="pegawai_id">
        
        <div>
            <label for="nama_pegawai">Nama Pegawai:</label>
            <input type="text" id="nama_pegawai" name="nama_pegawai" required>
        </div>
        <div>
            <label for="nip">NIP:</label>
            <input type="text" id="nip" name="nip" required>
        </div>
        <div>
            <label for="alamat">Alamat:</label>
            <input type="text" id="alamat" name="alamat" required>
        </div>
        <button type="submit">Simpan Data</button>
        <button type="button" id="btn-clear">Batal/Clear</button>
    </form>

    <h2>Daftar Pegawai</h2>
    <table id="tabel-pegawai">
        <thead>
            <tr>
                <th>Nama Pegawai</th>
                <th>NIP</th>
                <th>Alamat</th>
                <th>Aksi</th> </tr>
        </thead>
        <tbody>
            <?php
            if (count($list_pegawai) > 0) {
                foreach ($list_pegawai as $pegawai) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($pegawai['nama_pegawai']) . "</td>";
                    echo "<td>" . htmlspecialchars($pegawai['nip']) . "</td>";
                    echo "<td>" . htmlspecialchars($pegawai['alamat']) . "</td>";
                    echo "<td>
                            <button class='btn-edit' 
                                    data-id='" . $pegawai['id'] . "' 
                                    data-nama='" . htmlspecialchars($pegawai['nama_pegawai'], ENT_QUOTES) . "' 
                                    data-nip='" . htmlspecialchars($pegawai['nip'], ENT_QUOTES) . "' 
                                    data-alamat='" . htmlspecialchars($pegawai['alamat'], ENT_QUOTES) . "'>
                                Edit
                            </button>
                            <button class='btn-delete' data-id='" . $pegawai['id'] . "'>
                                Delete
                            </button>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>Tidak ada data pegawai</td></tr>";
            }
            ?>
        </tbody>
    </table>

<script>
    const API_URL = 'http://localhost:8080/latihan_api/api_saya.php';
    const form = document.getElementById('form-pegawai');
    const inputId = document.getElementById('pegawai_id');
    const inputNama = document.getElementById('nama_pegawai');
    const inputNip = document.getElementById('nip');
    const inputAlamat = document.getElementById('alamat');
    const btnClear = document.getElementById('btn-clear');
    const tabelPegawai = document.getElementById('tabel-pegawai').getElementsByTagName('tbody')[0];

    function clearForm() {
        inputId.value = '';
        inputNama.value = '';
        inputNip.value = '';
        inputAlamat.value = '';
    }

    function refreshPage() {
        location.reload();
    }

    form.addEventListener('submit', async function(event) {
        event.preventDefault();

        const id = inputId.value;
        const data = {
            nama_pegawai: inputNama.value,
            nip: inputNip.value,
            alamat: inputAlamat.value
        };

        let method = 'POST';
        let url = API_URL;

        if (id) {
            method = 'PUT';
            url = `${API_URL}?id=${id}`;
        }

        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.status === 'ok') {
                alert(result.message);
                refreshPage();
            } else {
                alert('Error: ' + result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghubungi API.');
        }
    });

    tabelPegawai.addEventListener('click', async function(event) {
        if (event.target.classList.contains('btn-edit')) {
            const id = event.target.dataset.id;
            const nama = event.target.dataset.nama;
            const nip = event.target.dataset.nip;
            const alamat = event.target.dataset.alamat;

            inputId.value = id;
            inputNama.value = nama;
            inputNip.value = nip;
            inputAlamat.value = alamat;

            window.scrollTo(0, 0);
        }

        if (event.target.classList.contains('btn-delete')) {
            const id = event.target.dataset.id;
            
            if (confirm('Apakah kamu yakin ingin menghapus data ini?')) {
                try {
                    const response = await fetch(`${API_URL}?id=${id}`, {
                        method: 'DELETE'
                    });

                    const result = await response.json();

                    if (result.status === 'ok') {
                        alert(result.message);
                        refreshPage();
                    } else {
                        alert('Error: ' + result.message);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menghubungi API.');
                }
            }
        }
    });

    btnClear.addEventListener('click', clearForm);

</script>

</body>
</html>