<?php

namespace App\Http\Middleware;

use App\Helpers\UrlFactory;
use App\Models\Web\Language;
use Closure;

class VerifyLanguageInUrl
{
    /**
     * Verify language code in url
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $url = $request->route('url');

        /** @var UrlFactory $urlFactory */
        $urlFactory = resolve('UrlFactory');
        $correct = $urlFactory->resolveUrlLanguage($url);

        if(!$correct) {
            if ($url === '' || $url === '/') {
                return redirect()->route('homepage', Language::findDefault()->language_code);
            }
            return abort(404);
        }

        return $next($request);
    }
}
