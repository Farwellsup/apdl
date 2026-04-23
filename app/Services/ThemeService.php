<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class ThemeService
{
    public function generateCssVariables($company)
    {


        return "
        :root {
            --white: #ffffff;
            --black: #000000;
            --primary: {$company->primary_color};
            --secondary: {$company->secondary_color};
            --tertiary: {$company->third_color};
            --primary-text: {$company->primary_text_color};
            --secondary-text: {$company->secondary_text_color};
            --tertiary-text: {$company->third_text_color};
            --menu-color: {$company->menu_color};
            --menu-active-color: {$company->menu_active_color};
            --button-primary: {$company->button_primary_color};
            --button-secondary: {$company->button_secondary_color};
            --button-tertiary: {$company->button_third_color};
            --footer-color: {$company->footer_color};

  
            --logo: url('{$company->image('logo', 'flexible')}');
            --courses-banner: url('{$company->image('courses_banner', 'flexible')}');
            --live-sessions-banner: url('{$company->image('live_sessions_banner', 'flexible')}');
            --training-calendar-banner: url('{$company->image('training_calendar_banner', 'flexible')}');
            --community-banner: url('{$company->image('community_banner', 'flexible')}');
            --training-feed: url('{$company->image('training_feed', 'flexible')}');
            --myprogress-banner: url('{$company->image('myprogress_banner', 'flexible')}');
            --our-progress-banner: url('{$company->image('our_progress_banner', 'flexible')}');
            --enrolled-banner: url('{$company->image('enrolled_banner', 'flexible')}');

  
            --mandatory-svg: url('{$company->image('mandatory_svg', 'default')}');
            --hr-recommended-svg: url('{$company->image('hr_recommended_svg', 'default')}');
            --clock-svg: url('{$company->image('clock_svg', 'default')}');
            --calendar-svg: url('{$company->image('calendar_svg', 'default')}');
            --tick-svg: url('{$company->image('tick_svg', 'default')}');
            --user-svg: url('{$company->image('user_svg', 'default')}');
            --notification-bell-svg: url('{$company->image('notification_bell_svg', 'default')}');
            --not-finished-trophy-svg: url('{$company->image('not_finished_trophy_svg', 'default')}');
            --finished-trophy-svg: url('{$company->image('finished_trophy_svg', 'default')}');
            --like-svg: url('{$company->image('like_svg', 'default')}');
            --comment-svg: url('{$company->image('comment_svg', 'default')}');
            --rocket-svg: url('{$company->image('rocket_svg', 'default')}');
        }
        ";
    }

    public function generateThemeFile($company)
    {
        $timestamp = $company->theme_updated_at->timestamp ?? now()->timestamp;
        $filename = "themes/company-{$company->id}-{$timestamp}.css";
        $fullPath = public_path($filename);

        // Ensure directory exists
        if (!file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        file_put_contents($fullPath, $this->generateCssVariables($company));

        return $filename;
    }

    public function getCachedTheme($company)
    {
        $key = "theme_{$company->id}_{$company->theme_updated_at->timestamp}";
        return Cache::rememberForever($key, function () use ($company) {
            return $this->generateThemeFile($company);
        });
    }
}
