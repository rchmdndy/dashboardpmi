<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function getAll(){
        return response()->json(Package::all()->map(function ($package){
            $thumbnail = $package->image;
            $package->thumbnail = $thumbnail ? asset("storage/".$thumbnail) : null;
            unset(
                $package->image,
                $package->created_at,
                $package->updated_at,
                $package->hasLodgeRoom,
                $package->hasMeetingRoom,
                $package->min_person_quantity
            );
            return $package;
        }));
    }

    public function getDetail(Request $request){
        $package = Package::find($request->id);

        if (!$package) {
            return response(['message' => 'Paket tidak ditemukan'], 404);
        }

        $thumbnail = $package->image;

        // If the package exists, you can manipulate it directly
        $package->thumbnail = $thumbnail? asset('storage/'.$thumbnail) : null;
        unset(
            $package->image,
            $package->created_at,
            $package->updated_at
        );
        return response([$package]);
    }
}
