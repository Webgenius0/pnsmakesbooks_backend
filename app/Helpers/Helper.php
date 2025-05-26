<?php

namespace App\Helpers;

use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Stichoza\GoogleTranslate\GoogleTranslate;

class Helper
{

    //! File or Image Upload
    public static function fileUpload($file, string $folder, string $name): ?string
    {
        if (!$file->isValid()) {
            return null;
        }

        $imageName = Str::slug($name) . '.' . $file->extension();
        $path = public_path('uploads/' . $folder);
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }
        $file->move($path, $imageName);
        return 'uploads/' . $folder . '/' . $imageName;
    }

    //! File or Image Delete
    public static function fileDelete(string $path): void
    {
        if (file_exists($path)) {
            unlink($path);
        }
    }

    //! Generate Slug
    //! Generate Slug
    public static function makeSlug(string $title, $table): string
    {
        $slug = Str::slug($title);
        while (DB::table($table)->where('slug', $slug)->exists()) {
            $randomString = Str::random(5);
            $slug = Str::slug($title) . '-' . $randomString;
        }
        return $slug;
    }

    //! JSON Response
    public static function jsonResponse(bool $status, string $message, int $code, $data = null, bool $paginate = false, $paginateData = null): JsonResponse
    {
        $response = [
            'status' => $status,
            'message' => $message,
            'code' => $code,
        ];
        if ($paginate && !empty($paginateData)) {
            $response['data'] = $data;
            $response['pagination'] = [
                'current_page' => $paginateData->currentPage(),
                'last_page' => $paginateData->lastPage(),
                'per_page' => $paginateData->perPage(),
                'total' => $paginateData->total(),
                'first_page_url' => $paginateData->url(1),
                'last_page_url' => $paginateData->url($paginateData->lastPage()),
                'next_page_url' => $paginateData->nextPageUrl(),
                'prev_page_url' => $paginateData->previousPageUrl(),
                'from' => $paginateData->firstItem(),
                'to' => $paginateData->lastItem(),
                'path' => $paginateData->path(),
            ];
        } elseif ($paginate && !empty($data)) {
            $response['data'] = $data->items();
            $response['pagination'] = [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                'first_page_url' => $data->url(1),
                'last_page_url' => $data->url($data->lastPage()),
                'next_page_url' => $data->nextPageUrl(),
                'prev_page_url' => $data->previousPageUrl(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem(),
                'path' => $data->path(),
            ];
        } elseif ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    public static function jsonErrorResponse(string $message, int $code = 400, array $errors = []): JsonResponse
    {
        $response = [
            'status' => false,
            'message' => $message,
            'code' => $code,
            'errors' => $errors,
        ];
        return response()->json($response, $code);
    }


    public static function sendNotifyMobile($token, $notifyData): void
    {
        try {
            $SystemSetting = SystemSetting::first();
            if ($SystemSetting) {
                $app_logo = $SystemSetting->logo;
            } else {
                $app_logo = 'logo.png';
            }

            $factory = (new Factory)->withServiceAccount(storage_path('app/private/easy-to-manage-firebase-adminsdk-76x4g-a33d9372d7.json'));

            $messaging = $factory->createMessaging();
            $notification = Notification::create(
                $notifyData['title'],
                Str::limit($notifyData['body'], 100),
                $app_logo
            );
            $message = CloudMessage::withTarget('token', $token)->withNotification($notification);
            $messaging->send($message);
            Log::error("firebase notification success");
        } catch (\Exception $exception) {
            Log::error($exception);
        }
    }



    public static function translateText($text, $lang = 'en')
    {
        try {
            $tr = new GoogleTranslate();
            $tr->setTarget($lang);
            return $tr->translate($text);
        } catch (\Exception $e) {
            return $text;
        }
    }
    public static function translateCached($text, $lang = 'en')
    {
        if (is_null($text)) {
            return ''; // অথবা return $text;
        }

        $key = 'translated_' . md5($text . $lang);
        return cache()->rememberForever($key, function () use ($text, $lang) {
            try {
                $tr = new GoogleTranslate();
                $tr->setTarget($lang);
                return $tr->translate($text);
            } catch (\Exception $e) {
                return $text;
            }
        });
    }
    public static function translateHtmlPreserveTags($html, $lang)
    {
        // Step 1: Null বা empty check
        if (empty($html)) {
            return '';
        }

        try {
            $doc = new \DOMDocument();
            libxml_use_internal_errors(true); // HTML parsing warning ignore

            $doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

            $xpath = new \DOMXPath($doc);

            foreach ($xpath->query('//text()') as $textNode) {
                $text = trim($textNode->nodeValue);
                if ($text !== '') {
                    $translated = self::translateCached($text, $lang);
                    $textNode->nodeValue = $translated;
                }
            }

            // Step 2: Return HTML without <!DOCTYPE>
            $html = $doc->saveHTML($doc->documentElement);
            return preg_replace('/^<!DOCTYPE.+?>/', '', $html);
        } catch (\Exception $e) {
            // Step 3: Fallback on error
            return $html;
        }
    }

}
