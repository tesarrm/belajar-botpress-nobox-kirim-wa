<?php
include "utils/header.php";
include 'nobox/nobox.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

switch ($method) {
    case 'POST':
        $data = $_POST;
        $required_fields = ['nama_ujian', 'nama', 'tgl', 'bnr', 'slh', 'nilai', 'no_hp_ortu'];

        $email = $data['email'];

        // get token
        $query88 = mysqli_query($conn, "SELECT * FROM token_nobox WHERE email = '$email'");
        $nobox_token = mysqli_fetch_assoc($query88);
        $token = $nobox_token['token'];


        // Memeriksa apakah semua field yang diperlukan telah diberikan
        foreach ($required_fields as $field) {
            if (!isset($data[$field])) {
                echo json_encode(array('error' => "Field '$field' harus diberikan"));
                exit;
            }
        }

        // Memasukkan data ke database
        $sql = "INSERT INTO hasil_ujian (nama_ujian, nama, tgl, bnr, slh, nilai)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $data['nama_ujian'], $data['nama'], $data['tgl'], $data['bnr'], $data['slh'], $data['nilai']);
        $result = $stmt->execute();
        $stmt->close();

        // Mengirim email dan pesan WhatsApp
        if ($result && kirimUjianEmail($data['email'], $data) && kirimHasilUjianWA($data['no_hp_ortu'], $data, $token)) {
            echo json_encode(array('message' => 'Data berhasil ditambahkan dan terkirim ke Email dan WhatsApp Ortu'));
        } else {
            echo json_encode(array('message' => 'Data berhasil ditambahkan, tetapi email gagal terkirim'));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array('error' => 'Method not allowed'));
        break;
}

function kirimUjianEmail($email, $data)
{
    // Konten email
    $hasilUjianEmail = "
        <h2>Hasil Ujian</h2>
        <h3>Selamat {$data['nama']} anda telah melakukan ujian berikut:</h3>
        <p>Nama Ujian: {$data['nama_ujian']}</p>
        <p>Tanggal & Jam Ujian: {$data['tgl']}</p>
        <p>Jumlah Benar: {$data['bnr']}</p>
        <p>Jumlah Salah: {$data['slh']}</p>
        <p>Nilai: {$data['nilai']}</p>
    ";

    return kirimEmail($email, 'Hasil Ujian', $hasilUjianEmail);
}

function kirimHasilUjianWA($no_hp_ortu, $data, $token)
{
    // Mendapatkan account ID WhatsApp
    $accountIdWA = getWhatsAppAccountId($token);

    // Mengirim pesan WhatsApp
    $hasilUjianWa = "*Hasil Ujian*
        _Anak anda {$data['nama']}, telah melakukan ujian berikut_
        Nama Ujian: {$data['nama_ujian']}
        Tanggal & Jam Ujian: {$data['tgl']}
        Jumlah Benar: {$data['bnr']}
        Jumblah Salah: {$data['slh']}
        Nilai: {$data['nilai']}";

    return sendWhatsAppMessage($no_hp_ortu, $accountIdWA, $hasilUjianWa, $token);
}

function getWhatsAppAccountId($token)
{
    $nobox = new Nobox($token);
    $listAccount = $nobox->getAccountList();
    $accountData = $listAccount->Data;

    foreach ($accountData as $item) {
        $nama = $item->Name;
        // Memeriksa apakah string 'WhatsApp' ditemukan dalam nama
        if (strpos($nama, 'WhatsApp') !== false) {
            return $item->Id;
        }
    }

    return '';
}

function kirimEmail($email, $subject, $body)
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'tesarrm58@gmail.com';
        $mail->Password = 'cslulirpurnvnnpw';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('tesarrm58@gmail.com', 'Support');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();

        return true;
    } catch (Exception $e) {
        return false;
    }
}

function sendWhatsAppMessage($no_hp_ortu, $accountIdWA, $hasilUjianWa, $token)
{
    try {
        $extId = $no_hp_ortu;
        $channelId = '1';
        $accountIds = $accountIdWA;
        $bodyType = '1';
        $body = $hasilUjianWa;
        $attachment = '[]';

        $nobox = new Nobox($token);
        $tokenResponse = $nobox->sendInboxMessageExt($extId, $channelId, $accountIds, $bodyType, $body, $attachment);

        return true;
    } catch (Exception $e) {
        return false;
    }
}
