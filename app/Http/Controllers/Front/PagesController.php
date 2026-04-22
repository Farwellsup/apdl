<?php

namespace App\Http\Controllers\Front;

use Artisan;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\PageRepository;
use App\Models\Company;

class PagesController extends Controller
{
    //

      protected $pageKey;

  public function __construct()
  {
    $this->homeKey = PageRepository::PAGE_HOMEPAGE;
    
    
  }

    public function index()
  {

    $itemPage = app(PageRepository::class)->getPage($this->homeKey);

    if (!Auth::check()) {

      return view('site.pages.home', [
        'pageItem' => $itemPage,
      ]);

    } else {

      $id = Auth::user()->company_id;

      $company = Company::where('id', $id)->first();
      return view('site.pages.home', [
        'pageItem' => $itemPage,
        'company' => $company,
      ]);
    }
  }


  public function pages(Request $request, $key)
  {

    if (env('APP_DEBUG') === 'false' || env('APP_ENV') === 'production') {

      Artisan::call('view:clear');
      Artisan::call('optimize:clear');
    }

    $itemPage = app(PageRepository::class)->getPage($key);

    if ($key === 'home') {
      return redirect()->route('home');

    } else {

      return view('site.pages.' . $key, [
        'pageItem' => $itemPage,
    
      ]);
    }
  }
}
