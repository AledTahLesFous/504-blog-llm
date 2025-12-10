<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use League\CommonMark\CommonMarkConverter;

class DebunkController extends Controller
{
    public function show($articleId)
    {
        $debunk = DB::table('debunks')->where('article_id', $articleId)->firstOrFail();

        $converter = new CommonMarkConverter();
        $html = $converter->convert($debunk->content);

        return view('debunks.show', [
            'debunk' => $debunk,
            'html' => $html,
        ]);
    }
}
