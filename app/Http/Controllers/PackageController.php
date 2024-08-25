<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function getAll(){
        return response()->json(Package::all()->map(function ($package){
            $thumbnail = $package->package_images->first()->image_path;
            $package->thumbnail = $thumbnail ? asset('storage/images/paket/'.$thumbnail) : null;
            unset(
                $package->package_images,
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

        // If the package exists, you can manipulate it directly
        $package->images = $package->package_images->pluck('image_path')->map(function ($imagePath){
            return asset('/storage/images/paket/'.$imagePath);
        });
        unset($package->package_images);
        unset($package->created_at);
        unset($package->updated_at);

        return response([$package]);
    }
}
