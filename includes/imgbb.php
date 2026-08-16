<?php

/**
 * Upload an image file to ImgBB and return its hosted URL, or false on failure.
 */
function upload_image_imgbb(string $imageFilePath)
{
    if (!is_readable($imageFilePath)) {
        return false;
    }

    $imageData = base64_encode(file_get_contents($imageFilePath));
    $url = 'https://api.imgbb.com/1/upload?key=' . urlencode(IMGBB_API_KEY);

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_POSTFIELDS => ['image' => $imageData],
    ]);

    $response = curl_exec($curl);
    $error = curl_error($curl);
    curl_close($curl);

    if ($response === false || $error) {
        return false;
    }

    $result = json_decode($response, true);
    if (isset($result['success']) && $result['success'] === true) {
        return $result['data']['url'];
    }

    return false;
}
