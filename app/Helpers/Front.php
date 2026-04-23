<?php

use App\Models\PlatformSetting;
use App\Models\Company;
use App\Models\Menu;
use Illuminate\Support\Facades\Auth;

if (!function_exists('companyBranding')) {
   function companyBranding()
   {

      if (!Auth::check()) {

         $company = PlatformSetting::where('id', 1)->first();

         return $company->theme_css_path;
      } else {

         $co = Company::where('id', Auth::user()->company_id)->first();
         $company = PlatformSetting::where('id', $co->platform_settings_id)->first();

         return $company->theme_css_path;
      }
   }
}

if (!function_exists('notAuthenticatedMenu')) {

   function notAuthenticatedMenu()
   {
      $menus = Menu::where('menu_type_id', 4)
         ->published()
         ->orderBy('position', 'asc')
         ->get();

      return $menus;
   }
}


if (!function_exists('mainMenu')) {

   function mainMenu()
   {
      $menus = Menu::where('menu_type_id', 1)->orWhere('menu_type_id', 4)
         ->published()
         ->orderBy('position', 'asc')
         ->get();

      return $menus;
   }
}


if (!function_exists('sideMenu')) {

   function sideMenu()
   {
      $menus = Menu::where('menu_type_id', 2)
         ->published()
         ->orderBy('position', 'asc')
         ->get();

      return $menus;
   }
}

if (!function_exists('truncateString')) {

   function truncateString($str, $len)
   {
      $tail = max(0, $len - 10);
      $trunk = substr($str, 0, $tail);
      $trunk .= strrev(preg_replace('~^..+?[\s,:]\b|^...~', '...', strrev(substr($str, $tail, $len - $tail))));
      return $trunk;
   }
}
