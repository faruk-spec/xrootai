<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name',
        'subject',
        'body_html',
        'body_text',
        'available_variables',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'available_variables' => 'array',
        ];
    }

    /**
     * Clear the cache for this template automatically when saved/deleted.
     */
    protected static function booted()
    {
        static::saved(function ($template) {
            Cache::forget("email_template_{$template->slug}");
            Cache::forget("email_template_data_{$template->slug}");
        });

        static::deleted(function ($template) {
            Cache::forget("email_template_{$template->slug}");
            Cache::forget("email_template_data_{$template->slug}");
        });
    }

    /**
     * Fetch and render an active email template with variable substitution.
     */
    public static function render(string $slug, array $data = []): ?array
    {
        $templateData = Cache::rememberForever("email_template_data_{$slug}", function () use ($slug) {
            $template = self::where('slug', $slug)->first();
            if (!$template) {
                return null;
            }
            return [
                'subject' => $template->subject,
                'body_html' => $template->body_html,
                'body_text' => $template->body_text ?? strip_tags($template->body_html),
            ];
        });

        if (!$templateData || !is_array($templateData)) {
            Cache::forget("email_template_data_{$slug}");
            Cache::forget("email_template_{$slug}");
            $template = self::where('slug', $slug)->first();
            if (!$template) {
                return null;
            }
            $templateData = [
                'subject' => $template->subject,
                'body_html' => $template->body_html,
                'body_text' => $template->body_text ?? strip_tags($template->body_html),
            ];
        }

        // Always inject universal default variables
        $universalData = [
            'app_name' => SystemSetting::get('general_chatbot_name', config('app.name', 'XrootAI')),
            'app_url' => config('app.url', 'http://localhost'),
            'current_year' => date('Y'),
        ];

        $mergedData = array_merge($universalData, $data);

        $subject = $templateData['subject'];
        $bodyHtml = $templateData['body_html'];
        $bodyText = $templateData['body_text'];

        foreach ($mergedData as $key => $value) {
            if (is_scalar($value) || (is_object($value) && method_exists($value, '__toString'))) {
                $valStr = (string) $value;
                // Replace double braces {{var}} and single braces {var}
                $subject = str_replace(["{{{$key}}}", "{{ {$key} }}", "{{$key}}"], $valStr, $subject);
                $bodyHtml = str_replace(["{{{$key}}}", "{{ {$key} }}", "{{$key}}"], $valStr, $bodyHtml);
                $bodyText = str_replace(["{{{$key}}}", "{{ {$key} }}", "{{$key}}"], $valStr, $bodyText);
            }
        }

        return [
            'subject' => $subject,
            'body_html' => $bodyHtml,
            'body_text' => $bodyText,
        ];
    }
}
