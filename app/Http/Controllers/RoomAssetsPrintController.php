<?php

namespace App\Http\Controllers;

use App\Models\RoomAsset;
use App\Models\User;
use Illuminate\Http\Request;

class RoomAssetsPrintController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {

        $email = $request->user;

        $user = User::whereEmail($email)->firstOrFail();
        $recordIds = $request->input('records', []);
        $records = RoomAsset::whereIn('id', $recordIds)->orderBy('room_id', 'asc')->with(['room', 'inventory'])->get();
    //    dd($records);

        return view('RoomAssets.print', ['records' => $records, 'user' => $user]);
    }
}
