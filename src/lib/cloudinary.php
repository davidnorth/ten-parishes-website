<?php

function cloudinary_url(string $publicId, string $transform = 'w_800,c_limit'): string {
    return 'https://res.cloudinary.com/' . CLOUDINARY_CLOUD_NAME . '/image/upload/' . $transform . '/' . $publicId;
}

function cloudinary_image(string $publicId, int $width, int $height, string $alt = ''): string {
    $url = cloudinary_url($publicId, "w_{$width},h_{$height},c_fill");
    return '<img src="' . htmlspecialchars($url) . '" alt="' . htmlspecialchars($alt) . '" width="' . $width . '" height="' . $height . '">';
}

function cloudinary_upload(string $tmpPath, string $fileName): string {
    $timestamp = time();
    $params = ['timestamp' => $timestamp];
    ksort($params);
    $signature = sha1(http_build_query($params) . CLOUDINARY_API_SECRET);

    $ch = curl_init('https://api.cloudinary.com/v1_1/' . CLOUDINARY_CLOUD_NAME . '/image/upload');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => [
            'file'      => new CURLFile($tmpPath, '', $fileName),
            'api_key'   => CLOUDINARY_API_KEY,
            'timestamp' => $timestamp,
            'signature' => $signature,
        ],
    ]);
    $result = json_decode(curl_exec($ch), true);
    curl_close($ch);

    if (!isset($result['public_id'])) {
        throw new RuntimeException($result['error']['message'] ?? 'Cloudinary upload failed');
    }

    return $result['public_id'];
}
