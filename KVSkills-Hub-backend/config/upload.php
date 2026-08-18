<?php
declare(strict_types=1);

function store_document_upload(array $file, array $allowedMimeTypes): array
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Muat naik gagal. Kod: ' . ($file['error'] ?? 'unknown'));
    }
    $maxBytes = max(1, (int)env('MAX_UPLOAD_MB', 10)) * 1024 * 1024;
    if ((int)$file['size'] > $maxBytes) {
        throw new RuntimeException('Fail melebihi had saiz yang dibenarkan.');
    }
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']) ?: '';
    if (!in_array($mime, $allowedMimeTypes, true)) {
        throw new RuntimeException('Jenis fail tidak dibenarkan.');
    }
    $extensionMap = [
        'application/pdf'=>'pdf',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'=>'docx',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'=>'xlsx',
    ];
    $extension = $extensionMap[$mime] ?? 'bin';
    $name = bin2hex(random_bytes(20)) . '.' . $extension;
    $relative = 'uploads/' . date('Y/m') . '/' . $name;
    $absolute = STORAGE_PATH . '/' . $relative;
    if (!is_dir(dirname($absolute)) && !mkdir(dirname($absolute), 0770, true) && !is_dir(dirname($absolute))) {
        throw new RuntimeException('Direktori storan gagal dicipta.');
    }
    if (!move_uploaded_file($file['tmp_name'], $absolute)) {
        throw new RuntimeException('Fail gagal disimpan.');
    }
    return ['relative_path'=>'storage/private/' . $relative,'mime_type'=>$mime,'file_size'=>(int)$file['size'],'checksum_sha256'=>hash_file('sha256',$absolute),'original_filename'=>basename((string)$file['name'])];
}
